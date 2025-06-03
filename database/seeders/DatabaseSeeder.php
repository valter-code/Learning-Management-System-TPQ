<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Spp;
use App\Models\Foto;
use App\Models\User;
use App\Models\Kelas;
use App\Enums\UserRole;
use App\Models\Setting;
use App\Enums\StatusSpp;
use App\Models\Pertemuan;
use App\Models\Pengumuman;
use Illuminate\Support\Str;
use App\Enums\StatusAbsensi;
use App\Models\AbsenPengajar;
use App\Models\AbsensiSantri;
use App\Models\SantriProfile;
use App\Models\KegiatanGaleri;
use App\Enums\PengumumanStatus;
use App\Models\PendaftarSantri;
use App\Models\PertemuanMateri;
use Illuminate\Database\Seeder;
use App\Enums\StatusPertemuanEnum;
use App\Models\PertemuanTugasItem;
use App\Enums\StatusPublikasiGaleri;
use Illuminate\Support\Facades\Hash;
use App\Enums\StatusPendaftaranSantri;
use App\Enums\StatusPublikasi; // Untuk Galeri
use Illuminate\Support\Facades\Storage; // Untuk path file

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nonaktifkan event model untuk seeding massal (opsional, bisa mempercepat)
        // Model::unguard();

        // Buat direktori storage jika belum ada (untuk contoh path foto)
        if (!Storage::disk('public')->exists('pengumuman-fotos')) {
            Storage::disk('public')->makeDirectory('pengumuman-fotos');
        }
        if (!Storage::disk('public')->exists('galeri-sampul')) {
            Storage::disk('public')->makeDirectory('galeri-sampul');
        }
        if (!Storage::disk('public')->exists('galeri/kegiatan-ramadhan')) { // Contoh subfolder
            Storage::disk('public')->makeDirectory('galeri/kegiatan-ramadhan');
        }
        if (!Storage::disk('public')->exists('avatars/admins')) {
            Storage::disk('public')->makeDirectory('avatars/admins');
        }
        if (!Storage::disk('public')->exists('avatars/akademik')) {
            Storage::disk('public')->makeDirectory('avatars/akademik');
        }
        if (!Storage::disk('public')->exists('avatars/pengajar')) {
            Storage::disk('public')->makeDirectory('avatars/pengajar');
        }
        if (!Storage::disk('public')->exists('avatars/santri')) {
            Storage::disk('public')->makeDirectory('avatars/santri');
        }


        // 1. Buat Akun Pengguna (Admin, Akademik, Pengajar, Santri)
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
            'avatar_url' => 'avatars/admins/admin.png', // Contoh path
        ]);

        $akademik1 = User::create([
            'name' => 'Staf Akademik 1',
            'email' => 'akademik1@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::AKADEMIK,
            'email_verified_at' => now(),
            'avatar_url' => 'avatars/akademik/akademik1.png',
        ]);

        $pengajar1 = User::create([
            'name' => 'Ustadz Ahmad',
            'email' => 'pengajar1@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PENGAJAR,
            'email_verified_at' => now(),
            'avatar_url' => 'avatars/pengajar/pengajar1.png',
        ]);
        $pengajar2 = User::create([
            'name' => 'Ustadzah Fatimah',
            'email' => 'pengajar2@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PENGAJAR,
            'email_verified_at' => now(),
            'avatar_url' => 'avatars/pengajar/pengajar2.png',
        ]);

        // 2. Buat Kelas
        $kelasA = Kelas::create(['nama_kelas' => 'Kelas Alif', 'deskripsi' => 'Kelas dasar untuk pemula', 'wali_kelas_id' => $pengajar1->id]);
        $kelasB = Kelas::create(['nama_kelas' => 'Kelas Ba', 'deskripsi' => 'Kelas lanjutan setelah Alif', 'wali_kelas_id' => $pengajar2->id]);
        $kelasC = Kelas::create(['nama_kelas' => 'Kelas Tahsin', 'deskripsi' => 'Kelas perbaikan bacaan', 'wali_kelas_id' => $pengajar1->id]);

        // Hubungkan Pengajar dengan Kelas (Many to Many)
        $pengajar1->mengajarDiKelas()->attach([$kelasA->id, $kelasC->id]);
        $pengajar2->mengajarDiKelas()->attach([$kelasB->id]);

        // 3. Buat Akun Santri & Profil & Hubungkan ke Kelas
        for ($i = 1; $i <= 5; $i++) {
            $santri = User::create([
                'name' => 'Santri Ke-' . $i,
                'email' => 'santri' . $i . '@tpq.com',
                'password' => Hash::make('password'),
                'role' => UserRole::SANTRI,
                'email_verified_at' => now(),
                'avatar_url' => 'avatars/santri/santri'.$i.'.png',
            ]);

            SantriProfile::create([
                'user_id' => $santri->id,
                'nama_wali' => 'Wali Santri ' . $i,
                'nomor_telepon_wali' => '08123456789' . $i,
                'email_wali' => 'wali.santri' . $i . '@example.com', // Email wali bisa sama dengan email login santri atau beda
                'alamat' => 'Alamat Santri ' . $i,
                'tanggal_lahir' => Carbon::now()->subYears(7 + $i)->subMonths($i)->format('Y-m-d'),
            ]);

            // Masukkan santri ke kelas (contoh: santri 1-3 ke kelas A, santri 4-5 ke kelas B)
            if ($i <= 3) {
                $santri->kelasYangDiikuti()->attach($kelasA->id);
            } else {
                $santri->kelasYangDiikuti()->attach($kelasB->id);
            }
            // Santri 1 juga ikut kelas C
            if ($i === 1) {
                 $santri->kelasYangDiikuti()->attach($kelasC->id);
            }
        }
        $santri1 = User::where('email', 'santri1@tpq.com')->first();
        $santri4 = User::where('email', 'santri4@tpq.com')->first();


        // 4. Buat Pertemuan, Materi, dan Tugas (oleh Pengajar)
        $pertemuan1KelasA = Pertemuan::create([
            'kelas_id' => $kelasA->id,
            'user_id' => $pengajar1->id, // Dibuat oleh Ustadz Ahmad
            'judul_pertemuan' => 'Pengenalan Huruf Hijaiyah',
            'deskripsi_pertemuan' => 'Belajar mengenal bentuk dan nama huruf Alif sampai Ya.',
            'tanggal_pertemuan' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'waktu_mulai' => '08:00:00',
            'status_pertemuan' => StatusPertemuanEnum::DIJADWALKAN,
        ]);
        PertemuanMateri::create(['pertemuan_id' => $pertemuan1KelasA->id, 'judul_materi' => 'Video Huruf Alif-Ba-Ta', 'tipe_materi' => 'link', 'url_link_materi' => 'https://youtube.com/contoh']);
        PertemuanMateri::create(['pertemuan_id' => $pertemuan1KelasA->id, 'judul_materi' => 'Modul PDF Huruf', 'tipe_materi' => 'file', 'path_file_materi' => 'materi-files/modul1.pdf']);
        PertemuanTugasItem::create(['pertemuan_id' => $pertemuan1KelasA->id, 'judul_tugas' => 'Latihan Menulis Alif-Ya', 'deskripsi_tugas' => 'Tulis di buku dan kumpulkan fotonya.', 'deadline_tugas' => Carbon::now()->addDays(3)]);

        $pertemuan2KelasA = Pertemuan::create([
            'kelas_id' => $kelasA->id,
            'user_id' => $pengajar1->id,
            'judul_pertemuan' => 'Harakat Dasar (Fathah, Kasrah, Dhommah)',
            'deskripsi_pertemuan' => 'Mengenal tanda baca dasar.',
            'tanggal_pertemuan' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '08:00:00',
            'status_pertemuan' => StatusPertemuanEnum::DIJADWALKAN,
        ]);
        PertemuanTugasItem::create(['pertemuan_id' => $pertemuan2KelasA->id, 'judul_tugas' => 'Latihan Membaca dengan Harakat', 'deskripsi_tugas' => 'Rekam suara dan kirim.']);


        // 5. Buat Absensi Pengajar
        AbsenPengajar::create(['pengajar_id' => $pengajar1->id, 'tanggal' => Carbon::now()->subDays(1)->format('Y-m-d'), 'status' => StatusAbsensi::MASUK, 'waktu_masuk' => '07:55:00']);
        AbsenPengajar::create(['pengajar_id' => $pengajar2->id, 'tanggal' => Carbon::now()->subDays(1)->format('Y-m-d'), 'status' => StatusAbsensi::MASUK, 'waktu_masuk' => '07:50:00']);
        AbsenPengajar::create(['pengajar_id' => $pengajar1->id, 'tanggal' => Carbon::now()->subDays(2)->format('Y-m-d'), 'status' => StatusAbsensi::IZIN, 'keterangan' => 'Acara keluarga']);

        // 6. Buat Absensi Santri (Harian)
        if ($santri1) {
            AbsensiSantri::create(['santri_id' => $santri1->id, 'tanggal_absensi' => Carbon::now()->subDays(1)->format('Y-m-d'), 'status_kehadiran' => StatusAbsensi::MASUK]);
            AbsensiSantri::create(['santri_id' => $santri1->id, 'tanggal_absensi' => Carbon::now()->subDays(2)->format('Y-m-d'), 'status_kehadiran' => StatusAbsensi::SAKIT, 'keterangan' => 'Demam']);
        }
        if ($santri4) {
            AbsensiSantri::create(['santri_id' => $santri4->id, 'tanggal_absensi' => Carbon::now()->subDays(1)->format('Y-m-d'), 'status_kehadiran' => StatusAbsensi::MASUK]);
        }


        // 7. Buat Pengumuman (oleh Admin atau Akademik)
        Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Libur Idul Adha 2025',
            'slug' => Str::slug('Pengumuman Libur Idul Adha 2025'),
            'konten' => '<p>Diberitahukan kepada seluruh santri dan wali santri bahwa kegiatan belajar mengajar TPQ akan diliburkan dalam rangka Idul Adha 1446 H.</p>',
            'foto' => 'pengumuman-fotos/libur-idul-adha.jpg', // Contoh path
            'status' => PengumumanStatus::PUBLISHED,
            'published_at' => now(),
        ]);
        Pengumuman::create([
            'user_id' => $akademik1->id,
            'judul' => 'Jadwal Ujian Akhir Semester',
            'slug' => Str::slug('Jadwal Ujian Akhir Semester'),
            'konten' => '<p>Berikut adalah jadwal ujian akhir semester untuk semua kelas. Mohon dipersiapkan dengan baik.</p>',
            'status' => PengumumanStatus::DRAFT,
        ]);

        // 8. Buat Kegiatan Galeri dan Foto-fotonya (oleh Akademik atau Admin)
        $kegiatanRamadhan = KegiatanGaleri::create([
            'nama_kegiatan' => 'Kegiatan Pesantren Kilat Ramadhan 1446 H',
            'slug_kegiatan' => Str::slug('Kegiatan Pesantren Kilat Ramadhan 1446 H'),
            'deskripsi_kegiatan' => 'Dokumentasi kegiatan pesantren kilat selama bulan Ramadhan.',
            'foto_sampul' => 'galeri-sampul/ramadhan-cover.jpg', // Contoh path
            'user_id' => $akademik1->id,
            'status_publikasi' => StatusPublikasiGaleri::TERBIT,
            'tanggal_publikasi' => now()->subDays(5),
        ]);
        Foto::create(['kegiatan_galeri_id' => $kegiatanRamadhan->id, 'user_id' => $akademik1->id, 'judul_foto' => 'Pembukaan Pesantren Kilat', 'path_file' => 'galeri/kegiatan-ramadhan/foto1.jpg', 'urutan_foto' => 1]);
        Foto::create(['kegiatan_galeri_id' => $kegiatanRamadhan->id, 'user_id' => $akademik1->id, 'judul_foto' => 'Belajar Bersama', 'path_file' => 'galeri/kegiatan-ramadhan/foto2.jpg', 'urutan_foto' => 2]);
        Foto::create(['kegiatan_galeri_id' => $kegiatanRamadhan->id, 'user_id' => $akademik1->id, 'judul_foto' => 'Buka Puasa Bersama', 'path_file' => 'galeri/kegiatan-ramadhan/foto3.jpg', 'urutan_foto' => 3]);

        // 9. Buat Data SPP (oleh Admin atau Akademik)
        if ($santri1) {
            Spp::create(['santri_id' => $santri1->id, 'bulan' => Carbon::now()->subMonth()->month, 'tahun' => Carbon::now()->subMonth()->year, 'jumlah_bayar' => 150000, 'status_pembayaran' => StatusSpp::BELUM_BAYAR, 'pencatat_id' => $admin->id]);
            Spp::create(['santri_id' => $santri1->id, 'bulan' => Carbon::now()->month, 'tahun' => Carbon::now()->year, 'jumlah_bayar' => 150000, 'status_pembayaran' => StatusSpp::BELUM_BAYAR, 'pencatat_id' => $akademik1->id]);
        }
        if ($santri4) {
            Spp::create(['santri_id' => $santri4->id, 'bulan' => Carbon::now()->month, 'tahun' => Carbon::now()->year, 'jumlah_bayar' => 150000, 'status_pembayaran' => StatusSpp::BELUM_BAYAR, 'pencatat_id' => $akademik1->id]);
        }

        // Pendaftar Santri (untuk di-manage)
        PendaftarSantri::create([
            'nama_lengkap_calon_santri' => 'Calon Santri Pending',
            'tanggal_lahir_calon_santri' => '2017-01-01',
            'jenis_kelamin_calon_santri' => 'laki-laki',
            'nama_wali' => 'Wali Calon Pending',
            'nomor_telepon_wali' => '08000000001',
            'email_wali' => 'wali.pending@example.com',
            'status_pendaftaran' => StatusPendaftaranSantri::PENDING,
        ]);
         PendaftarSantri::create([
            'nama_lengkap_calon_santri' => 'Calon Santri Diproses',
            'tanggal_lahir_calon_santri' => '2016-05-05',
            'jenis_kelamin_calon_santri' => 'perempuan',
            'nama_wali' => 'Wali Calon Diproses',
            'nomor_telepon_wali' => '08000000002',
            'email_wali' => 'wali.diproses@example.com',
            'status_pendaftaran' => StatusPendaftaranSantri::DIPROSES,
        ]);
        Setting::updateOrCreate(['key' => 'contact_address'], ['value' => 'Jl. TPQ Al-Barokah No. 1']);
        Setting::updateOrCreate(['key' => 'contact_phone'], ['value' => '081234567890']);
        Setting::updateOrCreate(['key' => 'contact_email'], ['value' => 'info@tpq.com']);
        Setting::updateOrCreate(['key' => 'contact_maps_iframe'], ['value' => '']);
        
        Setting::updateOrCreate(['key' => 'web_brief_history'], ['value' => " Menjadi lembaga pendidikan Al-Qur'an terdepan dalam mencetak generasi yang cinta Al-Qur'an, berakhlak mulia, cerdas, dan bermanfaat bagi umat."]);
        Setting::updateOrCreate(['key' => 'web_mission'], ['value' => "1. Menyelenggarakan pembelajaran Al-Qur'an yang efektif, inovatif, dan menyenangkan.\n2. Membina santri agar memiliki pemahaman Al-Qur'an yang baik dan mampu mengamalkannya.\n3. Mengembangkan potensi santri dalam bidang akademik, non-akademik, dan keagamaan.\n4. Menanamkan nilai-nilai Islam dan akhlakul karimah dalam setiap aspek pendidikan.\n5. Membangun kerjasama yang erat dengan orang tua dan masyarakat."]);
        Setting::updateOrCreate(['key' => 'web_vision'], ['value' => " Didirikan pada tahun [Tahun Berdiri], TPQ kami berawal dari keprihatinan akan pentingnya pendidikan Al-Qur'an sejak dini. Dengan semangat kebersamaan dan dedikasi, kami terus berkembang hingga saat ini, berkomitmen untuk memberikan pendidikan terbaik bagi para santri. "]);        

        // Model::reguard();
        $this->command->info('Database berhasil di-seed dengan data LMS TPQ!');
    }
}
