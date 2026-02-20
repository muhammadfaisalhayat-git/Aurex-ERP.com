<?php

namespace App\Models\Production;

use App\Models\Branch;
use App\Models\Company;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkCenter extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name',
        'description',
        'capacity',
        'is_active'
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
