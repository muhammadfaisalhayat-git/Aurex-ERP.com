<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceWorkshop;

class MaintenanceWorkshopController extends Controller
{
    public function index()
    {
        $workshops = MaintenanceWorkshop::latest()->paginate(10);
        return view('maintenance.workshops.index', compact('workshops'));
    }
}
