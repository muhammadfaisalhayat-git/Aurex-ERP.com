<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaintenanceVoucherController extends Controller
{
    public function index()
    {
        return view('maintenance.vouchers.index');
    }

    public function create()
    {
        return view('maintenance.vouchers.create');
    }

    public function show($id)
    {
        return view('maintenance.vouchers.show', compact('id'));
    }
}
