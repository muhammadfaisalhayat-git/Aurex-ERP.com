<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MaintenanceWorkshop extends Model { use HasFactory, SoftDeletes; protected $fillable = ['code','name_en','name_ar','address','phone','email','manager_name','workshop_type','is_active','notes']; protected $casts = ['is_active'=>'boolean']; public function scopeActive($query) { return $query->where('is_active',true); } public function getNameAttribute() { return app()->getLocale()==='ar'?$this->name_ar:$this->name_en; } }
