<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tesseract Binary Path
    |--------------------------------------------------------------------------
    |
    | Path to the Tesseract OCR binary. Leave null for auto-detection.
    | Windows: 'C:\Program Files\Tesseract-OCR\tesseract.exe'
    | Linux: '/usr/bin/tesseract'
    |
    */
    'tesseract_path' => env('TESSERACT_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | OCR Languages
    |--------------------------------------------------------------------------
    |
    | Languages to use for OCR. Multiple languages can be specified.
    | Common: 'eng' (English), 'ara' (Arabic)
    |
    */
    'languages' => env('OCR_LANGUAGES', 'eng+ara'),

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    */
    'max_file_size' => 10240, // KB (10MB)
    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'],
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf'],

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    */
    'upload_path' => storage_path('app/invoices/uploads'),

    /*
    |--------------------------------------------------------------------------
    | OCR Confidence Threshold
    |--------------------------------------------------------------------------
    |
    | Minimum confidence score (0-100) for accepting extracted text
    |
    */
    'confidence_threshold' => 60,

    /*
    |--------------------------------------------------------------------------
    | Text Extraction Patterns
    |--------------------------------------------------------------------------
    |
    | Regex patterns for extracting specific invoice fields
    |
    */
    'patterns' => [
        'invoice_number' => [
            '/invoice\s*#?\s*:?\s*([A-Z0-9\-\/]+)/i',
            '/inv\s*#?\s*:?\s*([A-Z0-9\-\/]+)/i',
            '/رقم\s*الفاتورة\s*:?\s*([A-Z0-9\-\/]+)/u',
        ],
        'date' => [
            '/(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})/',
            '/(\d{4}[\/-]\d{1,2}[\/-]\d{1,2})/',
        ],
        'total' => [
            '/total\s*:?\s*([0-9,\.]+)/i',
            '/المجموع\s*:?\s*([0-9,\.]+)/u',
        ],
        'tax' => [
            '/tax\s*:?\s*([0-9,\.]+)/i',
            '/vat\s*:?\s*([0-9,\.]+)/i',
            '/ضريبة\s*:?\s*([0-9,\.]+)/u',
        ],
    ],
];
