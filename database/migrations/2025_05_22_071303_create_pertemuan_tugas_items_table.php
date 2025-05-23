<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pertemuan_tugas_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->onDelete('cascade');
            $table->string('judul_tugas');
            $table->text('deskripsi_tugas')->nullable();
            $table->string('file_lampiran_tugas')->nullable();
            $table->dateTime('deadline_tugas')->nullable();
            $table->integer('poin_maksimal_tugas')->nullable();
            $table->text('catatan_tambahan_tugas')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('pertemuan_tugas_items');
    }
};