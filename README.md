![Jadwal Operasi dan Display OK — RS Mutiara Aini](docs/banner.svg)

# Sistem Informasi Jadwal Operasi dan Display OK

Aplikasi web untuk mengelola jadwal operasi dan menampilkan informasi kamar operasi Rumah Sakit Mutiara Aini. Identitas dan tujuan ini berasal dari `composer.json` serta controller dan tampilan di dalam `okrsma1.zip`.

**[Instalasi](#instalasi-lokal) · [Menjalankan](#menjalankan) · [Penggunaan](#penggunaan) · [Status](#status-saat-ini)**

| Teknologi | Distribusi source | Verifikasi |
| --- | --- | --- |
| PHP `^8.2` · Laravel `^12.0` · Blade | `okrsma1/` (hasil ekstraksi) | Tinjauan statis; belum diuji berjalan |

## Mulai dari sini

- **Ingin mencoba aplikasi?** Ikuti [kebutuhan instalasi](#kebutuhan-instalasi) dan [instalasi lokal](#instalasi-lokal), lalu [jalankan server](#menjalankan).
- **Ingin mengenal proyek?** Baca [fitur yang ada](#fitur-yang-ada-di-source) dan [struktur aplikasi](#struktur-aplikasi).
- **Ingin melanjutkan pengembangan?** Periksa [pekerjaan yang belum selesai](#yang-masih-perlu-dilengkapi-dan-diverifikasi).

## Status saat ini

Ditinjau pada 5 November 2026. Source aplikasi telah diekstrak ke **[`okrsma1/`](okrsma1/)** agar dapat dibaca langsung di GitHub. Arsip asli `okrsma1.zip` tetap tersedia, dan `docs/` berisi aset dokumentasi. Folder source hasil ekstraksi tidak menyertakan `vendor/`, database `laravel_db`, sesi, cache hasil pemakaian, log, maupun foto profil unggahan lama. Data contoh pasien, staf, dan jadwal klinis di seeder tidak disalin; seeder hasil ekstraksi hanya membuat akun admin awal.

Implementasi sudah memiliki route, controller, model, Blade view, migrasi, dan seeder. Peninjauan dilakukan secara statis; instalasi, migrasi database, dan alur aplikasi belum diuji berjalan. Keberadaan kode tidak berarti seluruh fitur sudah lolos pengujian atau siap produksi. Tidak ditemukan direktori pengujian aplikasi `tests/` di dalam arsip.

## Fitur yang ada di source

- Login/logout dengan username dan password; pembatasan akses admin berdasarkan permission.
- Dashboard serta tambah, ubah, hapus, salin antar tanggal, dan pindahkan waktu jadwal operasi.
- Jadwal memuat pasien, tanggal/jam, OK 1–3, diagnosis, tindakan, operator, anestesi, tim bedah, CITO, catatan, dan status Terjadwal/Selesai/Batal.
- Data master pasien, operator, anestesi, tim bedah, dan profesi.
- Pengelolaan shift dan penugasan petugas Dinas, RR, dan On Call; penyalinan penugasan antar tanggal.
- Cetak jadwal operasi dan ekspor jadwal operasi berformat XML Spreadsheet dengan ekstensi `.xls`.
- Display jadwal dan shift melalui `/display`, dengan pengambilan data setiap 3 detik dari `/display/events`.
- Pengelolaan pengguna/permission, profil dan foto pengguna, serta pesan sapaan.

**Batasan yang ditemukan:** route ekspor shift menunjuk metode `export`, sedangkan `ShiftController` hanya menyediakan `exportCsv`. Ekspor shift belum dapat dianggap berfungsi sebelum ketidaksesuaian ini diperbaiki dan diuji.

## Struktur aplikasi

Direktori [`okrsma1/`](okrsma1/) berisi:

| Lokasi | Isi |
| --- | --- |
| `app/Http/Controllers/` | Logika autentikasi, jadwal, shift, display, dan administrasi |
| `app/Models/` | Model data aplikasi |
| `routes/web.php` | Route halaman dan endpoint data |
| `resources/views/` | Tampilan Blade admin, login, dan display |
| `database/migrations/` | Definisi dan perubahan skema database |
| `database/seeders/DatabaseSeeder.php` | Akun admin awal (tanpa data pasien/staf) |
| `composer.json`, `composer.lock` | Dependensi PHP |
| `public/` | Entry point web dan aset publik |

## Kebutuhan instalasi

- PHP yang memenuhi `^8.2` dan kebutuhan ekstensi pada dependensi Composer.
- Composer; Laravel yang diminta source adalah `^12.0`.
- Database: konfigurasi menyediakan SQLite dan MySQL. Contoh penyiapan di bawah menggunakan MySQL dan ekstensi PHP PDO MySQL. Versi MySQL yang telah diuji belum didokumentasikan.
- Git dan alat ekstraksi ZIP.
- Hak tulis untuk `storage/`, `bootstrap/cache/`, serta `public/uploads/profiles/` jika menggunakan unggahan foto.
- Browser dan akses internet untuk aset Bootstrap/Bootstrap Icons dari CDN pada layout admin.

## Instalasi lokal

Langkah berikut diturunkan dari source dan **belum diverifikasi melalui eksekusi**. Gunakan database lokal baru untuk percobaan.

1. Ambil source yang sudah diekstrak:

   ```bash
   git clone https://github.com/atnanbuyung00-prog/Sistem-display-ok.v1.git
   cd Sistem-display-ok.v1
   cd okrsma1
   ```

2. Buat database MySQL kosong dan akun database lokal. Buat sendiri file `.env` di direktori yang sama dengan `artisan`; arsip tidak menyediakan `.env.example`. Contoh minimum:

   ```dotenv
   APP_NAME="Jadwal Operasi OK"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jadwal_ok_local
   DB_USERNAME=ISI_USER_DATABASE_LOKAL
   DB_PASSWORD=ISI_PASSWORD_DATABASE_LOKAL

   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```

   Sesuaikan nilai database dengan database dan akun yang dibuat. Jangan memasukkan `.env` ke Git.

3. Pasang dependensi dari lockfile dan siapkan aplikasi:

   ```bash
   composer install
   php artisan optimize:clear
   php artisan key:generate
   php artisan migrate
   ```

   Folder hasil ekstraksi tidak membawa `vendor/` dan cache lama; dependensi dipasang melalui Composer. Gunakan database lokal baru. Berkas `laravel_db` yang ada di arsip asli tidak disalin ke folder source.

4. Opsional, hanya pada database percobaan baru:

   ```bash
   php artisan db:seed
   ```

   Seeder hasil ekstraksi hanya membuat akun `admin` dengan password `admin`; data pasien/staf dan jadwal contoh telah dihilangkan. Segera ubah password melalui profil sebelum memberikan akses kepada pihak lain. Seeder hasil ekstraksi menggunakan `firstOrCreate`, sehingga akun admin yang sudah ada tidak ditimpa. Tetap tinjau seeder sebelum menjalankannya pada database berisi data.

## Menjalankan

Dari direktori `okrsma1/`:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Buka:

| URL lokal | Kegunaan |
| --- | --- |
| `http://127.0.0.1:8000/login` | Login admin |
| `http://127.0.0.1:8000/admin` | Dashboard sesuai permission |
| `http://127.0.0.1:8000/display` | Layar jadwal dan shift |
| `http://127.0.0.1:8000/display?date=2026-11-05` | Contoh display untuk tanggal tertentu |

Server bawaan ini untuk pengembangan lokal. Konfigurasi deployment produksi belum tersedia sebagai panduan teruji; document root server web harus diarahkan ke `public/`.

## Penggunaan

1. Login dengan akun yang sudah dibuat. Akun awal di atas tersedia hanya setelah seeder dijalankan.
2. Lengkapi master operator, anestesi, profesi, dan tim bedah sesuai kebutuhan.
3. Masukkan jadwal operasi: pilih tanggal, isi pasien, jam, ruang OK, tindakan, serta tenaga yang bertugas. Gunakan pengelolaan jadwal untuk mengubah, menghapus, atau menyalin data.
4. Atur shift dan petugas untuk tanggal yang dipilih.
5. Buka `/display` pada browser layar informasi; tanggal bawaan menggunakan Asia/Jakarta. Tinjau perubahan setelah data disimpan.
6. Gunakan halaman jadwal untuk cetak atau ekspor operasi. Ekspor shift masih memiliki batasan yang disebutkan di atas.

## Yang masih perlu dilengkapi dan diverifikasi

- Source telah diekstrak ke Git. Tambahkan `.env.example` yang aman.
- Pastikan versi PHP, ekstensi, database, dan prosedur instalasi melalui uji pada lingkungan bersih.
- Perbaiki dan uji route ekspor shift. Kode penugasan RR juga menjalankan perubahan kolom khusus MySQL dengan pengecualian yang diabaikan; kompatibilitas SQLite dan hak perubahan skema perlu diuji.
- Tambahkan pengujian autentikasi/permission, CRUD jadwal, shift, display, cetak, dan ekspor.
- Arsip memuat sesi, cache tampilan, foto unggahan, dan berkas database. Tinjau dan bersihkan artefak tersebut sebelum distribusi source berikutnya.
- Route `/display` dan `/display/events` tidak memakai middleware login; endpoint mengembalikan jadwal beserta relasi pasien. Pembatasan akses dan data yang layak ditampilkan harus ditetapkan sebelum menggunakan data pasien nyata.
- Dokumentasikan deployment, backup/restore, lisensi, pengelola proyek, serta status rilis. Hal-hal ini belum dapat ditetapkan dari isi yang ditinjau.
- Hubungan dengan repositori [Sistem-jadwal-OK](https://github.com/atnanbuyung00-prog/Sistem-jadwal-OK) belum dijelaskan. Jangan menganggap source kedua repositori sama.
