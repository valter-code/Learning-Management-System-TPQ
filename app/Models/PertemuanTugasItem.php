<?php
namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PertemuanTugasItem extends Model {
    use HasFactory;
    protected $fillable = [
        'pertemuan_id', 'judul_tugas', 'deskripsi_tugas', 'file_lampiran_tugas', 
        'deadline_tugas', 'poin_maksimal_tugas', 'catatan_tambahan_tugas'
    ];
    protected $casts = [
        'deadline_tugas' => 'datetime',
    ];
    public function pertemuan(): BelongsTo {
        return $this->belongsTo(Pertemuan::class);
    }
    public function getPengumpulanSantriAttribute(): ?PengumpulanTugas
    {
        if (Auth::check()) {
            // Menggunakan relasi pengumpulanTugas() yang baru saja didefinisikan
            return $this->pengumpulanTugas()->where('santri_id', Auth::id())->first();
        }
        return null;
    }

    public function pengumpulanTugas(): HasMany // Nama metode jamak: pengumpulanTugas
    {
        // 'pertemuan_tugas_item_id' adalah foreign key di tabel 'pengumpulan_tugas'
        return $this->hasMany(PengumpulanTugas::class, 'pertemuan_tugas_item_id');
    }

    
}