<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusPendaftaranSantri;

class PendaftarSantri extends Model
{
    use HasFactory;

    protected $table = 'pendaftar_santri';

    protected $fillable = [
        'nama_lengkap_calon_santri',
        'tempat_lahir_calon_santri',
        'tanggal_lahir_calon_santri',
        'jenis_kelamin_calon_santri',
        'alamat_calon_santri',
        'nama_wali',
        'nomor_telepon_wali',
        'email_wali',
        'pekerjaan_wali',
        'catatan_tambahan',
        'status_pendaftaran',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_lahir_calon_santri' => 'date',
        'status_pendaftaran' => StatusPendaftaranSantri::class,
    ];
}
