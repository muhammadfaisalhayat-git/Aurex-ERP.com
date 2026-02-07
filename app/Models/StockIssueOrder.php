<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StockIssueOrder extends Model { use HasFactory, SoftDeletes; protected $fillable = ['document_number','issue_date','warehouse_id','reference_type','reference_id','reference_number','issue_type','status','notes','created_by','posted_by','posted_at']; protected $casts = ['issue_date'=>'date','posted_at'=>'datetime']; public function warehouse() { return $this->belongsTo(Warehouse::class); } public function items() { return $this->hasMany(StockIssueOrderItem::class); } public function isPosted() { return $this->status==='posted'; } }
