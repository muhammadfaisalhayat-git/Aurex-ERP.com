<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class AccountMapping extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'module',
        'key',
        'chart_of_account_id',
        'is_active',
        'notes',
    ];

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
