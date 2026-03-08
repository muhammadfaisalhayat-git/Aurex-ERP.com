<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandatoryFieldConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'field_name',
        'field_label',
        'is_mandatory',
        'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
    ];
}
