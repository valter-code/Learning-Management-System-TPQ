<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('judul_pertemuan');
            $table->date('tanggal_pertemuan');
            $table->time('waktu_mulai')->nullable();
            $table->text('deskripsi_pertemuan')->nullable();
            // Tambahkan kolom lain yang relevan, misal: user_id (pembuat pertemuan/pengajar)
            // $table->foreignId('pengajar_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pertemuan');
    }
};