<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'type',
        'location',
        'notes',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];
}
