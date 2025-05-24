<?php

namespace App\Enums;

enum StatusPublikasiGaleri: string
{
    case DRAFT = 'draft';
    case TERBIT = 'terbit';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::TERBIT => 'Sudah Terbit',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::TERBIT => 'success',
        };
    }
}