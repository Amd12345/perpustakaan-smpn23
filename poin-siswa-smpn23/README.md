# Sistem Informasi Poin Pelanggaran dan Prestasi Siswa

Aplikasi web untuk SMP Negeri 23 Kota Tangerang Selatan. Dibangun dengan Laravel 12, MySQL, Bootstrap 5, Chart.js, Spatie Permission, DomPDF, Maatwebsite Excel, dan notifikasi WhatsApp Fonnte.

## Fitur

- Dashboard publik (tanpa login) dan dashboard internal per peran.
- Manajemen siswa, kelas, guru, orang tua/wali, tahun ajaran, import/export Excel siswa.
- Kategori pelanggaran dan prestasi serta aturan poin per tahun ajaran.
- Pencatatan pelanggaran dan prestasi dengan bukti foto/dokumen.
- Point Transaction Ledger: semua perubahan poin tercatat (sumber, saldo sebelum/sesudah, petugas).
- Pengurangan poin: persyaratan, bukti penyelesaian, verifikasi, setujui/tolak/revisi.
- Konversi poin prestasi menjadi pengurangan poin pelanggaran.
- Notifikasi WhatsApp ke orang tua/wali utama: ambang pelanggaran, prestasi, persyaratan, pengurangan, penolakan.
- Laporan bulanan otomatis (PDF dan Excel), revisi berriwayat, finalisasi.
- Activity log seluruh aktivitas penting.
- Proteksi role di backend (bukan hanya menu).

## Role

| Role | Akses utama |
|---|---|
| Super Admin | Semua modul, manajemen akun, konfigurasi sistem |
| Admin Sekolah | Data master, kategori, aturan poin, pelanggaran/prestasi, pengurangan, laporan |
| Kepala Sekolah | Dashboard, data siswa/pelanggaran/prestasi, riwayat poin, laporan |
| Guru | Input pelanggaran/prestasi, persyaratan pengurangan, verifikasi, konversi, riwayat poin |

## Akun awal (seed)

```
superadmin@poinsiswa.test / admin12345
admin@poinsiswa.test      / admin12345
kepsek@poinsiswa.test     / admin12345
guru@poinsiswa.test       / admin12345
```

Segera ganti password setelah instalasi.

## Instalasi (Windows + Laragon)

1. Pastikan PHP 8.3+ dan MySQL aktif.
2. Buat database dan user:
   ```sql
   CREATE DATABASE poin_siswa_smpn23 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'poin_app'@'localhost' IDENTIFIED BY 'password_anda';
   GRANT ALL PRIVILEGES ON poin_siswa_smpn23.* TO 'poin_app'@'localhost';
   FLUSH PRIVILEGES;
   ```
3. Salin `.env.example` menjadi `.env`, sesuaikan DB_DATABASE/DB_USERNAME/DB_PASSWORD.
4. Jalankan:
   ```
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --seed
   ```
5. Jalankan server uji:
   ```
   php artisan serve --host=127.0.0.1 --port=8010
   ```

Catatan Windows: jika perintah `composer` gagal, panggil langsung:
```
php C:/ProgramData/ComposerSetup/bin/composer.phar ...
```

## Kredensial Fonnte (WhatsApp)

Kredensial WA wajib disimpan di `.env`, bukan di database/kode:

```
FONTE_API_URL=https://api.fonnte.com
FONTE_API_KEY=token_fonnte_anda
```

Setelah token diisi, jalankan queue worker agar notifikasi terkirim:

```
php artisan queue:work
```

Status pengiriman bisa dipantau di menu `Notifikasi WhatsApp`. Pesan yang gagal bisa dikirim ulang lewat tombol retry.

## Scheduler dan Queue Worker (production)

Laporan bulanan dibuat otomatis pada hari terakhir tiap bulan pukul 23:55 (Asia/Jakarta).

### Scheduler

Tambahkan cron ini di server (Linux):
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Di Windows/Laragon, gunakan Task Scheduler menjalankan tiap menit:
```
php D:\git\poin-siswa-smpn23\artisan schedule:run
```

### Queue worker

Jalankan terus-menerus (supervisor/systemd di Linux):
```
php artisan queue:work --sleep=3 --tries=3
```

Di Windows/Laragon, bisa dijalankan lewat Task Scheduler mode repeat atau command prompt terpisah.

## Format Import Siswa

Kolom CSV/Excel (baris pertama judul):
```
nisn,nama,jenis_kelamin,kelas,tahun_ajaran,tanggal_lahir,alamat,nama_orang_tua,telepon_orang_tua,hubungan
```

Contoh:
```
2026001,Ahmad Fauzi,L,7A,2026/2027,2012-05-01,Jl. Contoh No. 1,Budi Santoso,08123456789,ayah
```

Aturan:
- `jenis_kelamin` hanya `L` atau `P`.
- Kelas dan tahun ajaran sudah harus terdaftar sebelum import.
- Telepon orang tua dinormalisasi ke angka (awalan 0 otomatis jadi 62 saat kirim WA).

## Struktur folder utama

```
app/Http/Controllers     controller web
app/Services             PointService, WhatsAppService, MonthlyReportService, ActivityLogService
app/Jobs                 job queue kirim WhatsApp
app/Models               Eloquent model
database/migrations      struktur tabel
database/seeders         data awal role, setting, kategori, akun
resources/views          Blade view
routes/web/*.php         route per modul
```

## Keamanan

- Semua route module dilindungi middleware `auth`, `active`, dan `role` di backend.
- Validasi server-side di controller.
- Database transaction pada proses ledger dan perubahan penting.
- Activity log tidak dapat diubah lewat aplikasi.
- Kredensial Fonnte hanya di environment.

## Pemeliharaan

Backup database:
```
mysqldump -u poin_app -p poin_siswa_smpn23 > backup.sql
```

Reset data demo ke kondisi awal:
```
php artisan migrate:fresh --seed
```

### Data dummy untuk pengujian

Seeder dummy membuat 500 siswa, 9 kelas, 15 akun guru tambahan, orang tua, pelanggaran, dan prestasi. Notifikasi WA sementara dimatikan selama seeding lalu dipulihkan.
```
php artisan db:seed --class=DummyDataSeeder --force
```
Seeder hanya jalan jika tabel siswa masih kosong. Akun guru dummy: `guru01@poinsiswa.test` sampai `guru15@poinsiswa.test`, password `guru12345`.

Bersihkan cache:
```
php artisan config:clear
php artisan view:clear
php artisan route:clear
```
