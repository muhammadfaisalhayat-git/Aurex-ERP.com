<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['branch', 'roles', 'company'])->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('employee_code', 'like', "%{$s}%");
            });
        }
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->paginate(20);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();

        return view('acp.system.user-details.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load(['branch', 'roles', 'company', 'warehouses']);
        return view('acp.system.user-details.show', compact('user'));
    }

    public function visitingCardPdf(User $user)
    {
        $user->load(['branch', 'roles', 'company']);

        $pdf = Pdf::loadView('acp.system.user-details.visiting-card', compact('user'))
            ->setPaper([0, 0, 255.12, 153.07]) // 90mm x 54mm exactly in points
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Helvetica',
            ]);

        return $pdf->download('visiting-card-' . str()->slug($user->name) . '.pdf');
    }
}
