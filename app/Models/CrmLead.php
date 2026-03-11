<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'mobile',
        'address',
        'source',
        'salesman_id',
        'company_id',
        'branch_id',
        'status',
        'notes',
    ];

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function opportunities()
    {
        return $this->hasMany(CrmOpportunity::class, 'lead_id');
    }

    public function activities()
    {
        return $this->morphMany(CrmActivity::class, 'activitable');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
