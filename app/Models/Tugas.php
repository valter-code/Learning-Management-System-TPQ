<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tugas extends Model { // Nama kelas singular, nama tabel bisa 'tugas' atau 'tugas_list'
    use HasFactory;
    protected $table = 'tugas'; // Eksplisit jika nama tabel berbeda dari plural 'tugases'
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'file_lampiran', 'poin_maksimal'];

    public function user(): BelongsTo { // Pembuat tugas
        return $this->belongsTo(User::class, 'user_id');
    }
    public function pertemuans(): BelongsToMany {
        return $this->belongsToMany(Pertemuan::class, 'pertemuan_tugas')
                    // Jika ada data pivot tambahan, definisikan di sini
                    // ->withPivot('tanggal_deadline_pertemuan', 'catatan_tugas_pertemuan') 
                    ->withTimestamps();
    }
}