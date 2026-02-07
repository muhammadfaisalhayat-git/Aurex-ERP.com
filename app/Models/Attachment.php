<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Attachment extends Model { use HasFactory; protected $fillable = ['entity_type','entity_id','file_name','original_name','file_path','mime_type','file_size','description','uploaded_by']; protected $casts = ['file_size'=>'integer']; public function uploader() { return $this->belongsTo(User::class,'uploaded_by'); } }
