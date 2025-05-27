<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusSpp;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('users')->onDelete('cascade');
            $table->integer('bulan'); // 1-12
            $table->integer('tahun');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->date('tanggal_bayar')->nullable();
            $table->string('status_pembayaran')->default(StatusSpp::BELUM_BAYAR->value);
            
            $table->foreignId('pencatat_id')->nullable()->comment('Admin/Akademik yang mencatat/memverifikasi')->constrained('users')->onDelete('set null');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::table('spp', function (Blueprint $table) {
            // Jika Anda perlu drop foreign key secara eksplisit sebelum drop kolom
            // if (DB::getDriverName() !== 'sqlite') {
            //     $table->dropForeign(['pencatat_id']);
            // }
        });
        Schema::dropIfExists('spp');
    }
};
