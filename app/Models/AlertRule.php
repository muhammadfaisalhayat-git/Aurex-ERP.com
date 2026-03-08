<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'module',
        'condition_type',
        'threshold',
        'recipients',
        'is_active',
    ];

    protected $casts = [
        'recipients' => 'array',
        'is_active' => 'boolean',
        'threshold' => 'decimal:2',
    ];
}
