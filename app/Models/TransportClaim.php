<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
class TransportClaim extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'claim_number', 'claim_date', 'transport_order_id', 'claim_type', 'description', 'claim_amount', 'status', 'evidence_files', 'resolution_notes', 'settled_amount', 'created_by', 'reviewed_by', 'reviewed_at', 'settled_by', 'settled_at'];
    protected $casts = ['claim_date' => 'date', 'claim_amount' => 'decimal:2', 'settled_amount' => 'decimal:2', 'reviewed_at' => 'datetime', 'settled_at' => 'datetime'];
    public function transportOrder()
    {
        return $this->belongsTo(TransportOrder::class);
    }
}
