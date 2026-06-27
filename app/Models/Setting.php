<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Helper to get a setting value with a default fallback.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set/update a setting value.
     */
    public static function setValue(string $key, mixed $value): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }
}
