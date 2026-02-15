<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PurchaseInvoice::with(['vendor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vq) use ($search) {
                        $vq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(10);

        return view('purchases.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('purchases.invoices.create');
    }

    public function show($id)
    {
        return view('purchases.invoices.show', compact('id'));
    }

    public function post($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }

    public function unpost($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
