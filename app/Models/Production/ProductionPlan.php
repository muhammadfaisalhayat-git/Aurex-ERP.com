<?php

namespace App\Models\Production;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionPlan extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'plan_date',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'plan_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }
}
