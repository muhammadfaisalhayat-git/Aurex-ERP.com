<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

class AuditLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'action',
        'entity_type',
        'entity_id',
        'user_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $entityType, $entityId, $oldValues = null, $newValues = null, $notes = null)
    {
        return self::create([
            'company_id' => session('active_company_id'),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
            'notes' => $notes,
        ]);
    }
}
