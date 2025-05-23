<?php
// app/Enums/StatusPengumpulanTugasEnum.php
namespace App\Enums;

enum StatusPengumpulanTugasEnum: string
{
    case BELUM_DIKUMPULKAN = 'belum_dikumpulkan';
    case DIKUMPULKAN = 'dikumpulkan';
    case TERLAMBAT = 'terlambat';
    case DINILAI = 'dinilai';

    public function getLabel(): string
    {
        return match ($this) {
            self::BELUM_DIKUMPULKAN => 'Belum Dikumpulkan',
            self::DIKUMPULKAN => 'Sudah Dikumpulkan',
            self::TERLAMBAT => 'Terlambat Dikumpulkan',
            self::DINILAI => 'Sudah Dinilai',
        };
    }

    // Opsional: tambahkan getColor() jika belum ada
    public function getColor(): string
    {
        return match ($this) {
            self::BELUM_DIKUMPULKAN => 'danger',
            self::DIKUMPULKAN => 'primary',
            self::TERLAMBAT => 'warning',
            self::DINILAI => 'success',
        };
    }
}