<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\PengumumanStatus; 
use Spatie\Sluggable\HasSlug; 
use Spatie\Sluggable\SlugOptions; 
use Illuminate\Database\Eloquent\Builder; 

class Pengumuman extends Model
{
    use HasFactory; // HasSlug

    protected $table = 'pengumuman';
    protected $fillable = [
        'user_id',
        'judul',
        'slug',
        'konten',
        'foto',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => PengumumanStatus::class, 
        'published_at' => 'datetime',
    ];

    /**
     * Jika Anda ingin membuat slug otomatis dari judul (Opsional).
     * Anda perlu install paket: composer require spatie/laravel-sluggable
     */
    // public function getSlugOptions() : SlugOptions
    // {
    //     return SlugOptions::create()
    //         ->generateSlugsFrom('judul')
    //         ->saveSlugsTo('slug');
    // }

    /**
     * Relasi ke User (pembuat/penanggung jawab pengumuman).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Anda juga bisa menambahkan scope untuk pengumuman yang sudah publish, dll.
    // public function scopePublished($query)
    // {
    //     return $query->where('status', PengumumanStatus::PUBLISHED->value)
    //                  ->whereNotNull('published_at')
    //                  ->where('published_at', '<=', now());
    // }

    public function scopePublished(Builder $query): Builder
{
    return $query->where('status', PengumumanStatus::PUBLISHED)
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
}
}