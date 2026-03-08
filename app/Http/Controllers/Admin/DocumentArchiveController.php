<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivedDocument;
use Illuminate\Http\Request;

class DocumentArchiveController extends Controller
{
    public function index(Request $request)
    {
        if (ArchivedDocument::count() === 0) {
            $this->seedDummyData();
        }

        $query = ArchivedDocument::with('archiver')->orderBy('archived_at', 'desc');

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }
        if ($request->filled('search')) {
            $query->where('original_number', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('archived_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('archived_at', '<=', $request->date_to);
        }

        $documents = $query->paginate(20);
        $types = ArchivedDocument::select('document_type')->distinct()->pluck('document_type');

        return view('acp.system.document-archive.index', compact('documents', 'types'));
    }

    public function show(ArchivedDocument $archivedDocument)
    {
        $archivedDocument->load('archiver');
        return view('acp.system.document-archive.show', compact('archivedDocument'));
    }

    private function seedDummyData()
    {
        $types = [
            'Sales Invoice' => ['SI-2026-001', 'SI-2026-002', 'SI-2026-003', 'SI-2025-098', 'SI-2025-099'],
            'Purchase Invoice' => ['PI-2026-001', 'PI-2025-045', 'PI-2025-046'],
            'Quotation' => ['QT-2026-001', 'QT-2026-002', 'QT-2025-120'],
            'Journal Voucher' => ['JV-2026-001', 'JV-2025-200', 'JV-2025-201'],
            'Sales Contract' => ['SC-2025-010', 'SC-2025-011'],
        ];

        $userId = \App\Models\User::first()?->id;

        foreach ($types as $type => $numbers) {
            foreach ($numbers as $i => $number) {
                ArchivedDocument::create([
                    'document_type' => $type,
                    'document_id' => $i + 1,
                    'original_number' => $number,
                    'file_path' => 'archives/' . strtolower(str_replace(' ', '_', $type)) . '/' . $number . '.pdf',
                    'archived_by' => $userId,
                    'archived_at' => now()->subDays(rand(1, 90)),
                    'notes' => rand(0, 1) ? 'End of fiscal year archiving' : null,
                ]);
            }
        }
    }
}
