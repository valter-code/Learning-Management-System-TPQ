<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\StatusPengumpulanTugasEnum;

class PengumpulanTugas extends Model {
    use HasFactory;
    protected $table = 'pengumpulan_tugas';
    protected $fillable = [
        'pertemuan_tugas_item_id', 'santri_id', 'file_jawaban', 
        'teks_jawaban', 'tanggal_pengumpulan', 'status_pengumpulan', 
        'nilai', 'komentar_pengajar'
    ];
    protected $casts = [
        'tanggal_pengumpulan' => 'datetime',
        'status_pengumpulan' => StatusPengumpulanTugasEnum::class, // <-- Gunakan di sini
    ];

    public function pertemuanTugasItem(): BelongsTo {
        return $this->belongsTo(PertemuanTugasItem::class, 'pertemuan_tugas_item_id');
    }
    public function santri(): BelongsTo {
        return $this->belongsTo(User::class, 'santri_id');
    }

    public function pengumpulanTugas(): HasMany {
        return $this->hasMany(PengumpulanTugas::class, 'pertemuan_tugas_item_id');
    }
    
    public function getPengumpulanSantriAttribute() {
        if (auth()->check()) {
            return $this->pengumpulanTugas()->where('santri_id', auth()->id())->first();
        }
        return null;
    }
    
}