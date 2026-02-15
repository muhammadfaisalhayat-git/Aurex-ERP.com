<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Display the barcode generator UI.
     */
    public function index()
    {
        return view('inventory.barcodes.index');
    }

    /**
     * AJAX search for products.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name_en', 'LIKE', "%{$query}%")
                    ->orWhere('name_ar', 'LIKE', "%{$query}%")
                    ->orWhere('code', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();

        // Map to include both real name and code for frontend compatibility
        $mapped = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'product_code' => $p->code,
                'sale_price' => $p->sale_price
            ];
        });

        return response()->json($mapped);
    }

    /**
     * Print the barcodes.
     */
    public function print(Request $request)
    {
        $items = $request->get('items', []);

        if (empty($items)) {
            return redirect()->back()->with('error', __('messages.no_items_selected'));
        }

        $barcodeData = [];
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                for ($i = 0; $i < ($item['quantity'] ?? 1); $i++) {
                    $barcodeData[] = [
                        'name' => $product->name,
                        'code' => $product->code,
                        'price' => $product->sale_price,
                    ];
                }
            }
        }

        return view('inventory.barcodes.print', compact('barcodeData'));
    }
}
