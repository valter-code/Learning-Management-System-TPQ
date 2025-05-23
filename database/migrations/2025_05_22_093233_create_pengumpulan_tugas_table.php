<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            // Merujuk ke item tugas spesifik di pertemuan tertentu
            $table->foreignId('pertemuan_tugas_item_id')->constrained('pertemuan_tugas_items')->onDelete('cascade');
            $table->foreignId('santri_id')->constrained('users')->onDelete('cascade'); // User dengan role santri
            $table->string('file_jawaban')->nullable(); // Path ke file jawaban
            $table->text('teks_jawaban')->nullable();   // Jika jawaban berupa teks online
            $table->timestamp('tanggal_pengumpulan');
            $table->enum('status_pengumpulan', ['belum_dikumpulkan', 'dikumpulkan', 'dinilai', 'terlambat'])->default('belum_dikumpulkan');
            $table->integer('nilai')->nullable();
            $table->text('komentar_pengajar')->nullable();
            $table->timestamps();

            $table->unique(['pertemuan_tugas_item_id', 'santri_id']); // Satu santri hanya bisa mengumpulkan satu kali per item tugas
        });
    }
    public function down(): void {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};