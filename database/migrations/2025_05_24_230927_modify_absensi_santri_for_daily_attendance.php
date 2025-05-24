<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi_santri', function (Blueprint $table) {
            // 1. Hapus foreign key dan unique constraint lama yang melibatkan pertemuan_id
            // Kita akan mencoba drop dengan nama default Laravel.
            // Jika nama constraint Anda berbeda, Anda perlu menyesuaikannya.
            // Atau, jika Anda tahu pasti constraintnya ada, Anda bisa langsung drop tanpa if.

            // Drop foreign key untuk pertemuan_id terlebih dahulu
            // Nama default constraint: absensi_santri_pertemuan_id_foreign
            // Beberapa driver (seperti MySQL) memerlukan array untuk dropForeign jika kolomnya saja yang disebut.
            // Jika Anda hanya punya satu foreign key di kolom itu, cukup nama kolom.
            if (Schema::hasColumn('absensi_santri', 'pertemuan_id')) {
                try {
                    // Mencoba drop foreign key dengan menyebutkan kolomnya.
                    // Ini akan bekerja jika hanya ada satu FK di kolom tersebut
                    // atau jika driver database mendukungnya.
                    $table->dropForeign(['pertemuan_id']);
                } catch (\Exception $e) {
                    // Log error jika drop gagal, mungkin karena nama constraint berbeda
                    // atau constraint tidak ada.
                    // Di lingkungan produksi, Anda mungkin ingin lebih hati-hati.
                    // Untuk pengembangan, ini bisa diabaikan jika kolomnya berhasil di-drop nanti.
                    DB::rollBack(); // Batalkan transaksi jika ada
                    // Log::warning("Gagal drop foreign key untuk pertemuan_id: " . $e->getMessage());
                    // throw $e; // Atau lempar lagi errornya jika ini krusial
                }
            }

            // Drop unique constraint lama
            // Nama default: absensi_santri_pertemuan_id_santri_id_unique
            // Untuk dropUnique, Anda perlu memberikan array kolom atau nama eksplisit constraintnya.
            // Jika Anda tidak yakin namanya, cara paling aman adalah mengetahui nama constraint dari DB Anda.
            // Untuk sekarang, kita coba drop kolomnya dulu, lalu unique constraint baru akan dibuat.
            // Jika Anda tahu nama unique constraintnya, gunakan:
            // $table->dropUnique('nama_unique_constraint_lama_anda');
            // Atau jika unique constraint hanya melibatkan kolom pertemuan_id dan santri_id:
            // $table->dropUnique(['pertemuan_id', 'santri_id']); // Ini mungkin tidak selalu berhasil jika nama constraintnya custom

            // 2. Hapus kolom pertemuan_id
            // Ini akan otomatis menghapus index dan foreign key yang terkait dengan kolom ini di banyak database (seperti MySQL)
            // jika belum di-drop secara eksplisit.
            if (Schema::hasColumn('absensi_santri', 'pertemuan_id')) {
                $table->dropColumn('pertemuan_id');
            }

            // 3. Tambahkan kolom kelas_id (nullable)
            if (!Schema::hasColumn('absensi_santri', 'kelas_id')) {
                $table->foreignId('kelas_id')
                      ->nullable()
                      ->after('santri_id')
                      ->comment('Kelas santri pada hari absensi jika relevan')
                      ->constrained('kelas')
                      ->onDelete('set null');
            }

            // 4. Tambahkan unique constraint baru untuk absensi harian per santri
            // Memberi nama eksplisit pada unique constraint lebih baik.
            $table->unique(['santri_id', 'tanggal_absensi'], 'absensi_santri_harian_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_santri', function (Blueprint $table) {
            // 1. Hapus unique constraint harian
            $table->dropUnique('absensi_santri_harian_unique');

            // 2. Hapus kolom kelas_id
            if (Schema::hasColumn('absensi_santri', 'kelas_id')) {
                // Drop foreign key untuk kelas_id terlebih dahulu
                // Nama default constraint: absensi_santri_kelas_id_foreign
                try {
                    $table->dropForeign(['kelas_id']);
                } catch (\Exception $e) {
                    // Log::warning("Gagal drop foreign key untuk kelas_id saat rollback: " . $e->getMessage());
                }
                $table->dropColumn('kelas_id');
            }

            // 3. Tambahkan kembali kolom pertemuan_id
            if (!Schema::hasColumn('absensi_santri', 'pertemuan_id')) {
                $table->foreignId('pertemuan_id')
                      ->after('santri_id')
                      ->constrained('pertemuan') // Asumsi tabel 'pertemuan' ada
                      ->onDelete('cascade');
            }

            // 4. Tambahkan kembali unique constraint lama
            // Ini mungkin gagal jika ada data yang tidak unik setelah perubahan di 'up'
            // atau jika nama constraintnya tidak standar.
            // $table->unique(['pertemuan_id', 'santri_id'], 'absensi_santri_pertemuan_id_santri_id_unique');
            // Jika Anda tidak yakin nama constraint lama, mungkin lebih aman untuk tidak menambahkannya kembali
            // atau pastikan Anda tahu nama yang benar.
        });
    }
};
