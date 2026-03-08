<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

use App\Traits\BelongsToTenant;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'employee_code',
        'branch_id',
        'default_language',
        'theme',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'password_reset_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'user_warehouse');
    }

    public function dashboardWidgets()
    {
        return $this->hasMany(DashboardWidget::class);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
