<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Enums\StatusPublikasiGaleri;
use Illuminate\Database\Eloquent\Builder;

class KegiatanGaleri extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'kegiatan_galeri'; 

    protected $fillable = [
        'nama_kegiatan',
        'slug_kegiatan',
        'deskripsi_kegiatan',
        'foto_sampul',
        'user_id',
        'status_publikasi',
        'tanggal_publikasi',
    ];

    protected $casts = [
        'status_publikasi' => StatusPublikasiGaleri::class,
        'tanggal_publikasi' => 'datetime',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('nama_kegiatan')
            ->saveSlugsTo('slug_kegiatan');
    }

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fotos(): HasMany // Satu kegiatan memiliki banyak foto
    {
        return $this->hasMany(Foto::class, 'kegiatan_galeri_id')->orderBy('urutan_foto');
    }
    
    // Scope untuk yang sudah terbit
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status_publikasi', StatusPublikasiGaleri::TERBIT)
                     ->whereNotNull('tanggal_publikasi')
                     ->where('tanggal_publikasi', '<=', now());
    }
}
