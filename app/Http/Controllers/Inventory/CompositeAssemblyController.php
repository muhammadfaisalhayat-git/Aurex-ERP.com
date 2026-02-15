<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompositeAssemblyController extends Controller
{
    public function index()
    {
        return view('inventory.composite-assemblies.index');
    }

    public function create()
    {
        return view('inventory.composite-assemblies.create');
    }
}
