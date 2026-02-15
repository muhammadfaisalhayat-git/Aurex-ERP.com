<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PurchaseAIController extends Controller
{
    /**
     * Scan an uploaded invoice image and return extracted data.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'invoice_image' => 'required|image|max:5120', // Max 5MB
        ]);

        try {
            // In a real implementation, you would:
            // 1. Store the file: $path = $request->file('invoice_image')->store('temp/scans');
            // 2. Call an OCR/Vision API (Google Cloud Vision, AWS Textract, OpenAI GPT-4o)
            // 3. Parse the result into a structured format.

            // Simulate AI processing delay
            usleep(1500000); // 1.5 seconds

            // Mock Data Generation
            $fileName = $request->file('invoice_image')->getClientOriginalName();

            // Generate some random details based on filename or just generic defaults
            $isRiyadh = Str::contains(Str::lower($fileName), 'riyadh');
            $isJeddah = Str::contains(Str::lower($fileName), 'jeddah');

            $mockData = [
                'success' => true,
                'data' => [
                    'supplier_name' => $isRiyadh ? 'Riyadh Supply Co.' : ($isJeddah ? 'Jeddah Trading Ltd' : 'Vendor One Solutions'),
                    'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
                    'invoice_date' => now()->format('Y-m-d'),
                    'items' => [
                        [
                            'description' => 'Industrial Gloves (Example)',
                            'quantity' => 10,
                            'unit_price' => 25.00,
                            'tax_rate' => 15,
                        ],
                        [
                            'description' => 'Safety Boots (Example)',
                            'quantity' => 5,
                            'unit_price' => 120.00,
                            'tax_rate' => 15,
                        ]
                    ],
                    'detected_text' => 'Simulated OCR result from ' . $fileName,
                ]
            ];

            return response()->json($mockData);

        } catch (\Exception $e) {
            Log::error('AI Invoice Scan Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.scan_failed')
            ], 500);
        }
    }
}
