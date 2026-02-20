<?php

namespace App\Models\Production;

use App\Models\Company;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'work_center_id',
        'code',
        'name',
        'brand',
        'model',
        'hourly_cost',
        'status'
    ];

    protected $casts = [
        'hourly_cost' => 'decimal:2'
    ];

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
