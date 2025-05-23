<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PertemuanMateri extends Model {
    use HasFactory;

    protected $table = 'pertemuan_materi'; // <--- Sesuaikan dengan nama tabel di migrasi
    protected $fillable = [
        'pertemuan_id', 'judul_materi', 'tipe_materi', 
        'path_file_materi', 'url_link_materi', 'konten_text_materi', 'deskripsi_materi'
    ];
    public function pertemuan(): BelongsTo {
        return $this->belongsTo(Pertemuan::class);
    }
}