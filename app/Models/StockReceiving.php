<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

use Illuminate\Support\Facades\DB;

class StockReceiving extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $table = 'stock_receiving';
    protected $fillable = ['company_id', 'document_number', 'receiving_date', 'warehouse_id', 'vendor_id', 'customer_id', 'purchase_order_number', 'delivery_note_number', 'reference_type', 'reference_id', 'status', 'notes', 'created_by', 'received_by', 'received_at'];
    protected $casts = ['receiving_date' => 'date', 'received_at' => 'datetime'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function items()
    {
        return $this->hasMany(StockReceivingItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class , 'received_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function post()
    {
        if ($this->status === 'received') {
            return false;
        }

        return DB::transaction(function () {
            // If items have no received_quantity, set to ordered_quantity
            foreach ($this->items as $item) {
                if ($item->received_quantity <= 0) {
                    $item->update(['received_quantity' => $item->ordered_quantity]);
                }
            }

            $this->update([
                'status' => 'received',
                'received_at' => now(),
                'received_by' => auth()->id(),
            ]);

            $stockService = app(\App\Services\StockManagementService::class);

            // Record movements
            foreach ($this->items as $item) {
                $stockService->recordMovement([
                    'product_id' => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'warehouse_id' => $this->warehouse_id,
                    'movement_type' => 'in',
                    'quantity' => $item->received_quantity,
                    'unit_cost' => $item->product->average_cost ?? 0,
                    'reference_type' => 'stock_receiving',
                    'reference_id' => $this->id,
                    'reference_number' => $this->document_number,
                    'notes' => 'Stock Receiving: ' . $this->document_number
                ]);
            }

            // Accounting integration
            app(\App\Services\AccountingService::class)->postStockReceiving($this);

            return true;
        });
    }

    public function unpost()
    {
        if ($this->status !== 'received') {
            return false;
        }

        return DB::transaction(function () {
            $stockService = app(\App\Services\StockManagementService::class);
            $stockService->reverseMovement('stock_receiving', $this->id);

            $this->update([
                'status' => 'pending',
                'received_at' => null,
                'received_by' => null,
            ]);

            // Reverse accounting entries
            app(\App\Services\AccountingService::class)->unpostDocument('stock_receiving', $this->id);
            return true;
        });
    }
}
