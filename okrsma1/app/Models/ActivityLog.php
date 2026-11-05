<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_username',
        'action',
        'module',
        'record_id',
        'patient_name',
        'operation_date',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'operation_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an activity log entry.
     *
     * @param string $action 'INPUT' | 'UBAH' | 'HAPUS' | 'SALIN' | 'RESTORE' | 'BACKUP'
     * @param string $module 'Jadwal Operasi' | 'Pasien' | 'Shift' | 'User' | 'Data Master' | 'Database'
     * @param string $description Human-readable message
     * @param array $extra Optional data (record_id, patient_name, operation_date, old_values, new_values)
     * @return self
     */
    public static function record(string $action, string $module, string $description, array $extra = []): self
    {
        $user = auth()->user();

        $data = [
            'user_id'        => $user?->id,
            'user_name'      => $user?->name ?? 'Sistem / Anonim',
            'user_username'  => $user?->username ?? 'system',
            'action'         => strtoupper(trim($action)),
            'module'         => $module,
            'description'    => $description,
            'record_id'      => $extra['record_id'] ?? null,
            'patient_name'   => $extra['patient_name'] ?? null,
            'operation_date' => $extra['operation_date'] ?? null,
            'old_values'     => $extra['old_values'] ?? null,
            'new_values'     => $extra['new_values'] ?? null,
            'ip_address'     => request()->ip(),
            'user_agent'     => substr((string) request()->userAgent(), 0, 500),
        ];

        return self::create($data);
    }
}
