<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name_en',
        'name_ar',
        'type', // Keeping for backward compatibility or migration
        'account_type_id',
        'parent_id',
        'level',
        'is_posting_allowed',
        'is_active',
        'sub_ledger_type',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    protected $casts = [
        'is_posting_allowed' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    protected $appends = ['name'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($account) {
            if ($account->parent_id) {
                $parent = static::find($account->parent_id);
                $account->level = $parent ? $parent->level + 1 : 1;
            } else {
                $account->level = 1;
            }
        });
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function mainAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function subAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function scopePosting($query)
    {
        return $query->where('is_posting_allowed', true);
    }

    public function scopeNonPosting($query)
    {
        return $query->where('is_posting_allowed', false);
    }

    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }
    public function isControlAccount()
    {
        return in_array($this->sub_ledger_type, ['customer', 'vendor']);
    }
}
