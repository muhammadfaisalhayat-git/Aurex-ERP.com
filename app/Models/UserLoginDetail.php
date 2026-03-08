<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
