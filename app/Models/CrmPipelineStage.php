<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class CrmPipelineStage extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'name_en',
        'name_ar',
        'sort_order',
        'color',
        'is_won',
        'is_lost',
        'company_id',
    ];

    public function opportunities()
    {
        return $this->hasMany(CrmOpportunity::class, 'stage_id');
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name_en) : $this->name_en;
    }
}
