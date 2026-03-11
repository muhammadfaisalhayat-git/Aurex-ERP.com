<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class CrmOpportunity extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'title',
        'lead_id',
        'customer_id',
        'expected_revenue',
        'expected_closing',
        'probability',
        'stage_id',
        'salesman_id',
        'company_id',
        'branch_id',
        'description',
    ];

    protected $casts = [
        'expected_revenue' => 'decimal:2',
        'expected_closing' => 'date',
        'probability' => 'integer',
    ];

    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function stage()
    {
        return $this->belongsTo(CrmPipelineStage::class, 'stage_id');
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function activities()
    {
        return $this->morphMany(CrmActivity::class, 'activitable');
    }
}
