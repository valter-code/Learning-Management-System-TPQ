<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatusAbsensi;

class AbsensiSantri extends Model
{
    use HasFactory;

    protected $table = 'absensi_santri';

    protected $fillable = [
        'santri_id',
        'pengajar_id',
        'tanggal_absensi',
        'status_kehadiran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_absensi' => 'date',
        'status_kehadiran' => StatusAbsensi::class,
    ];

    // Relasi kelas() dihapus
    // public function kelas(): BelongsTo
    // {
    //     return $this->belongsTo(Kelas::class);
    // }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'santri_id');
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }
}
