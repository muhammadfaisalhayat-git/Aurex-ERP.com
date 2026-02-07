<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TransportOrderItem extends Model { use HasFactory; protected $fillable = ['transport_order_id','product_id','quantity','notes']; protected $casts = ['quantity'=>'decimal:3']; }
