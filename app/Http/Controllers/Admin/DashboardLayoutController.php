<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardLayoutController extends Controller
{
    public function index()
    {
        return view('admin.dashboard-layout.index');
    }

    public function store(Request $request)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
