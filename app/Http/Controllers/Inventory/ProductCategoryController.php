<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::with('parent')->paginate(20);
        return view('inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        return view('inventory.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:product_categories,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ProductCategory::create($validated);

        return redirect()->route('inventory.categories.index')
            ->with('success', __('messages.category_created'));
    }

    public function show(ProductCategory $category)
    {
        return view('inventory.categories.show', compact('category'));
    }

    public function edit(ProductCategory $category)
    {
        $categories = ProductCategory::where('id', '!=', $category->id)->get();
        return view('inventory.categories.create', compact('category', 'categories'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'code' => 'required|unique:product_categories,code,' . $category->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id|not_in:' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('inventory.categories.index')
            ->with('success', __('messages.category_updated'));
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->with('error', __('messages.category_has_dependencies'));
        }

        $category->delete();

        return redirect()->route('inventory.categories.index')
            ->with('success', __('messages.category_deleted'));
    }
}
