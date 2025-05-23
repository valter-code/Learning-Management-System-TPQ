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
        Schema::create('santri_profiles', function (Blueprint $table) {
            $table->id(); // Primary key untuk tabel profil ini sendiri
            
            // Foreign key yang merujuk ke tabel 'users'
            // Ini juga bisa dijadikan primary key jika Anda ingin relasi one-to-one yang ketat
            // dan user_id unik di tabel ini.
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            
            $table->text('kelas')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nama_wali')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santri_profiles');
    }
};
