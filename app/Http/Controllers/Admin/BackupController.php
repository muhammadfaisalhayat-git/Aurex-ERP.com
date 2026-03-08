<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage backups');
    }

    public function index()
    {
        if (BackupLog::count() === 0) {
            $this->seedDummyData();
        }

        $backups = BackupLog::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('acp.system.backup.index', compact('backups'));
    }

    public function create(Request $request)
    {
        // Simulate creating a backup
        $filename = 'backup_' . date('Y_m_d_His') . '.zip';

        $backup = BackupLog::create([
            'filename' => $filename,
            'size_bytes' => rand(10000000, 500000000),
            'disk' => 'local',
            'status' => 'completed',
            'notes' => $request->get('notes', 'Manual backup'),
            'created_by' => auth()->id(),
        ]);

        AuditLog::log('create', 'backup', $backup->id, null, $backup->toArray());

        return redirect()->route('acp.system.backup.index')
            ->with('success', __('messages.sm_backup_created'));
    }

    public function download(BackupLog $backup)
    {
        // In production, this would stream the actual file
        return back()->with('info', __('messages.sm_backup_download_simulated', ['filename' => $backup->filename]));
    }

    private function seedDummyData()
    {
        $statuses = ['completed', 'completed', 'completed', 'failed'];
        for ($i = 0; $i < 8; $i++) {
            BackupLog::create([
                'filename' => 'backup_' . now()->subDays($i)->format('Y_m_d_His') . '.zip',
                'size_bytes' => rand(10000000, 500000000),
                'disk' => $i % 3 === 0 ? 'google' : 'local',
                'status' => $statuses[array_rand($statuses)],
                'notes' => $i === 0 ? 'Scheduled daily backup' : ($i % 2 === 0 ? 'Manual backup' : 'Scheduled daily backup'),
                'created_by' => \App\Models\User::inRandomOrder()->first()?->id,
            ]);
        }
    }
}
