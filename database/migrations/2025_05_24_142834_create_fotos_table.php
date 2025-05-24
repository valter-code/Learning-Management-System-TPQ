<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto', function (Blueprint $table) { // Nama tabel singular
            $table->id();
            $table->foreignId('kegiatan_galeri_id')->constrained('kegiatan_galeri')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->comment('ID User yang mengupload')->constrained('users')->onDelete('set null');
            $table->string('judul_foto')->nullable();
            $table->text('deskripsi_foto')->nullable();
            $table->string('path_file'); // Path ke file gambar
            $table->string('tipe_mime')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable(); // Ukuran file dalam bytes
            $table->integer('urutan_foto')->default(0);
            $table->boolean('is_unggulan')->default(false)->comment('Apakah foto ini jadi sampul/highlight');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto');
    }
};
