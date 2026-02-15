<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view inventory')->only(['index', 'show', 'bom']);
        $this->middleware('can:create inventory')->only(['create', 'store', 'createBom', 'storeBom']);
        $this->middleware('can:edit inventory')->only(['edit', 'update', 'editBom', 'updateBom']);
        $this->middleware('can:delete inventory')->only(['destroy']);
    }
    public function index()
    {
        $products = Product::with('category')->paginate(20);
        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        return view('inventory.products.create', compact('categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'bomComponents.component']);
        return view('inventory.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function bom(Product $product)
    {
        $product->load('bomComponents.component');
        return view('inventory.products.bom', compact('product'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $products = Product::where('is_purchasable', true)
            ->where(function ($query) use ($search) {
                $query->where('name_en', 'like', "%$search%")
                    ->orWhere('name_ar', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            })
            ->limit(10)
            ->get(['id', 'code', 'name_en', 'name_ar', 'sale_price', 'cost_price']);

        return response()->json($products);
    }
}
