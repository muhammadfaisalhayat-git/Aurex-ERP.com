<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\VendorDocument;
use App\Services\ProductMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseAIController extends Controller
{
    protected $productMatcher;

    public function __construct(ProductMatcher $productMatcher)
    {
        $this->productMatcher = $productMatcher;
    }

    /**
     * Scan an uploaded invoice image and return extracted data.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'invoice_image' => 'required|image|max:5120', // Max 5MB
        ]);

        try {
            // Store the invoice image
            $file = $request->file('invoice_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('vendor_invoices/temp', $fileName, 'public');

            // In a real implementation, you would:
            // 1. Call an OCR/Vision API (Google Cloud Vision, AWS Textract, OpenAI GPT-4o)
            // 2. Parse the result into a structured format.
            // For now, we'll simulate with mock data

            // Simulate AI processing delay
            usleep(1500000); // 1.5 seconds

            // Mock extracted data - in real implementation, this comes from AI
            $extractedData = $this->simulateAIExtraction($file->getClientOriginalName());

            // Process items and match/create products
            $processedItems = [];
            foreach ($extractedData['items'] as $item) {
                $product = $this->productMatcher->findOrCreateProduct(
                    $item['description'],
                    $item['unit_price'],
                    $item['unit_price'] // Use unit_price as cost price for now
                );

                $processedItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name_en,
                    'product_code' => $product->code,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 15,
                    'discount_amount' => 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'supplier_name' => $extractedData['supplier_name'],
                    'invoice_number' => $extractedData['invoice_number'],
                    'invoice_date' => $extractedData['invoice_date'],
                    'items' => $processedItems,
                    'image_path' => $filePath,
                    'detected_text' => $extractedData['detected_text'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Invoice Scan Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.scan_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save scanned invoice image to vendor profile.
     */
    public function saveToVendor(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'image_path' => 'required|string',
            'invoice_number' => 'nullable|string',
        ]);

        try {
            // Move file from temp to vendor-specific folder
            $tempPath = $request->image_path;
            $vendorId = $request->vendor_id;

            // Create new path
            $fileName = basename($tempPath);
            $newPath = "vendor_invoices/{$vendorId}/{$fileName}";

            // Move file
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->move($tempPath, $newPath);
            }

            // Create vendor document record
            VendorDocument::create([
                'vendor_id' => $vendorId,
                'document_type' => 'invoice',
                'file_path' => $newPath,
                'original_filename' => $fileName,
                'notes' => $request->invoice_number ? "Invoice #: {$request->invoice_number}" : null,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.invoice_saved_to_vendor')
            ]);

        } catch (\Exception $e) {
            Log::error('Save Invoice to Vendor Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.save_failed')
            ], 500);
        }
    }

    /**
     * Simulate AI extraction - Replace this with real AI service integration.
     */
    protected function simulateAIExtraction(string $fileName): array
    {
        // Check filename for hints
        $lowerName = strtolower($fileName);
        $isRiyadh = Str::contains($lowerName, 'riyadh');
        $isJeddah = Str::contains($lowerName, 'jeddah');

        // Simulate different invoice contents based on filename
        return [
            'supplier_name' => $isRiyadh ? 'Riyadh Supply Co.' : ($isJeddah ? 'Jeddah Trading Ltd' : 'Vendor One Solutions'),
            'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
            'invoice_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'description' => 'Industrial Gloves',
                    'quantity' => 10,
                    'unit_price' => 25.00,
                    'tax_rate' => 15,
                ],
                [
                    'description' => 'Safety Boots',
                    'quantity' => 5,
                    'unit_price' => 120.00,
                    'tax_rate' => 15,
                ]
            ],
            'detected_text' => 'Simulated OCR result from ' . $fileName,
        ];
    }
}