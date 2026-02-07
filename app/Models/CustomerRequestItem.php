<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CustomerRequestItem extends Model { use HasFactory; protected $fillable = ['customer_request_id','product_id','quantity','notes']; protected $casts = ['quantity'=>'decimal:3']; public function customerRequest() { return $this->belongsTo(CustomerRequest::class); } public function product() { return $this->belongsTo(Product::class); } }
