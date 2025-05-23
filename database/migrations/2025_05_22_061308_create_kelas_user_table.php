<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('ID Pengajar')->constrained('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            // Jika ingin Opsi B untuk Wali Kelas, tambahkan: $table->boolean('is_wali_kelas')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'kelas_id']); // Pengajar tidak bisa didaftarkan dua kali ke kelas yang sama
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kelas_user');
    }
};