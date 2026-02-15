<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
class Trailer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'code', 'plate_number', 'trailer_type', 'capacity_kg', 'driver_name', 'driver_phone', 'license_number', 'license_expiry', 'status', 'is_active'];
    protected $casts = ['license_expiry' => 'date', 'capacity_kg' => 'decimal:2', 'is_active' => 'boolean'];
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
