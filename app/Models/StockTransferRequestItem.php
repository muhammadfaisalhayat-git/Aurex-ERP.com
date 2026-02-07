<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class StockTransferRequestItem extends Model { use HasFactory; protected $fillable = ['stock_transfer_request_id','product_id','quantity','notes']; protected $casts = ['quantity'=>'decimal:3']; }
