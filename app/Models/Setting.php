<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description'
    ];

    protected $primaryKey = 'key';      // Ubah primary key
    public $incrementing = false;       // Karena 'key' bukan auto-increment
    protected $keyType = 'string';      // Karena 'key' adalah string

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, string $default = ''): string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     */
    public static function setValue(string $key, string $value, string $description = ''): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description
            ]
        );
    }

    /**
     * Get multiple settings
     */
    public static function getMultiple(array $keys): array
    {
        return static::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
    }
}