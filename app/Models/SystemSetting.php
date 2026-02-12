<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'display_name_en',
        'display_name_ar',
        'description',
        'is_editable',
    ];

    protected $casts = [
        'is_editable' => 'boolean',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue($key, $value, $type = 'string')
    {
        $setting = self::where('key', $key)->first();

        if ($setting && !$setting->is_editable) {
            return false;
        }

        $storedValue = match ($type) {
            'json' => json_encode($value),
            default => (string) $value,
        };

        if ($setting) {
            $setting->update(['value' => $storedValue, 'type' => $type]);
        }

        return true;
    }
}
