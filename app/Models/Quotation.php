<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Quotation extends Model { use HasFactory, SoftDeletes; protected $fillable = ['document_number','quotation_date','expiry_date','customer_id','branch_id','warehouse_id','salesman_id','status','version','parent_quotation_id','subtotal','discount_amount','tax_rate','tax_amount','total_amount','terms_conditions','notes','created_by','converted_by','converted_at','converted_to_id','converted_to_type']; protected $casts = ['quotation_date'=>'date','expiry_date'=>'date','converted_at'=>'datetime','subtotal'=>'decimal:2','discount_amount'=>'decimal:2','tax_rate'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2']; public function customer() { return $this->belongsTo(Customer::class); } public function items() { return $this->hasMany(QuotationItem::class); } }
