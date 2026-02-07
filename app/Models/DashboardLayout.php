<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DashboardLayout extends Model { use HasFactory; protected $fillable = ['name','role_id','user_id','is_default','layout_config']; protected $casts = ['layout_config'=>'json','is_default'=>'boolean']; public function role() { return $this->belongsTo(\Spatie\Permission\Models\Role::class); } public function user() { return $this->belongsTo(User::class); } }
