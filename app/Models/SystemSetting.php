<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

class SystemSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'system_settings';

    protected $fillable = [
        'company_id',
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
        // Use withoutGlobalScope to allow fetching global settings where company_id is NULL
        // First try to find a company-specific setting (if tenant scope allows it)
        // then fallback to a global setting (where company_id is null)
        $setting = self::withoutGlobalScope('tenant')
            ->where('key', $key)
            ->where(function ($query) {
                if (auth()->check() && \Session::has('active_company_id')) {
                    $query->where('company_id', \Session::get('active_company_id'))
                        ->orWhereNull('company_id');
                } else {
                    $query->whereNull('company_id');
                }
            })
            ->orderBy('company_id', 'desc') // Company-specific first, then null
            ->first();

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
        $setting = self::withoutGlobalScope('tenant')->where('key', $key)->first();

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
