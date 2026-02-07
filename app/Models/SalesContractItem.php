<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SalesContractItem extends Model { use HasFactory; protected $fillable = ['sales_contract_id','product_id','quantity','unit_price','total_amount','notes']; protected $casts = ['quantity'=>'decimal:3','unit_price'=>'decimal:4','total_amount'=>'decimal:2']; public function salesContract() { return $this->belongsTo(SalesContract::class); } public function product() { return $this->belongsTo(Product::class); } }
