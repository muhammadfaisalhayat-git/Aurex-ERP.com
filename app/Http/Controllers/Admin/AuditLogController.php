<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->filled('entity_type')) {
            $query->where('entity_type', 'like', '%' . $request->entity_type . '%');
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();
        
        // Get unique actions and entities for filters
        $actions = AuditLog::distinct()->pluck('action');
        $entities = AuditLog::distinct()->whereNotNull('entity_type')->pluck('entity_type');

        return view('admin.audit_logs.index', compact('logs', 'users', 'actions', 'entities'));
    }
}
