<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatusSpp;

class Spp extends Model
{
    use HasFactory;

    protected $table = 'spp';

    protected $fillable = [
        'santri_id',
        'bulan',
        'biaya_bulanan', // Ditambahkan
        'tahun',
        'jumlah_bayar',
        'tanggal_bayar',
        'status_pembayaran',
        'pencatat_id', // Diubah dari admin_id
        'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'status_pembayaran' => StatusSpp::class,
        'jumlah_bayar' => 'decimal:2',
        'biaya_bulanan' => 'decimal:2', // Ditambahkan cast ke decimal

    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'santri_id');
    }

    public function pencatat(): BelongsTo // Diubah dari admin()
    {
        return $this->belongsTo(User::class, 'pencatat_id');
    }

    public function getNamaBulanAttribute(): string
    {
        return \Carbon\Carbon::create()->month($this->bulan)->translatedFormat('F');
    }
}
