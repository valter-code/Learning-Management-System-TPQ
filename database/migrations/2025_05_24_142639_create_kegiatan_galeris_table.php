<?php

use App\Enums\StatusPublikasi;
use App\Enums\StatusPublikasiGaleri;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_galeri', function (Blueprint $table) { 
            $table->id();
            $table->string('nama_kegiatan');
            $table->string('slug_kegiatan')->unique()->nullable(); 
            $table->text('deskripsi_kegiatan')->nullable();
            $table->string('foto_sampul')->nullable()->comment('Path ke file gambar sampul');
            $table->foreignId('user_id')->nullable()->comment('ID User yang membuat')->constrained('users')->onDelete('set null');
            $table->string('status_publikasi')->default(StatusPublikasiGaleri::DRAFT->value);
            $table->timestamp('tanggal_publikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_galeri');
    }
};
