<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaintenanceWorkshopController extends Controller
{
    public function index()
    {
        return view('maintenance.workshops.index');
    }
}
