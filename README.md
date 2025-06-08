# 📘 Dokumentasi LMS TPQ

Ini adalah dokumentasi teknis untuk aplikasi **LMS TPQ** – sistem manajemen pembelajaran berbasis web yang dirancang khusus untuk mendukung kegiatan administrasi dan pengajaran di lingkungan Taman Pendidikan Al-Qur’an (TPQ).  
Dokumen ini ditujukan bagi tim implementasi dan pengguna internal yang akan mengelola sistem.

---


## 📑 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Peran Pengguna & Fungsinya](#peran-pengguna--fungsinya)
  - [Guest (Pengunjung/Wali Calon Santri)](#guest-pengunjungwali-calon-santri)
  - [Admin](#admin)
  - [Akademik (Staf Akademik)](#akademik-staf-akademik)
  - [Pengajar (Ustadz/Ustadzah)](#pengajar-ustadzustadzah)
  - [Santri](#santri)
- [Prasyarat Instalasi](#prasyarat-instalasi)
- [Langkah-Langkah Instalasi](#langkah-langkah-instalasi)
- [Konfigurasi Awal](#konfigurasi-awal)

---

## 🎯 Fitur Utama

- **Manajemen Pengguna Multi-Peran**: Admin, Akademik, Pengajar, Santri
- **Pendaftaran Santri Baru**: Formulir online dengan proses aktivasi
- **Manajemen Akademik**: Kelas, Pertemuan, Materi, Tugas
- **Absensi Harian**: Santri & Pengajar
- **Manajemen Konten Publik**: Pengumuman, Galeri
- **Manajemen Keuangan**: SPP, Tagihan
- **Notifikasi Email**: Aktivasi akun, verifikasi, tagihan
- **Panel Admin Filament**: Antarmuka modern
- **Halaman Publik**: Informasi TPQ, pengumuman, galeri

---

## 👥 Peran Pengguna & Fungsinya

### Guest (Pengunjung/Wali Calon Santri)

- Melihat informasi publik (Beranda, Pengumuman, Galeri, dll.)
- Mendaftar santri baru via formulir online
- Menerima notifikasi email pendaftaran

### Admin

- Akses penuh ke seluruh sistem
- Statistik dashboard
- CRUD semua pengguna
- Manajemen pendaftar & aktivasi santri
- CRUD kelas, pertemuan, materi, tugas
- Manajemen pengumuman & galeri
- Tagihan SPP & laporan pembayaran
- Riwayat absensi pengajar & santri
- email: admin@tpq.com
- password: password

### Akademik (Staf Akademik)

- Akses terbatas untuk manajemen akademik & pengguna
- Manajemen pendaftaran santri baru
- CRUD kelas, pertemuan, materi, tugas
- Manajemen pengumuman & galeri
- Riwayat absensi santri
- email: akademik1@tpq.com
- password: password

### Pengajar (Ustadz/Ustadzah)

- Fokus pada kelas yang diajar
- Absensi pribadi & absensi santri
- CRUD pertemuan, materi, tugas kelas sendiri
- Riwayat absensi santri dari kelasnya
- email: pengajar1@tpq.com, pengajar2@tpq.com
- password: password

### Santri

- Absensi mandiri
- Akses materi & tugas kelas
- Pengerjaan & pengumpulan tugas
- Daftar tugas pribadi
- Rekap absensi pribadi
- Edit profil pribadi
- email: santri1@tpq.com, santri2@tpq.com
- password: password

---

## ⚙️ Prasyarat Instalasi

- PHP >= 8.1
- Composer
- Node.js & NPM (atau Yarn)
- MySQL (disarankan)
- Web server: Nginx atau Apache
- PHP extensions: `mbstring`, `xml`, `curl`, `gd`, dll.

---

## 🚀 Langkah-Langkah Instalasi

```bash
# 1. Clone repository
git clone [URL_REPOSITORY_ANDA] lms-tpq
cd lms-tpq

# 2. Install dependency PHP
composer install
composer require filament/filament:"^3.3" -W

# 3. Salin .env.example menjadi .env
cp .env.example .env

# 4. Generate key aplikasi
php artisan key:generate

# 5. Konfigurasi database di file .env
# Ubah DB_DATABASE, DB_USERNAME, DB_PASSWORD, dll.
& Ubah MAIL_MAILER, MAIL_SCHEME, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS, MAIL_FROM_NAME sesuai kredensial email anda

# 6. Jalankan migrasi
php artisan migrate

# 7. Install dependency JavaScript
npm install

# 8. Compile aset frontend
npm run dev   # untuk development
npm run build # untuk produksi

# 9. Buat symbolic link storage
php artisan storage:link

# 10. Isi database dengan beberapa table default 
php artisan db:seed

```

## Konfigurasi Awal
```bash

ubah bagian
APP_NAME="ISI NAMA LMS TPQ ANDA"
APP_URL=http://localhost (biasanya diubah ke http://127.0.0.1 jika foto profile user tidak muncul) 

DB_DATABASE=lms_tpq (pastikan sesuai)
DB_USERNAME=root
DB_PASSWORD=root (pastikan sesuai db anda)

(ubah dengan kredensial email anda)
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="emailanda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
