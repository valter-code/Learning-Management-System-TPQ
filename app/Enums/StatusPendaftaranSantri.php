<?php

namespace App\Enums;

enum StatusPendaftaranSantri: string
{
    case PENDING = 'pending';
    case AKTIF = 'aktif';
    case DITOLAK = 'ditolak';
    case DIPROSES = 'diproses'; 

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::AKTIF => 'Aktif (Sudah Jadi Santri)',
            self::DITOLAK => 'Ditolak',
            self::DIPROSES => 'Sedang Diproses',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::AKTIF => 'success',
            self::DITOLAK => 'danger',
            self::DIPROSES => 'info',
        };
    }
}