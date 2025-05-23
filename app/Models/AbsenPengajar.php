<?php

namespace App\Models;

use App\Enums\StatusAbsensi; // Import Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenPengajar extends Model
{
    use HasFactory;

    protected $table = 'AbsensiPengajar'; // Sesuaikan dengan nama tabel di migrasi

    protected $fillable = [
        'pengajar_id',
        'tanggal',
        'waktu_masuk',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime:H:i:s', // Casting ke format jam, menit, detik
        'status' => StatusAbsensi::class, // Casting ke Enum
    ];

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }
}