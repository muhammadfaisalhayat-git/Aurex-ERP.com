<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionRun extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'run_number',
        'start_date',
        'end_date',
        'status',
        'total_commission',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'total_commission' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statements()
    {
        return $this->hasMany(CommissionStatement::class);
    }

    public function isEditable()
    {
        return $this->status === 'draft';
    }

    public function isCalculatable()
    {
        return in_array($this->status, ['draft', 'calculated']);
    }

    public function isApprovable()
    {
        return $this->status === 'calculated';
    }
}
