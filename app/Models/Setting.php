<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Получить значение настройки по ключу.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting ? self::decodeValue($setting->value) : $default;
    }

    /**
     * Установить значение настройки по ключу.
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => self::encodeValue($value)]);
    }

    /**
     * Кодировать значение для хранения в БД.
     */
    private static function encodeValue(mixed $value): string
    {
        return is_bool($value) ? ($value ? '1' : '0') : (string) $value;
    }

    /**
     * Декодировать значение из БД.
     */
    private static function decodeValue(?string $value): mixed
    {
        if ($value === '1') return true;
        if ($value === '0') return false;
        return $value;
    }
}
