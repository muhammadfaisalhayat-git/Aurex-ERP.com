<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendor;
use App\Models\Product;

class InvoiceOcrService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('ocr');
    }

    /**
     * Extract data from invoice image
     *
     * @param string $imagePath
     * @return array
     */
    public function extractInvoiceData($imagePath)
    {
        try {
            // Perform OCR
            $text = $this->performOcr($imagePath);

            if (empty($text)) {
                throw new \Exception('No text could be extracted from the image');
            }

            // Parse the extracted text
            $data = $this->parseInvoiceText($text);

            return [
                'success' => true,
                'data' => $data,
                'raw_text' => $text,
            ];

        } catch (\Exception $e) {
            Log::error('OCR extraction failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Perform OCR on image
     *
     * @param string $imagePath
     * @return string
     */
    protected function performOcr($imagePath)
    {
        try {
            $ocr = new TesseractOCR($imagePath);

            // Set language
            $ocr->lang($this->config['languages']);

            // Set Tesseract path if configured
            if (!empty($this->config['tesseract_path'])) {
                $ocr->executable($this->config['tesseract_path']);
            }

            // Run OCR
            return $ocr->run();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('Tesseract Error: ' . $message);

            // Check if it's a PDF processing error (usually missing Ghostscript)
            if (strpos(strtolower($imagePath), '.pdf') !== false) {
                if (strpos($message, 'gs') !== false || strpos($message, 'ghostscript') !== false || strpos($message, 'Pdf reading is not supported') !== false) {
                    throw new \Exception('Ghostscript is required to process PDF files. Please install Ghostscript (64-bit) on the server or upload the invoice as an image (JPG/PNG).');
                }
            }

            // Check if it's a Tesseract not found error
            if (
                preg_match('/tesseract/i', $message) &&
                (preg_match('/not found/i', $message) || preg_match('/не найден/i', $message) || preg_match('/is not recognized/i', $message))
            ) {
                throw new \Exception('Tesseract OCR binary not found at: ' . ($this->config['tesseract_path'] ?? 'default path') . '. Please check your .env configuration.');
            }

            throw new \Exception('OCR Error: ' . $message);
        }
    }

    /**
     * Parse extracted text to identify invoice fields
     *
     * @param string $text
     * @return array
     */
    protected function parseInvoiceText($text)
    {
        return [
            'invoice_number' => $this->extractInvoiceNumber($text),
            'invoice_date' => $this->extractDate($text, 'invoice'),
            'due_date' => $this->extractDate($text, 'due'),
            'vendor' => $this->matchOrSuggestVendor($text),
            'tax_id' => $this->extractTaxId($text),
            'address' => $this->extractAddress($text),
            'subtotal' => $this->extractAmount($text, 'subtotal'),
            'tax_amount' => $this->extractAmount($text, 'tax'),
            'total_amount' => $this->extractAmount($text, 'total'),
            'items' => $this->extractAndMatchLineItems($text),
        ];
    }

    /**
     * Extract invoice number
     */
    protected function extractInvoiceNumber($text)
    {
        // Try configured patterns first
        foreach ($this->config['patterns']['invoice_number'] as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }

        // Try common invoice number formats with labels above or beside
        $lines = explode("\n", $text);
        foreach ($lines as $i => $line) {
            $line = trim($line);

            // Pattern: Label [:] Value
            if (preg_match('/(?:#|invoice|bil|no|inv|رفم|فاتورة)\s*:?\s*([a-z0-9\-\/]{3,20})/i', $line, $matches)) {
                return trim($matches[1]);
            }

            // Pattern: Label on one line, Value on the next
            if (preg_match('/(?:invoice|فاتورة|رفم)\s*#?\s*$/i', $line) && isset($lines[$i + 1])) {
                $nextLine = trim($lines[$i + 1]);
                if (preg_match('/^([a-z0-9\-\/]{3,20})$/i', $nextLine, $matches)) {
                    return $matches[1];
                }
            }
        }

        return null;
    }

    /**
     * Extract dates
     */
    protected function extractDate($text, $type = 'invoice')
    {
        $keywords = [
            'invoice' => ['date', 'invoice date', 'تاريخ', 'بثا', 'تارين'],
            'due' => ['due', 'due date', 'تاريخ الاستحقاق'],
        ];

        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            foreach ($keywords[$type] as $keyword) {
                // Look for keyword followed by date
                $pattern = '/' . preg_quote($keyword, '/') . '\s*:?\s*(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})/i';
                if (preg_match($pattern, $line, $matches)) {
                    return $this->normalizeDate($matches[1]);
                }

                // Try alternate date formats (e.g. YYYY-MM-DD or DD.MM.YYYY)
                $pattern = '/' . preg_quote($keyword, '/') . '\s*:?\s*(\d{2,4}[\/.-]\d{1,2}[\/.-]\d{1,2})/i';
                if (preg_match($pattern, $line, $matches)) {
                    return $this->normalizeDate($matches[1]);
                }
            }
        }

        // Fallback to searching the whole text for generic date patterns if no keyword match
        foreach ($this->config['patterns']['date'] as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->normalizeDate($matches[1]);
            }
        }

        return null;
    }

    /**
     * Normalize date format to Y-m-d
     */
    protected function normalizeDate($dateString)
    {
        try {
            $date = new \DateTime($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Match existing vendor or suggest new vendor creation
     *
     * @param string $text
     * @return array|null
     */
    protected function matchOrSuggestVendor($text)
    {
        // Get first few lines (vendor usually at top)
        $lines = explode("\n", $text);
        $topLines = array_slice($lines, 0, 5);

        // Try to match against existing vendors
        $vendors = Vendor::where('is_active', true)->get();

        foreach ($vendors as $vendor) {
            $searchTerms = [
                $vendor->name_en,
                $vendor->name_ar,
                $vendor->code,
            ];

            foreach ($searchTerms as $term) {
                if (!empty($term)) {
                    foreach ($topLines as $line) {
                        if (stripos($line, $term) !== false) {
                            return [
                                'matched' => true,
                                'id' => $vendor->id,
                                'name' => $vendor->name_en,
                                'code' => $vendor->code,
                                'action' => 'use_existing',
                                'confidence' => 'high'
                            ];
                        }
                    }
                }
            }
        }

        // Extract vendor name from invoice for new vendor suggestion
        $suggestedName = null;
        foreach ($topLines as $line) {
            $line = trim($line);
            // Skip empty or purely numeric/symbolic lines
            if (empty($line) || strlen($line) < 4) {
                continue;
            }

            // Skip lines with common noise keywords in either language
            if (preg_match('/(invoice|فاتورة|tax|ضريبة|vat|id|#|no|tel|phone|رقم|هاتف|date|تاريخ|ref|PINV)/ui', $line)) {
                continue;
            }

            // The first non-header line is often the vendor name
            // Vendors in Arabic often have "شركة" (Company) or "مؤسسة" (Establishment)
            if (preg_match('/[\x{0600}-\x{06FF}]/u', $line)) {
                $suggestedName = $line;
                break;
            } elseif (strlen($line) > 5) {
                $suggestedName = $line;
                break;
            }
        }

        return [
            'matched' => false,
            'id' => null,
            'name' => $suggestedName,
            'tax_id' => $this->extractTaxId($text),
            'address' => $this->extractAddress($text),
            'action' => 'suggest_new',
            'confidence' => $suggestedName ? 'medium' : 'low'
        ];
    }

    /**
     * Extract Tax ID / VAT Number
     */
    protected function extractTaxId($text)
    {
        $patterns = [
            '/(?:tax id|vat|trn|registration)\s*:?\s*(\d{10,15})/i',
            '/(?:الرقم الضريبي|رقم الضريبة|الرقم الموحد)\s*:?\s*(\d{10,15})/u',
            '/\b(\d{15})\b/', // Catch isolated 15-digit numbers (likely Saudi VAT IDs)
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Extract Address
     */
    protected function extractAddress($text)
    {
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $line = trim($line);

            // Address keywords
            if (preg_match('/(?:address|street|district|ave|city|العنوان|حي|شارع|طريق|بناء)\s*:?\s*(.{10,100})/ui', $line, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Extract amount values
     */
    protected function extractAmount($text, $type)
    {
        $patterns = $this->config['patterns'][$type] ?? [];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = str_replace(',', '', $matches[1]);
                return (float) $amount;
            }
        }

        return null;
    }

    /**
     * Extract and match line items from invoice
     *
     * @param string $text
     * @return array
     */
    protected function extractAndMatchLineItems($text)
    {
        $items = [];
        $lines = explode("\n", $text);

        // Try to identify table rows with product information
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);

            if (strlen($line) < 5)
                continue;

            // Clean the line - normalize whitespace and separators
            $cleanedLine = preg_replace('/[|\[\]]/', ' ', $line);
            $cleanedLine = preg_replace('/\s+/', ' ', $cleanedLine);

            $extracted = null;

            // Pattern A: Multi-column (No, ItemNo, Description, Qty, Price, Amount, Ratio, VAT, Total)
            // Example: 1 2HR-PL6 فرع فالفون لييرة هولار 14 350.00 4,900.00 15 735.00 5,635.00
            if (preg_match('/^(\d+)\s+([A-Z0-9-]+)\s+(.+?)\s+(\d+(?:\.\d+)?)\s+([0-9,.]+)\s+([0-9,.]+)\s+(\d+)\s+([0-9,.]+)\s+([0-9,.]+)$/u', $cleanedLine, $matches)) {
                $extracted = [
                    'product_code' => $matches[2],
                    'description' => trim($matches[3]),
                    'quantity' => $matches[4],
                    'unit_price' => $matches[5],
                    'tax_amount' => $matches[8],
                    'total' => $matches[9],
                ];
            }
            // Pattern B: Description [space] Quantity [space] Price [space] Total
            elseif (preg_match('/^(.+?)\s+(\d+(?:\.\d+)?)\s+([0-9,.]+)\s+([0-9,.]+)$/', $cleanedLine, $matches)) {
                $extracted = [
                    'description' => trim($matches[1]),
                    'quantity' => $matches[2],
                    'unit_price' => $matches[3],
                    'total' => $matches[4],
                ];
            }
            // Pattern C: Quantity [space] Description [space] Price [space] Total (reversed RTL-ish)
            elseif (preg_match('/^(\d+(?:\.\d+)?)\s+(.+?)\s+([0-9,.]+)\s+([0-9,.]+)$/', $cleanedLine, $matches)) {
                $extracted = [
                    'description' => trim($matches[2]),
                    'quantity' => $matches[1],
                    'unit_price' => $matches[3],
                    'total' => $matches[4],
                ];
            }

            if ($extracted) {
                // Convert extracted values to numbers safely
                $qty = (float) str_replace(',', '', $extracted['quantity']);
                $price = (float) str_replace(',', '', $extracted['unit_price']);
                $total = (float) str_replace(',', '', $extracted['total']);
                $tax = isset($extracted['tax_amount']) ? (float) str_replace(',', '', $extracted['tax_amount']) : 0;

                // VALIDATION: Avoid noise
                if ($qty > 100000 || (isset($extracted['product_code']) && strlen($extracted['product_code']) < 3)) {
                    continue;
                }

                // Skip headers/footers
                if (preg_match('/(description|item|product|quantity|price|total|الوصف|الكمية|السعر|المجموع|subtotal|balance|date|invoice|فاتورة|ضريبة|VAT|No\.|Pos|Code)/iu', $extracted['description'])) {
                    continue;
                }

                // Try to match product by code first, then name
                $matchedProduct = null;
                if (!empty($extracted['product_code'])) {
                    $matchedProduct = Product::where('code', $extracted['product_code'])->first();
                }

                if (!$matchedProduct) {
                    $matchedProduct = $this->matchProduct($extracted['description']);
                }

                // If code was found but no product, prefix description with code for better auto-creation
                $finalName = $extracted['description'];
                if (!empty($extracted['product_code']) && !$matchedProduct) {
                    $finalName = "[" . $extracted['product_code'] . "] " . $finalName;
                }

                $items[] = [
                    'matched' => $matchedProduct !== null,
                    'product_id' => $matchedProduct ? $matchedProduct->id : null,
                    'product_name' => $matchedProduct ? ($matchedProduct->name_en ?: $matchedProduct->name_ar) : $finalName,
                    'suggested_name' => !$matchedProduct ? $finalName : null,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'tax_amount' => $tax,
                    'total' => $total,
                    'action' => $matchedProduct ? 'use_existing' : 'suggest_new',
                    'confidence' => $this->calculateMatchConfidence($extracted['description'], $matchedProduct)
                ];
            }
        }

        return $items;
    }

    /**
     * Match product by name
     *
     * @param string $searchName
     * @return Product|null
     */
    protected function matchProduct($searchName)
    {
        $searchName = trim($searchName);

        if (empty($searchName) || strlen($searchName) < 3) {
            return null;
        }

        // Try exact match first
        $product = Product::where('is_active', true)
            ->where('is_purchasable', true)
            ->where(function ($q) use ($searchName) {
                $q->where('name_en', 'LIKE', "%{$searchName}%")
                    ->orWhere('name_ar', 'LIKE', "%{$searchName}%");
            })
            ->first();

        return $product;
    }

    /**
     * Calculate confidence score for a match
     *
     * @param string $extractedName
     * @param Product|null $matchedProduct
     * @return string
     */
    protected function calculateMatchConfidence($extractedName, $matchedProduct)
    {
        if (!$matchedProduct) {
            return 'low';
        }

        // Check similarity of names
        $similarity = 0;
        similar_text(
            strtolower($extractedName),
            strtolower($matchedProduct->name_en),
            $similarity
        );

        if ($similarity > 80) {
            return 'high';
        } elseif ($similarity > 50) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Validate uploaded file
     */
    public function validateFile($file)
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > ($this->config['max_file_size'] * 1024)) {
            $errors[] = 'File size exceeds maximum allowed size of ' . $this->config['max_file_size'] . 'KB';
        }

        // Check mime type
        if (!in_array($file->getMimeType(), $this->config['allowed_mimes'])) {
            $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $this->config['allowed_extensions']);
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Store uploaded file temporarily
     */
    public function storeUploadedFile($file)
    {
        $uploadPath = $this->config['upload_path'];

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = uniqid('invoice_') . '.' . $file->getClientOriginalExtension();
        $filepath = $uploadPath . DIRECTORY_SEPARATOR . $filename;

        $file->move($uploadPath, $filename);

        return $filepath;
    }

    /**
     * Clean up temporary file
     */
    public function deleteTemporaryFile($filepath)
    {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
