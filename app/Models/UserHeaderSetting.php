<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHeaderSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'header_title',
        'logo_path',
        'show_company',
        'show_branch',
        'show_date',
    ];

    protected $casts = [
        'show_company' => 'boolean',
        'show_branch' => 'boolean',
        'show_date' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
