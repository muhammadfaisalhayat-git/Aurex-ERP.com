<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class RestoreBackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:restore backups');
    }

    public function index()
    {
        $backups = BackupLog::where('status', 'completed')
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('acp.system.restore-backup.index', compact('backups'));
    }

    public function restore(BackupLog $backup)
    {
        // Simulate restore — in production this would actually restore
        AuditLog::log('restore', 'backup', $backup->id, null, [
            'filename' => $backup->filename,
            'restored_at' => now()->toDateTimeString(),
        ]);

        return back()->with('success', __('messages.sm_backup_restored', ['filename' => $backup->filename]));
    }
}
