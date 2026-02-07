<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class MaintenanceVoucherPart extends Model { use HasFactory; protected $fillable = ['maintenance_voucher_id','product_id','quantity','unit_cost','total_cost','issued_by','issued_at']; protected $casts = ['quantity'=>'decimal:3','unit_cost'=>'decimal:4','total_cost'=>'decimal:2','issued_at'=>'datetime']; public function voucher() { return $this->belongsTo(MaintenanceVoucher::class); } public function product() { return $this->belongsTo(Product::class); } }
