<?php

namespace App\Enums;

enum StatusAbsensi: string
{
    case MASUK = 'Masuk';
    case IZIN = 'Izin';
    case SAKIT = 'Sakit';
    // Tambahkan ALPA jika diperlukan
    // case ALPA = 'Alpa';

    public function getLabel(): string
    {
        return $this->value; // Atau sesuaikan jika label berbeda dari value
    }

    // Opsional: Metode untuk warna jika diperlukan di view
    public function getColor(): string
    {
        return match ($this) {
            self::MASUK => 'success', // Warna Filament (primary, success, warning, danger, gray)
            self::IZIN => 'warning',
            self::SAKIT => 'danger',
            // self::ALPA => 'gray',
        };
    }
}