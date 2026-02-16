<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Services\InvoiceOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceOcrController extends Controller
{
    protected $ocrService;

    public function __construct(InvoiceOcrService $ocrService)
    {
        $this->ocrService = $ocrService;
        $this->middleware('auth');
    }

    /**
     * Extract data from uploaded invoice image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extractData(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'invoice_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            ]);

            $file = $request->file('invoice_image');

            // Validate file
            $validation = $this->ocrService->validateFile($file);
            if ($validation !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $validation,
                ], 422);
            }

            // Store file temporarily
            $filepath = $this->ocrService->storeUploadedFile($file);

            // Extract data
            $result = $this->ocrService->extractInvoiceData($filepath);

            // Add path to result for later persistence
            if ($result['success']) {
                $result['temp_file'] = basename($filepath);
            }

            // Log extraction
            if ($result['success']) {
                Log::info('Invoice OCR extraction successful', [
                    'file' => $file->getClientOriginalName(),
                    'extracted_fields' => array_keys($result['data']),
                ]);
            } else {
                // If failed, clean up
                $this->ocrService->deleteTemporaryFile($filepath);
                Log::warning('Invoice OCR extraction failed', [
                    'file' => $file->getClientOriginalName(),
                    'error' => $result['error'],
                ]);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('OCR extraction error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred during invoice processing. Please try again.',
            ], 500);
        }
    }
}
