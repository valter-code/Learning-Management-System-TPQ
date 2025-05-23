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
        // Hapus tabel pivot terlebih dahulu untuk menghindari masalah foreign key
        Schema::dropIfExists('materi_pertemuan');
        Schema::dropIfExists('pertemuan_tugas');

        // Kemudian hapus tabel master
        Schema::dropIfExists('materi');
        Schema::dropIfExists('tugas'); // Atau 'tugas_list' jika Anda menamainya demikian
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Membuat kembali tabel 'materi'
        // Anda perlu menyalin skema persis dari migrasi asli pembuat tabel 'materi'
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('ID Pembuat Materi')->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['file', 'link', 'text'])->default('text'); // Sesuaikan dengan Enum/definisi asli Anda
            $table->string('path_file')->nullable();
            $table->string('url_link')->nullable();
            $table->longText('konten_text')->nullable();
            $table->timestamps();
        });

        // Membuat kembali tabel 'tugas'
        // Anda perlu menyalin skema persis dari migrasi asli pembuat tabel 'tugas'
        Schema::create('tugas', function (Blueprint $table) { // Atau 'tugas_list'
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('ID Pembuat Tugas')->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->integer('poin_maksimal')->nullable();
            $table->timestamps();
        });

        // Membuat kembali tabel 'materi_pertemuan'
        // Anda perlu menyalin skema persis dari migrasi asli pembuat tabel 'materi_pertemuan'
        Schema::create('materi_pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->onDelete('cascade');
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['materi_id', 'pertemuan_id']);
        });

        // Membuat kembali tabel 'pertemuan_tugas'
        // Anda perlu menyalin skema persis dari migrasi asli pembuat tabel 'pertemuan_tugas'
        Schema::create('pertemuan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->onDelete('cascade');
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->dateTime('deadline_spesifik')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();
            $table->unique(['pertemuan_id', 'tugas_id']);
        });
    }
};
