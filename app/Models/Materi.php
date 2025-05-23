<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materi extends Model {
    use HasFactory;

    protected $table = 'materi';
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'tipe', 'path_file', 'url_link', 'konten_text'];

    public function user(): BelongsTo { // Pembuat materi
        return $this->belongsTo(User::class, 'user_id');
    }
    public function pertemuans(): BelongsToMany {
        return $this->belongsToMany(Pertemuan::class, 'materi_pertemuan')->withTimestamps();
    }
}