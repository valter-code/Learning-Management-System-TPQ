<?php

namespace App\Enums;

enum StatusSpp: string
{
    case BELUM_BAYAR = 'belum_bayar';
    case SUDAH_BAYAR = 'sudah_bayar';
    case TERLAMBAT = 'terlambat'; // Opsional

    public function getLabel(): string
    {
        return match ($this) {
            self::BELUM_BAYAR => 'Belum Bayar',
            self::SUDAH_BAYAR => 'Sudah Bayar',
            self::TERLAMBAT => 'Terlambat',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BELUM_BAYAR => 'danger',
            self::SUDAH_BAYAR => 'success',
            self::TERLAMBAT => 'warning',
        };
    }
}