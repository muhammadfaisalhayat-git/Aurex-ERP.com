<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'code',
        'description',
        'is_active',
    ];

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }
}
