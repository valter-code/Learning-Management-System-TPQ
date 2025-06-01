LMS TPQ - Sistem Manajemen Pembelajaran TPQ

Selamat datang di dokumentasi LMS TPQ! Aplikasi ini dirancang untuk membantu pengelolaan kegiatan belajar mengajar, administrasi, dan komunikasi di Taman Pendidikan Al-Qur'an (TPQ).
Daftar Isi

    Fitur Utama

    Peran Pengguna & Fungsinya

        Guest (Pengunjung/Wali Calon Santri)

        Admin

        Akademik (Staf Akademik)

        Pengajar (Ustadz/Ustadzah)

        Santri

    Prasyarat Instalasi

    Langkah-Langkah Instalasi

    Konfigurasi Awal

        File .env

        Migrasi dan Seeder Database

        Symbolic Link untuk Storage

        Konfigurasi Email (SMTP)

    Struktur Panel Filament

    Kontribusi

    Lisensi

1. Fitur Utama

    Manajemen Pengguna Multi-Peran: Admin, Akademik, Pengajar, Santri.

    Pendaftaran Santri Baru: Formulir pendaftaran online dengan proses aktivasi oleh admin/akademik.

    Manajemen Akademik: Pengelolaan Kelas, Pertemuan, Materi, dan Tugas.

    Absensi Harian: Fitur absensi mandiri untuk santri dan pengajar, serta rekap absensi.

    Manajemen Konten Publik: Pengelolaan Pengumuman dan Galeri Kegiatan.

    Manajemen Keuangan: Pencatatan dan penagihan SPP santri.

    Notifikasi Email: Untuk pendaftaran, aktivasi akun, verifikasi email, dan tagihan SPP.

    Panel Administrasi Berbasis Peran: Menggunakan Filament PHP untuk antarmuka admin yang intuitif.

    Halaman Publik: Untuk menampilkan informasi TPQ, pengumuman, dan galeri.

2. Peran Pengguna & Fungsinya
Guest (Pengunjung/Wali Calon Santri)

    Melihat Informasi Publik: Dapat mengakses halaman Beranda, Pengumuman, Galeri Kegiatan, Tentang Kami, dan Hubungi Kami.

    Mendaftar Santri Baru: Mengisi dan mengirimkan formulir pendaftaran online untuk calon santri. Data akan masuk ke sistem dengan status "Pending" dan wali akan menerima email notifikasi awal.

Admin

    Akses Penuh: Memiliki hak akses tertinggi ke semua fitur dan konfigurasi sistem.

    Dashboard: Melihat ringkasan statistik (jumlah pengajar, pendaftar baru, chart pendaftaran).

    Manajemen Pengguna:

        CRUD (Create, Read, Update, Delete) untuk semua peran pengguna (Admin lain, Akademik, Pengajar, Santri aktif).

        Mengatur penugasan kelas untuk Pengajar dan Santri.

    Manajemen Pendaftar Santri Baru:

        Melihat dan memproses pendaftaran santri baru.

        Mengubah status pendaftar (Pending, Diproses, Aktif, Ditolak).

        Melakukan aktivasi santri: membuat akun User, SantriProfile, mengirim email verifikasi dan info akun ke wali, mengubah status pendaftar menjadi "AKTIF".

    Manajemen Akademik (Umum):

        CRUD Kelas.

        CRUD Pertemuan, termasuk pengelolaan Materi dan Tugas di dalamnya.

    Manajemen Konten Website:

        CRUD Pengumuman (termasuk foto, status publikasi).

        CRUD Kegiatan Galeri (termasuk foto sampul, deskripsi, status publikasi) dan mengelola foto-foto di dalamnya.

    Manajemen Keuangan (SPP):

        Membuat (generate) tagihan SPP bulanan untuk semua santri aktif.

        Melihat status pembayaran SPP.

        Mengirim email tagihan SPP ke wali.

        Memperbarui status pembayaran SPP (misalnya, setelah konfirmasi manual).

    Laporan:

        Melihat riwayat absensi semua Pengajar.

        Melihat riwayat absensi semua Santri.

    Edit Profil: Mengubah informasi profil dan password akun Admin.

Akademik (Staf Akademik)

    Akses Terbatas: Memiliki hak akses untuk mengelola aspek akademik dan sebagian pengguna.

    Dashboard: Melihat widget dan chart pendaftar baru.

    Manajemen Pengguna:

        CRUD Staf Pengajar (termasuk mengatur kelas yang diajar).

        Mengelola Pendaftar Santri Baru (fungsi sama seperti Admin, termasuk aktivasi).

    Manajemen Akademik:

        CRUD Kelas.

        CRUD Pertemuan (termasuk Materi dan Tugas).

    Manajemen Konten Website:

        CRUD Pengumuman.

        CRUD Kegiatan Galeri & Fotonya.

    Laporan:

        Melihat riwayat absensi semua Santri.

    Edit Profil: Mengubah informasi profil dan password akun Akademik.

Pengajar (Ustadz/Ustadzah)

    Akses Terbatas: Fokus pada kegiatan belajar mengajar dan manajemen kelasnya.

    Dashboard:

        Melakukan absensi harian pribadi.

        Melihat widget kelas yang diajar.

    Manajemen Pertemuan:

        Mengakses daftar pertemuan untuk kelas yang diajar (terfilter).

        CRUD Pertemuan untuk kelasnya.

        Menambah, mengubah, menghapus Materi dan Tugas di dalam pertemuannya.

    Absensi Santri:

        Melakukan absensi harian untuk santri di kelasnya (terkait dengan sebuah pertemuan).

    Laporan:

        Melihat riwayat absensi santri dari kelas-kelas yang diajarnya (filter per tanggal).

    Edit Profil: Mengubah informasi profil dan password akun Pengajar.

Santri

    Akses Terbatas: Fokus pada aktivitas pembelajaran pribadi.

    Dashboard:

        Melakukan absensi mandiri harian.

        Melihat widget kelas yang diikuti.

    Materi & Tugas Kelas:

        Mengakses halaman "Materi & Tugas Kelas".

        Memfilter konten berdasarkan Kelas dan Pertemuan/Topik.

        Melihat daftar Materi dan Tugas untuk setiap pertemuan.

        Mengakses detail pertemuan (Infolist) untuk melihat detail materi atau mengerjakan tugas.

    Pengerjaan Tugas:

        Melihat detail instruksi tugas.

        Mengunggah file jawaban atau mengisi jawaban teks melalui modal.

        Mengirimkan pengumpulan tugas.

        Mengedit pengumpulan tugas (jika belum dinilai).

    Daftar Tugas Pribadi:

        Melihat semua tugas yang ditugaskan, beserta status pengerjaan dan nilai.

        Memfilter tugas berdasarkan Kelas dan Status Pengumpulan.

    Riwayat Absensi Pribadi:

        Melihat rekap absensi hariannya sendiri (filter per bulan/tahun).

    Edit Profil: Mengubah informasi profil (nama, email, password, avatar) dan melihat info kelasnya (read-only).

3. Prasyarat Instalasi

    PHP >= 8.1

    Composer

    Node.js & NPM (atau Yarn)

    Database (MySQL direkomendasikan)

    Web Server (Nginx atau Apache)

    Ekstensi PHP yang diperlukan oleh Laravel (mbstring, xml, curl, gd, dll.)

4. Langkah-Langkah Instalasi

    Clone Repository (jika sudah ada):

    git clone [URL_REPOSITORY_ANDA] lms-tpq
    cd lms-tpq

    Install Dependencies PHP:

    composer install

    Buat File .env:
    Salin file .env.example menjadi .env:

    cp .env.example .env

    Generate Kunci Aplikasi:

    php artisan key:generate

    Konfigurasi Database di .env:
    Sesuaikan variabel DB_DATABASE, DB_USERNAME, DB_PASSWORD, dll., di file .env Anda.

    Jalankan Migrasi Database:

    php artisan migrate

    Install Dependencies JavaScript:

    npm install

    Compile Aset Frontend:

    npm run dev 

    (atau npm run build untuk produksi)

    Buat Symbolic Link untuk Storage:
    Agar file yang diunggah (seperti foto profil, materi, galeri) bisa diakses publik:

    php artisan storage:link

5. Konfigurasi Awal
File .env

Pastikan Anda telah mengatur variabel penting berikut di file .env:

    `APP_NAME
