<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusAbsensi; // Gunakan Enum yang sudah ada atau yang baru

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_santri', function (Blueprint $table) { 
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->onDelete('cascade'); 
            $table->foreignId('santri_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('pengajar_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_absensi'); 
            $table->string('status_kehadiran')->default(StatusAbsensi::MASUK->value);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pertemuan_id', 'santri_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_santri');
    }
};
