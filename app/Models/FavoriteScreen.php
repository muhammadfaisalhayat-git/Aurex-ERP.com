<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteScreen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'route_name',
        'label',
        'icon',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
