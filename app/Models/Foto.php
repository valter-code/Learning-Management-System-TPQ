<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foto extends Model
{
    use HasFactory;

    protected $table = 'foto'; // Eksplisit nama tabel singular

    protected $fillable = [
        'kegiatan_galeri_id',
        'user_id',
        'judul_foto',
        'deskripsi_foto',
        'path_file',
        'tipe_mime',
        'ukuran_file',
        'urutan_foto',
        'is_unggulan',
    ];

    protected $casts = [
        'is_unggulan' => 'boolean',
        'ukuran_file' => 'integer',
        'urutan_foto' => 'integer',
    ];

    public function kegiatanGaleri(): BelongsTo
    {
        return $this->belongsTo(KegiatanGaleri::class, 'kegiatan_galeri_id');
    }

    public function user(): BelongsTo // Pengunggah foto
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
