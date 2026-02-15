<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TransportOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'document_number', 'order_date', 'trailer_id', 'branch_id', 'route_from', 'route_to', 'scheduled_date', 'completion_date', 'status', 'reference_type', 'reference_id', 'notes', 'created_by', 'closed_by', 'closed_at'];
    protected $casts = ['order_date' => 'date', 'scheduled_date' => 'date', 'completion_date' => 'date', 'closed_at' => 'datetime'];
    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }
    public function items()
    {
        return $this->hasMany(TransportOrderItem::class);
    }
}
