<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriPertemuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     * Laravel akan mengasumsikan 'materi_pertemuans' jika tidak dispesifikkan.
     *
     * @var string
     */
    protected $table = 'pertemuan_materi'; // Anda bisa menyesuaikan ini jika perlu

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pertemuan_id',
        'judul_materi',
        'deskripsi_materi',
        'path_file_materi', // Path ke file materi
        // 'urutan', // Opsional, untuk menentukan urutan materi
    ];

    /**
     * Relasi many-to-one: MateriPertemuan dimiliki oleh satu Pertemuan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }
}
