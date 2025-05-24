<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Gunakan raw SQL untuk menghapus semua constraints dan indexes yang bermasalah
        $this->cleanupConstraintsAndIndexes();

        Schema::table('absensi_santri', function (Blueprint $table) {
            // Hapus kolom kelas_id jika ada
            if (Schema::hasColumn('absensi_santri', 'kelas_id')) {
                $table->dropColumn('kelas_id');
            }

            // Hapus kolom pertemuan_id jika ada
            if (Schema::hasColumn('absensi_santri', 'pertemuan_id')) {
                $table->dropColumn('pertemuan_id');
            }

            // Buat unique constraint baru
            $table->unique(['santri_id', 'tanggal_absensi'], 'absensi_santri_harian_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_santri', function (Blueprint $table) {
            $table->dropUnique('absensi_santri_harian_unique');

            if (!Schema::hasColumn('absensi_santri', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->after('santri_id')
                      ->constrained('kelas')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('absensi_santri', 'pertemuan_id')) {
                 $table->foreignId('pertemuan_id')->after('santri_id')
                      ->constrained('pertemuan')->onDelete('cascade');
            }
        });
    }

    /**
     * Bersihkan semua constraints dan indexes yang bermasalah
     */
    private function cleanupConstraintsAndIndexes(): void
    {
        // Daftar kemungkinan nama constraint/index yang perlu dihapus
        $possibleConstraints = [
            'absensi_santri_santri_id_tanggal_absensi_unique',
            'absensi_santri_harian_unique',
            'absensi_santri_pertemuan_id_santri_id_unique',
            'absensi_santri_harian_unique_final'
        ];

        foreach ($possibleConstraints as $constraint) {
            try {
                DB::statement("ALTER TABLE absensi_santri DROP INDEX {$constraint}");
            } catch (\Exception $e) {
                // Abaikan jika constraint tidak ada
                continue;
            }
        }

        // Hapus foreign keys yang mungkin ada
        $foreignKeys = [
            'absensi_santri_kelas_id_foreign',
            'absensi_santri_pertemuan_id_foreign'
        ];

        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE absensi_santri DROP FOREIGN KEY {$fk}");
            } catch (\Exception $e) {
                // Abaikan jika foreign key tidak ada
                continue;
            }
        }
    }
};