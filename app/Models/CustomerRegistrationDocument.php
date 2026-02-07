<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRegistrationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_registration_id',
        'document_type',
        'document_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function customerRegistration()
    {
        return $this->belongsTo(CustomerRegistration::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDocumentTypeLabelAttribute()
    {
        $labels = [
            'id_card' => 'ID Card',
            'cr_certificate' => 'Commercial Registration',
            'tax_certificate' => 'Tax Certificate',
            'vat_certificate' => 'VAT Certificate',
            'bank_statement' => 'Bank Statement',
            'authorization_letter' => 'Authorization Letter',
            'other' => 'Other Document',
        ];

        return $labels[$this->document_type] ?? $this->document_type;
    }
}
