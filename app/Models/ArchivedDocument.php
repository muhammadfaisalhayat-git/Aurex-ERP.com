<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'document_id',
        'original_number',
        'file_path',
        'archived_by',
        'archived_at',
        'notes',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
