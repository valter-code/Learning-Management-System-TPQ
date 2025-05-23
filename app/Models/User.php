<?php

namespace App\Models;

use Filament\Panel;
use App\Enums\UserRole;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage; // Untuk avatar default atau dari storage

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class, // Casting ke Enum
    ];

     // Relasi: Kelas yang diajar oleh Pengajar ini (Many-to-Many)
     public function mengajarDiKelas(): BelongsToMany
     {
         return $this->belongsToMany(Kelas::class, 'kelas_user', 'user_id', 'kelas_id')->withTimestamps();
     }

     // Relasi: Kelas yang diikuti oleh Santri ini (Many-to-Many)
     public function kelasYangDiikuti(): BelongsToMany
     {
        return $this->belongsToMany(Kelas::class, 'kelas_santri', 'user_id', 'kelas_id')->withTimestamps();     }

     // Relasi: Kelas di mana Pengajar ini menjadi Wali Kelas (One-to-Many)
    // Ini menggunakan Opsi A (wali_kelas_id di tabel kelas)
    public function kelasSebagaiWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function santriProfile(): HasOne
    {
        return $this->hasOne(SantriProfile::class, 'user_id'); // 'user_id' adalah foreign key di tabel santri_profiles
    }

    // Relasi Pengajar ke Kelas (asumsi tabel pivot 'kelas_pengajar' atau 'kelas_user')
    public function kelasYangDiajar(): BelongsToMany
    {
        // Sesuaikan nama tabel pivot dan foreign key jika berbeda
        return $this->belongsToMany(Kelas::class, 'kelas_user', 'user_id', 'kelas_id')->withTimestamps();
    }

    // Relasi Santri ke Kelas (asumsi tabel pivot 'kelas_santri')
    // public function kelasYangDiikuti(): BelongsToMany
    // {
    //     // user_id di sini merujuk ke kolom user_id di tabel pivot kelas_santri
    //     return $this->belongsToMany(Kelas::class, 'kelas_santri', 'user_id', 'kelas_id')->withTimestamps();
    // }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->role === UserRole::ADMIN) {
            return true; // Admin bisa akses semua panel
        }

        return match ($panel->getId()) {
            'admin' => $this->role === UserRole::ADMIN,
            'akademik' => $this->role === UserRole::AKADEMIK,
            'pengajar' => $this->role === UserRole::PENGAJAR,
            'santri' => $this->role === UserRole::SANTRI,
            default => false,
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            // Jika avatar_url adalah path relatif di storage public
            // return Storage::disk('public')->url($this->avatar_url);
            // Jika avatar_url adalah URL absolut
            return $this->avatar_url;
        }
        // Avatar default jika tidak ada
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function pengumpulanTugasSantri(): HasMany { // Nama relasi yang berbeda
        return $this->hasMany(PengumpulanTugas::class, 'santri_id');
    }

//     public function user(): BelongsTo
// {
//     return $this->belongsTo(User::class, 'user_id'); // 'user_id' adalah foreign key di tabel pertemuan
// }
// ...
    
}