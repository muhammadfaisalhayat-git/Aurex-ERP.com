<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class QuotationItem extends Model { use HasFactory; protected $fillable = ['quotation_id','product_id','description','quantity','unit_price','discount_percentage','discount_amount','tax_rate','tax_amount','net_amount','gross_amount','notes']; protected $casts = ['quantity'=>'decimal:3','unit_price'=>'decimal:4','discount_percentage'=>'decimal:2','discount_amount'=>'decimal:2','tax_rate'=>'decimal:2','tax_amount'=>'decimal:2','net_amount'=>'decimal:2','gross_amount'=>'decimal:2']; public function quotation() { return $this->belongsTo(Quotation::class); } public function product() { return $this->belongsTo(Product::class); } }
