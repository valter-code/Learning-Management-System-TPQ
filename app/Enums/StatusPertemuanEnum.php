<?php
namespace App\Enums; // Namespace yang benar

enum StatusPertemuanEnum: string
{
    case DIJADWALKAN = 'Dijadwalkan';
    case BERLANGSUNG = 'Berlangsung';
    case SELESAI = 'Selesai';
    case DIBATALKAN = 'Dibatalkan';

    public function getLabel(): string
    {
        return $this->value;
    }
}