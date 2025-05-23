<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case AKADEMIK = 'akademik';
    case PENGAJAR = 'pengajar';
    case SANTRI = 'santri';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::AKADEMIK => 'Akademik',
            self::PENGAJAR => 'Pengajar',
            self::SANTRI => 'Santri',
        };
    }

    public function getColor(): string 
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::AKADEMIK => 'warning',
            self::PENGAJAR => 'info',
            self::SANTRI => 'success',
            default => 'gray',
        };
    }
}