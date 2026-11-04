<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    protected $fillable = ['medical_record_no', 'name', 'gender', 'age', 'birth_date', 'marital_status'];

    protected $appends = ['display_name'];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    /**
     * Nama pasien yang ditampilkan secara otomatis dengan prefix 
     * (Tn., Ny., Nn., An., By.) berdasarkan jenis kelamin dan umur.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = trim($this->name);
        $gender = strtoupper(trim($this->gender ?? ''));

        // Menghitung umur (bisa dari kolom age atau birth_date jika ada)
        $years = null;
        $months = null;

        if (!empty($this->age)) {
            // Jika kolom age diisi string (misal: "5 Tahun", "2 Bulan", atau angka)
            if (preg_match('/(\d+)\s*(th|tahun|yr)/i', $this->age, $match)) {
                $years = (int)$match[1];
            } elseif (is_numeric($this->age)) {
                $years = (int)$this->age;
            }
        } elseif ($this->birth_date) {
            // Jika menggunakan tanggal lahir
            $birthDate = Carbon::parse($this->birth_date);
            $years = $birthDate->diffInYears(Carbon::now());
            if ($years === 0) {
                $months = $birthDate->diffInMonths(Carbon::now());
            }
        }

        // Logika penentuan prefix otomatis
        $prefix = '';

        if ($years !== null && $years < 14) {
            // Anak-anak (di bawah 14 tahun) atau Bayi
            if ($years === 0 && $months !== null && $months < 1) {
                $prefix = 'By.'; // Bayi baru lahir
            } else {
                $prefix = 'An.'; // Anak
            }
        } else {
            // Dewasa / Remaja (14 tahun ke atas)
            if ($gender === 'L') {
                $prefix = 'Tn.'; // Tuan
            } elseif ($gender === 'P') {
                // Bisa dibedakan Nn. atau Ny. jika ada status perkawinan, default Ny./Nn.
                $marital = strtolower(trim($this->marital_status ?? ''));
                if ($marital === 'belum menikah' || $marital === 'single' || $years < 20) {
                    $prefix = 'Nn.'; // Nona
                } else {
                    $prefix = 'Ny.'; // Nyonya (atau sesuaikan jika ingin umum Tn/Ny)
                }
            }
        }

        // Jika prefix sudah ada di dalam nama, jangan didobel
        if ($prefix && !preg_match('/^(Tn\.|Ny\.|Nn\.|An\.|By\.)\s+/i', $name)) {
            return "{$prefix} {$name}";
        }

        return $name;
    }

    public function schedules()
    {
        return $this->hasMany(SurgerySchedule::class);
    }
}
