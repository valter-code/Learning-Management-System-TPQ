<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) { // Nama tabel 'kelas' (singular) atau 'kelas_list' jika 'kelas' adalah keyword SQL
            $table->id();
            $table->string('nama_kelas');
            $table->text('deskripsi')->nullable();
            // OPSI A untuk Wali Kelas:
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};