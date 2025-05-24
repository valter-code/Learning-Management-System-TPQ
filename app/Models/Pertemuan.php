<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Coolsam\NestedComments\Concerns\HasComments;
use Coolsam\NestedComments\Concerns\HasReactions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// BelongsToMany tidak lagi digunakan di sini jika materi/tugas sudah direct entry
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\StatusPertemuanEnum; // <--- TAMBAHKAN BARIS INI!

class Pertemuan extends Model
{
    use HasFactory;
    use HasComments;
    use HasReactions;

    // Anda mengubah nama tabel menjadi singular 'pertemuan'.
    // Pastikan file migrasi Anda juga membuat tabel dengan nama 'pertemuan'.
    protected $table = 'pertemuan';

    protected $fillable = [
        'kelas_id',
        'user_id',
        'judul_pertemuan',
        'tanggal_pertemuan',
        'waktu_mulai',
        'deskripsi_pertemuan',
        'status_pertemuan',
    ];

    protected $casts = [
        'tanggal_pertemuan' => 'date',
        'status_pertemuan' => StatusPertemuanEnum::class, // Sekarang ini akan merujuk ke App\Enums\StatusPertemuanEnum
    ];

    public function user(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id'); // 'user_id' adalah foreign key di tabel pertemuan
}

    public function materiItems()
    {
        return $this->hasMany(MateriPertemuan::class, 'id_pertemuan');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi yang dikomentari sudah benar jika Anda tidak lagi menggunakan sistem bank materi/tugas terpusat
    // public function materi(): BelongsToMany
    // {
    //     return $this->belongsToMany(Materi::class, 'materi_pertemuan', 'pertemuan_id', 'materi_id')->withTimestamps();
    // }
    // public function tugas(): BelongsToMany {
    //     return $this->belongsToMany(Tugas::class, 'pertemuan_tugas')
    //                 ->withPivot('deadline_spesifik', 'catatan_tambahan')
    //                 ->withTimestamps();
    // }

    public function itemsMateri(): HasMany
    {
        // Pastikan model PertemuanMateri merujuk ke tabel yang benar ('pertemuan_materi' jika singular)
        return $this->hasMany(PertemuanMateri::class, 'pertemuan_id');
    }

    public function itemsTugas(): HasMany
    {
        // Pastikan model PertemuanTugasItem merujuk ke tabel yang benar ('pertemuan_tugas_item' jika singular)
        return $this->hasMany(PertemuanTugasItem::class, 'pertemuan_id');
    }

    public function absensiSantris(): HasMany
    {
        return $this->hasMany(AbsensiSantri::class, 'pertemuan_id');
    }
}