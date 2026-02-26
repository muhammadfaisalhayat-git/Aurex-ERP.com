<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bankAccount) {
            $service = app(\App\Services\AccountingService::class);
            $parentCode = ($bankAccount->account_type === 'cash') ? '1101' : '1102';
            $parent = ChartOfAccount::where('code', $parentCode)->first();

            if ($parent) {
                $coa = ChartOfAccount::create([
                    'company_id' => $bankAccount->company_id,
                    'code' => $service->generateAccountCode($parent->type, $parent->id),
                    'name_en' => $bankAccount->bank_name ?: ($bankAccount->account_type === 'cash' ? 'Cash Account' : 'Bank Account'),
                    'name_ar' => $bankAccount->name_ar,
                    'type' => $parent->type,
                    'account_type_id' => $parent->account_type_id,
                    'parent_id' => $parent->id,
                    'is_posting_allowed' => true,
                    'is_active' => $bankAccount->is_active ?? true,
                    'sub_ledger_type' => 'bank',
                ]);
                $bankAccount->chart_of_account_id = $coa->id;
            }
        });

        static::updating(function ($bankAccount) {
            if ($bankAccount->isDirty(['bank_name', 'name_ar', 'is_active', 'account_type'])) {
                $coa = ChartOfAccount::find($bankAccount->chart_of_account_id);
                if ($coa) {
                    $parentCode = ($bankAccount->account_type === 'cash') ? '1101' : '1102';
                    $parent = ChartOfAccount::where('code', $parentCode)->first();

                    $coa->update([
                        'name_en' => $bankAccount->bank_name ?: ($bankAccount->account_type === 'cash' ? 'Cash Account' : 'Bank Account'),
                        'name_ar' => $bankAccount->name_ar,
                        'is_active' => $bankAccount->is_active,
                        'parent_id' => $parent ? $parent->id : $coa->parent_id,
                    ]);
                }
            }
        });

        static::deleted(function ($bankAccount) {
            if ($bankAccount->chart_of_account_id) {
                ChartOfAccount::find($bankAccount->chart_of_account_id)?->delete();
            }
        });
    }

    protected $fillable = [
        'company_id',
        'code',
        'name_en',
        'name_ar',
        'bank_name',
        'account_number',
        'iban',
        'currency_code',
        'account_type',
        'opening_balance',
        'current_balance',
        'chart_of_account_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
