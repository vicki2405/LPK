<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Helper to get setting value by key with fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting || $setting->value === null) {
            return $default;
        }

        $decoded = json_decode($setting->value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
    }

    /**
     * Helper to set setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): static
    {
        \Illuminate\Support\Facades\Cache::forget('landing_page_props');
        \Illuminate\Support\Facades\Cache::forget('site_settings_all');
        $val = is_array($value) ? json_encode($value) : (string) $value;
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $val, 'group' => $group]
        );
    }

    /**
     * Get all settings as structured associative array.
     */
    public static function getAllSettings(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('site_settings_all', 300, function () {
            $settings = static::all();
            $result = [];
            foreach ($settings as $s) {
                $decoded = json_decode($s->value, true);
                $result[$s->key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $s->value;
            }
            return $result;
        });
    }
}
