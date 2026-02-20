<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MaintenanceVoucher extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'voucher_number', 'voucher_date', 'workshop_id', 'customer_id', 'vendor_id', 'entity_type', 'entity_id', 'entity_name', 'maintenance_type', 'problem_description', 'work_description', 'scheduled_date', 'completion_date', 'status', 'estimated_cost', 'actual_cost', 'technician_name', 'notes', 'created_by', 'completed_by', 'completed_at'];
    protected $casts = ['voucher_date' => 'date', 'scheduled_date' => 'date', 'completion_date' => 'date', 'completed_at' => 'datetime', 'estimated_cost' => 'decimal:2', 'actual_cost' => 'decimal:2'];
    public function workshop()
    {
        return $this->belongsTo(MaintenanceWorkshop::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function parts()
    {
        return $this->hasMany(MaintenanceVoucherPart::class);
    }
}
