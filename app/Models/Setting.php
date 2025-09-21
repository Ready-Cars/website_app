<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key','value'];

    public $timestamps = false;

    public static function get(string $key, $default = null)
    {
        $val = static::query()->where('key', $key)->value('value');
        if ($val === null) return $default;
        return $val;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $val = static::get($key, $default ? '1' : '0');
        if (is_bool($val)) return $val;
        $str = strtolower((string)$val);
        return in_array($str, ['1','true','yes','on'], true);
    }

    public static function getInt(string $key, int $default = 0): int
    {
        $val = static::get($key, (string)$default);
        return (int)$val;
    }

    public static function setValue(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => is_scalar($value) ? (string)$value : json_encode($value)]);
    }
}
