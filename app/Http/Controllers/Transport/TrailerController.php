<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
    public function index()
    {
        $trailers = \App\Models\Trailer::paginate(10);
        return view('transport.trailers.index', compact('trailers'));
    }

    public function create()
    {
        return view('transport.trailers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:trailers,code',
            'plate_number' => 'required|string|unique:trailers,plate_number',
            'trailer_type' => 'required|string',
            'capacity_kg' => 'required|numeric|min:0',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'license_number' => 'nullable|string',
            'license_expiry' => 'nullable|date',
            'status' => 'required|in:available,busy,maintenance,retired',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        \App\Models\Trailer::create($validated);

        return redirect()->route('transport.trailers.index')
            ->with('success', __('messages.trailer_created_successfully'));
    }

    public function edit(\App\Models\Trailer $trailer)
    {
        return view('transport.trailers.edit', compact('trailer'));
    }

    public function update(Request $request, \App\Models\Trailer $trailer)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:trailers,code,' . $trailer->id,
            'plate_number' => 'required|string|unique:trailers,plate_number,' . $trailer->id,
            'trailer_type' => 'required|string',
            'capacity_kg' => 'required|numeric|min:0',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'license_number' => 'nullable|string',
            'license_expiry' => 'nullable|date',
            'status' => 'required|in:available,busy,maintenance,retired',
            'is_active' => 'boolean',
        ]);

        $trailer->update($validated);

        return redirect()->route('transport.trailers.index')
            ->with('success', __('messages.trailer_updated_successfully'));
    }

    public function destroy(\App\Models\Trailer $trailer)
    {
        $trailer->delete();
        return redirect()->route('transport.trailers.index')
            ->with('success', __('messages.trailer_deleted_successfully'));
    }
}
