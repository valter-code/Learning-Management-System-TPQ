# 📘 Dokumentasi LMS TPQ

Ini adalah dokumentasi teknis untuk aplikasi **LMS TPQ** – sistem manajemen pembelajaran berbasis web yang dirancang khusus untuk mendukung kegiatan administrasi dan pengajaran di lingkungan Taman Pendidikan Al-Qur’an (TPQ).  
Dokumen ini ditujukan bagi tim implementasi dan pengguna internal yang akan mengelola sistem.

---

## 📑 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Peran Pengguna & Fungsinya](#peran-pengguna--fungsinya)
- [Prasyarat Instalasi](#prasyarat-instalasi)
- [Langkah-Langkah Instalasi](#langkah-langkah-instalasi)
- [Konfigurasi Awal](#konfigurasi-awal)
- [Struktur Panel Filament](#struktur-panel-filament)
- [Lisensi dan Pemakaian](#lisensi-dan-pemakaian)

---

## 🎯 Fitur Utama

- Manajemen pengguna multi-peran: Admin, Akademik, Pengajar, Santri
- Formulir pendaftaran santri online
- Manajemen kelas, pertemuan, materi & tugas
- Absensi harian santri dan pengajar
- Pengumuman & galeri kegiatan publik
- Tagihan & pelaporan pembayaran SPP
- Notifikasi email (aktivasi, verifikasi, tagihan)
- Panel admin berbasis Filament PHP
- Halaman publik untuk informasi TPQ

---

## 👥 Peran Pengguna & Fungsinya

Silakan lihat penjelasan per peran:
- [Guest (Pengunjung/Wali)](#guest-pengunjungwali-calon-santri)
- [Admin](#admin)
- [Akademik](#akademik-staf-akademik)
- [Pengajar](#pengajar-ustadzustadzah)
- [Santri](#santri)

<!-- Detail peran seperti sebelumnya tetap digunakan di sini -->

---

## ⚙️ Prasyarat Instalasi

- PHP >= 8.1
- Composer
- Node.js & NPM (atau Yarn)
- MySQL (direkomendasikan)
- Web Server: Nginx atau Apache
- Ekstensi PHP Laravel: `mbstring`, `xml`, `curl`, `gd`, dll.

---

## 🚀 Langkah-Langkah Instalasi

```bash
# 1. Clone repository
git clone [URL_REPOSITORY_ANDA] lms-tpq
cd lms-tpq

# 2. Install dependency PHP
composer install

# 3. Salin file .env
cp .env.example .env

# 4. Generate key Laravel
php artisan key:generate

# 5. Edit konfigurasi database di .env
# Ubah DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai kebutuhan

# 6. Jalankan migrasi database
php artisan migrate

# 7. Install dependency frontend
npm install

# 8. Compile aset frontend
npm run dev   # untuk pengembangan
npm run build # untuk produksi

# 9. Buat symbolic link ke folder storage publik
php artisan storage:link
