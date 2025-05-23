<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tugas', function (Blueprint $table) { // Gunakan 'tugas_list' jika 'tugas' adalah keyword SQL
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('ID Pembuat Tugas')->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable(); // Path ke file lampiran tugas (jika ada)
            $table->integer('poin_maksimal')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tugas');
    }
};