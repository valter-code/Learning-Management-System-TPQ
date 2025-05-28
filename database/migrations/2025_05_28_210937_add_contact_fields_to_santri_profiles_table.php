<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('santri_profiles', function (Blueprint $table) {
            // Tambahkan kolom nomor_telepon_wali setelah nama_wali, bisa nullable
            $table->string('nomor_telepon_wali', 20)->nullable()->after('nama_wali');
            // Tambahkan kolom email_wali setelah nomor_telepon_wali, bisa nullable
            $table->string('email_wali')->nullable()->after('nomor_telepon_wali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santri_profiles', function (Blueprint $table) {
            // Hapus kolom saat rollback
            $table->dropColumn('nomor_telepon_wali');
            $table->dropColumn('email_wali');
        });
    }
};