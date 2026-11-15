# Perpustakaan SMPN 23

Aplikasi web perpustakaan sekolah berbasis Laravel 11 + MySQL. Semua fitur
dijalankan lewat browser (versi desktop NativePHP/Electron sudah dibuang).

## Fitur

- **Autentikasi** — halaman login, semua menu diproteksi, dibatasi 5 percobaan/menit.
- **Pengaturan Akun** — ganti username, nama, email, dan password (butuh password saat ini).
- **Dashboard** — statistik buku dipinjam & total stok, pencarian buku.
- **Data Buku** — tambah, edit, hapus, cari, import Excel (`.xlsx`/`.csv`), export CSV.
- **Peminjaman** — catat peminjaman (stok otomatis berkurang), pengembalian
  (stok kembali + denda Rp 5.000/hari setelah lewat 7 hari), export per rentang tanggal.

## Akun bawaan

| Username | Password |
|----------|----------|
| `admin`  | `admin123` |

Ganti segera lewat menu ⚙️ (Pengaturan Akun) setelah login pertama.

## Kebutuhan

- PHP 8.2+ (ekstensi: `pdo_mysql`, `mbstring`, `zip`, `xml`, `gd`)
- Composer
- MySQL 5.7+/8.x
- Node.js 18+ & npm (hanya untuk rebuild aset Vite; tampilan memakai Bootstrap CDN)

## Instalasi

```bash
# 1. Buat database
mysql -u root -e "CREATE DATABASE perpustakaan_smpn23 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Pasang dependensi
composer install
npm install --legacy-peer-deps   # lihat Catatan di bawah

# 3. Konfigurasi
cp .env.example .env
php artisan key:generate

# 4. Migrasi + akun admin
php artisan migrate
php artisan db:seed

# 5. Jalankan
php artisan serve
```

Buka http://127.0.0.1:8000 lalu masuk dengan `admin` / `admin123`.

Kredensial admin bisa diubah lewat `ADMIN_USERNAME` dan `ADMIN_PASSWORD` di `.env`
(berlaku saat `db:seed` membuat akun pertama).

## Konfigurasi

- Database: MySQL `perpustakaan_smpn23` (host `127.0.0.1`, port `3306`).
- Import Excel wajib punya header: `judul`, `penerbit`, `tahun_terbit`, `stok`.
- Buku yang di-import dengan judul sama akan **menambah stok**, bukan membuat duplikat.
- `APP_TIMEZONE=Asia/Jakarta` — dipakai perhitungan denda harian.


