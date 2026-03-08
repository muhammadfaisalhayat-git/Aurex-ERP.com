<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'size_bytes',
        'disk',
        'status',
        'notes',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size_bytes;
        if ($bytes >= 1073741824)
            return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)
            return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)
            return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
