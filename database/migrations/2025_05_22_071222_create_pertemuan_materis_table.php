<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pertemuan_materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->onDelete('cascade');
            $table->string('judul_materi');
            $table->enum('tipe_materi', ['file', 'link', 'text'])->default('text');
            $table->string('path_file_materi')->nullable();
            $table->string('url_link_materi')->nullable();
            $table->text('konten_text_materi')->nullable();
            $table->text('deskripsi_materi')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('pertemuan_materi');
    }
};