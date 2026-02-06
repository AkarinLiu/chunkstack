<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'array' => json_decode($setting->value, true) ?? [],
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        $setting = static::where('key', $key)->first();

        $data = [
            'value' => match ($type) {
                'boolean' => $value ? '1' : '0',
                'integer', 'float' => (string) $value,
                'array', 'json' => json_encode($value),
                default => (string) $value,
            },
            'type' => $type,
        ];

        if ($description) {
            $data['description'] = $description;
        }

        if ($setting) {
            $setting->update($data);
        } else {
            $data['key'] = $key;
            static::create($data);
        }
    }
}
