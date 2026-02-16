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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:simple,composite,service',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Product::create($validated);

        return redirect()->route('inventory.products.index')
            ->with('success', __('messages.product_created'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:simple,composite,service',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('inventory.products.index')
            ->with('success', __('messages.product_updated'));
    }

    public function destroy(Product $product)
    {
        // Check for dependencies if necessary, but SoftDeletes is used
        $product->delete();

        return redirect()->route('inventory.products.index')
            ->with('success', __('messages.product_deleted'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $products = Product::where('is_sellable', true)
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
