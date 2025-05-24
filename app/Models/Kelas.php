<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; // Sesuaikan jika nama tabel berbeda

    protected $fillable = [
        'kelas_id',
        'nama_kelas',
        'judul_pertemuan',
        'tanggal_pertemuan',
        'waktu_mulai',
        'deskripsi_pertemuan',
        'status_pertemuan',
    ];


    protected $casts = [
        'tanggal_pertemuan' => 'date',
        'status_pertemuan' => StatusPertemuanEnum::class,
    ];

    // Relasi: Pengajar yang mengajar kelas ini (Many-to-Many)
    public function pengajars(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kelas_user', 'kelas_id', 'user_id')->withTimestamps();
    }

    // Relasi: Santri di kelas ini (Many-to-Many)
    public function santris(): BelongsToMany
    {
        // Pastikan user_id di tabel pivot merujuk ke ID santri
        return $this->belongsToMany(User::class, 'kelas_santri', 'kelas_id', 'user_id')->withTimestamps();
    }

    // Relasi: Wali kelas untuk kelas ini (One-to-Many Inverse)
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    // Relasi: Pertemuan untuk kelas ini (One-to-Many)
    public function pertemuans(): HasMany
    {
        return $this->hasMany(Pertemuan::class, 'kelas_id');
    }

    public function getPengajarsDisplayNamesAttribute(): string
{
    if ($this->pengajars->isNotEmpty()) {
        return $this->pengajars->pluck('name')->join(', ');
    }
    return 'N/A';
}   

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

      // RELASI UNTUK MATERI YANG DIINPUT LANGSUNG
      public function itemsMateri(): HasMany
      {
          // Pastikan PertemuanMateri adalah model yang benar dan 'pertemuan_id' adalah foreign key
          return $this->hasMany(PertemuanMateri::class, 'pertemuan_id');
      }

      // RELASI UNTUK TUGAS YANG DIINPUT LANGSUNG
    public function itemsTugas(): HasMany
    {
        // Pastikan PertemuanTugasItem adalah model yang benar dan 'pertemuan_id' adalah foreign key
        return $this->hasMany(PertemuanTugasItem::class, 'pertemuan_id');
    }
}