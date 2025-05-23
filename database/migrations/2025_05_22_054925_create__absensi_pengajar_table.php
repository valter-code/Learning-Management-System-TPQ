<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusAbsensi; // Import Enum

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('AbsensiPengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajar_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable(); // Untuk mencatat jam masuk jika status 'Masuk'
            $table->enum('status', array_column(StatusAbsensi::cases(), 'value')); // Menggunakan Enum
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Opsional: Unique constraint untuk memastikan satu absensi per pengajar per hari
            $table->unique(['pengajar_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('AbsensiPengajar');
    }
};