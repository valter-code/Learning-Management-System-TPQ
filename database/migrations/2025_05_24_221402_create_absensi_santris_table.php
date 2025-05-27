<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusAbsensi;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_santri', function (Blueprint $table) { 
            $table->id();
            // foreignId('pertemuan_id') DIHAPUS SEPENUHNYA
            $table->foreignId('santri_id')->constrained('users')->onDelete('cascade'); 
            // $table->foreignId('pengajar_id')->nullable()->constrained('users')->onDelete('set null'); // Dicatat oleh pengajar (bisa null jika mandiri)
            $table->date('tanggal_absensi'); 
            $table->string('status_kehadiran')->default(StatusAbsensi::MASUK->value);
            $table->text('keterangan')->nullable();
            $table->time('waktu_masuk')->nullable(); // Pastikan ini ada dan tipe TIME

            $table->timestamps();

            // Unique constraint yang benar untuk absensi Harian Mandiri
            $table->unique(['santri_id', 'tanggal_absensi'], 'unique_santri_daily_absensi'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_santri');
    }
};