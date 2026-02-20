<?php

namespace App\Models\Production;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControl extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'reference_type',
        'reference_id',
        'inspector_id',
        'check_date',
        'status',
        'checkpoints',
        'remarks'
    ];

    protected $casts = [
        'check_date' => 'datetime',
        'checkpoints' => 'json'
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class , 'inspector_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
