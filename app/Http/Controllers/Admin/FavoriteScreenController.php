<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FavoriteScreen;
use Illuminate\Http\Request;

class FavoriteScreenController extends Controller
{
    public function index()
    {
        $favorites = FavoriteScreen::where('user_id', auth()->id())
            ->orderBy('sort_order')
            ->get();

        // Available screens for adding
        $availableScreens = $this->getAvailableScreens();

        return view('acp.system.favorite-screens.index', compact('favorites', 'availableScreens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
        ]);

        $maxSort = FavoriteScreen::where('user_id', auth()->id())->max('sort_order') ?? 0;

        FavoriteScreen::updateOrCreate(
            ['user_id' => auth()->id(), 'route_name' => $validated['route_name']],
            [
                'label' => $validated['label'],
                'icon' => $validated['icon'] ?? 'fas fa-star',
                'sort_order' => $maxSort + 1,
            ]
        );

        return back()->with('success', __('messages.sm_favorite_added'));
    }

    public function destroy(FavoriteScreen $favoriteScreen)
    {
        if ($favoriteScreen->user_id !== auth()->id()) {
            abort(403);
        }

        $favoriteScreen->delete();
        return back()->with('success', __('messages.sm_favorite_removed'));
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:favorite_screens,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            FavoriteScreen::where('id', $id)
                ->where('user_id', auth()->id())
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    private function getAvailableScreens()
    {
        return [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
            ['route' => 'sales.customers.index', 'label' => 'Customers', 'icon' => 'fas fa-users'],
            ['route' => 'sales.invoices.index', 'label' => 'Sales Invoices', 'icon' => 'fas fa-file-invoice-dollar'],
            ['route' => 'sales.quotations.index', 'label' => 'Quotations', 'icon' => 'fas fa-file-alt'],
            ['route' => 'purchases.vendors.index', 'label' => 'Vendors', 'icon' => 'fas fa-truck'],
            ['route' => 'purchases.invoices.index', 'label' => 'Purchase Invoices', 'icon' => 'fas fa-shopping-cart'],
            ['route' => 'inventory.products.index', 'label' => 'Products', 'icon' => 'fas fa-boxes'],
            ['route' => 'inventory.stock-ledger.index', 'label' => 'Stock Ledger', 'icon' => 'fas fa-list-alt'],
            ['route' => 'hr.employees.index', 'label' => 'Employees', 'icon' => 'fas fa-id-badge'],
            ['route' => 'accounting.gl.coa.index', 'label' => 'Chart of Accounts', 'icon' => 'fas fa-sitemap'],
            ['route' => 'accounting.gl.transactions.jv.index', 'label' => 'Journal Vouchers', 'icon' => 'fas fa-file-invoice'],
            ['route' => 'reports.sales.index', 'label' => 'Sales Reports', 'icon' => 'fas fa-chart-line'],
        ];
    }
}
