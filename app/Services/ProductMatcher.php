<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductMatcher
{
    /**
     * Find or create a product based on the scanned name and price.
     * 
     * @param string $productName The name from the scanned invoice
     * @param float $unitPrice The price from the scanned invoice
     * @param float|null $purchasePrice Optional purchase/cost price
     * @return Product The found or newly created product
     */
    public function findOrCreateProduct(string $productName, float $unitPrice, ?float $purchasePrice = null): Product
    {
        // First, try exact match
        $product = Product::where('name_en', $productName)
            ->orWhere('name_ar', $productName)
            ->first();

        if ($product) {
            return $product;
        }

        // Try fuzzy matching (products with similar names)
        $product = $this->findSimilarProduct($productName);

        if ($product) {
            return $product;
        }

        // Product doesn't exist, create a new one
        return $this->createProduct($productName, $unitPrice, $purchasePrice);
    }

    /**
     * Find a product with a similar name using fuzzy matching.
     * 
     * @param string $productName
     * @return Product|null
     */
    protected function findSimilarProduct(string $productName): ?Product
    {
        // Get all active products
        $products = Product::where('is_active', true)->get(['id', 'name_en', 'name_ar']);

        $highestSimilarity = 0;
        $matchedProduct = null;

        foreach ($products as $product) {
            // Check similarity with English name
            $similarityEn = $this->calculateSimilarity($productName, $product->name_en);

            // Check similarity with Arabic name
            $similarityAr = $this->calculateSimilarity($productName, $product->name_ar ?? '');

            $similarity = max($similarityEn, $similarityAr);

            // If similarity is above 80%, consider it a match
            if ($similarity > 80 && $similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $matchedProduct = $product;
            }
        }

        // Return the matched product if found, refresh to get all fields
        return $matchedProduct ? Product::find($matchedProduct->id) : null;
    }

    /**
     * Calculate similarity percentage between two strings.
     * 
     * @param string $str1
     * @param string $str2
     * @return float Similarity percentage (0-100)
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        // Normalize strings
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));

        if (empty($str1) || empty($str2)) {
            return 0;
        }

        // Use levenshtein distance for short strings
        if (strlen($str1) < 255 && strlen($str2) < 255) {
            $distance = levenshtein($str1, $str2);
            $maxLen = max(strlen($str1), strlen($str2));

            if ($maxLen == 0) {
                return 100;
            }

            return (1 - ($distance / $maxLen)) * 100;
        }

        // For longer strings, use similar_text
        similar_text($str1, $str2, $percent);
        return $percent;
    }

    /**
     * Create a new product from scanned invoice data.
     * 
     * @param string $productName
     * @param float $unitPrice
     * @param float|null $purchasePrice
     * @return Product
     */
    protected function createProduct(string $productName, float $unitPrice, ?float $purchasePrice = null): Product
    {
        // Generate a unique product code
        $code = $this->generateProductCode();

        // Create the product
        $product = Product::create([
            'code' => $code,
            'name_en' => $productName,
            'name_ar' => $productName, // Use same name for both languages initially
            'description_en' => 'Auto-created from invoice scan',
            'description_ar' => 'تم إنشاؤه تلقائياً من مسح الفاتورة',
            'sale_price' => $unitPrice,
            'cost_price' => $purchasePrice ?? $unitPrice,
            'purchase_price' => $purchasePrice ?? $unitPrice,
            'is_active' => true,
            'is_sellable' => true,
            'is_purchasable' => true,
            'track_stock' => true,
            'min_stock_level' => 0,
            'max_stock_level' => 9999,
        ]);

        return $product;
    }

    /**
     * Generate a unique product code.
     * 
     * @return string
     */
    protected function generateProductCode(): string
    {
        $prefix = 'PROD-AI-';
        $lastProduct = Product::where('code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastProduct || !preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $lastProduct->code, $matches)) {
            return $prefix . '001';
        }

        $nextNumber = intval($matches[1]) + 1;
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
