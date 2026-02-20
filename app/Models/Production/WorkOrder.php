<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'work_center_id',
        'machine_id',
        'sequence',
        'operation_name',
        'planned_hours',
        'actual_hours',
        'started_at',
        'completed_at',
        'status'
    ];

    protected $casts = [
        'planned_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function qualityControls()
    {
        return $this->morphMany(QualityControl::class , 'reference');
    }
}
