<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PurchaseInvoiceItem extends Model { use HasFactory; protected $fillable = ['purchase_invoice_id','product_id','description','quantity','unit_price','discount_percentage','discount_amount','tax_rate','tax_amount','total_amount','notes']; protected $casts = ['quantity'=>'decimal:3','unit_price'=>'decimal:4','discount_percentage'=>'decimal:2','discount_amount'=>'decimal:2','tax_rate'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2']; public function purchaseInvoice() { return $this->belongsTo(PurchaseInvoice::class); } public function product() { return $this->belongsTo(Product::class); } }
