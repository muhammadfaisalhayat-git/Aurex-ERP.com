<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TransportContract extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'contract_number', 'contract_date', 'start_date', 'end_date', 'contractor_name', 'contractor_phone', 'contract_value', 'status', 'terms_conditions', 'created_by', 'closed_by', 'closed_at'];
    protected $casts = ['contract_date' => 'date', 'start_date' => 'date', 'end_date' => 'date', 'closed_at' => 'datetime', 'contract_value' => 'decimal:2'];
}
