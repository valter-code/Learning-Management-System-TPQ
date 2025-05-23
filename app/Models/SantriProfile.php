<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SantriProfile extends Model
{
    use HasFactory;

    protected $table = 'santri_profiles';

    protected $fillable = [
        'user_id',
        'alamat',
        'tanggal_lahir',
        'nama_wali',
        'kelas',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
    
    /**
     * Relasi one-to-one (inverse) ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);    }
}
