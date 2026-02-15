<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::paginate(20);
        return view('inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('inventory.categories.create');
    }

    public function show(ProductCategory $category)
    {
        return view('inventory.categories.show', compact('category'));
    }

    public function edit(ProductCategory $category)
    {
        return view('inventory.categories.edit', compact('category'));
    }
}
