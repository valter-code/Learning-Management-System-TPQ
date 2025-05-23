<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('ID Pembuat Materi')->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['file', 'link', 'text'])->default('text');
            $table->string('path_file')->nullable()->comment('Untuk tipe file');
            $table->string('url_link')->nullable()->comment('Untuk tipe link');
            $table->longText('konten_text')->nullable()->comment('Untuk tipe text');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('materi');
    }
};