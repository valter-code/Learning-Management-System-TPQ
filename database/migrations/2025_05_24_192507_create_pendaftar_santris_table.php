<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusPendaftaranSantri;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar_santri', function (Blueprint $table) {
            $table->id();
            // Data Calon Santri
            $table->string('nama_lengkap_calon_santri');
            $table->string('tempat_lahir_calon_santri')->nullable();
            $table->date('tanggal_lahir_calon_santri');
            $table->enum('jenis_kelamin_calon_santri', ['laki-laki', 'perempuan']);
            $table->text('alamat_calon_santri')->nullable();

            // Data Wali/Orang Tua
            $table->string('nama_wali');
            $table->string('nomor_telepon_wali');
            $table->string('email_wali')->nullable(); // Email wali untuk notifikasi
            $table->string('pekerjaan_wali')->nullable();

            // Informasi Tambahan
            $table->text('catatan_tambahan')->nullable();
            $table->string('status_pendaftaran')->default(StatusPendaftaranSantri::PENDING->value);
            $table->text('catatan_admin')->nullable()->comment('Catatan dari admin terkait pendaftaran');
            
            $table->timestamps(); // Kapan pendaftaran dibuat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftar_santri');
    }
};
