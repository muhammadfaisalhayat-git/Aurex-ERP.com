<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_type',
        'summary',
        'due_date',
        'activitable_id',
        'activitable_type',
        'user_id',
        'status',
        'feedback',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function activitable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
