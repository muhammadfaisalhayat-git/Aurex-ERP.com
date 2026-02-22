<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $instanceId;
    protected $token;
    protected $baseUrl = 'https://api.ultramsg.com';

    public function __construct()
    {
        $this->instanceId = SystemSetting::getValue('whatsapp_instance_id');
        $this->token = SystemSetting::getValue('whatsapp_token');
    }

    /**
     * Send a document via UltraMsg API.
     *
     * @param string $phone
     * @param string $filePath
     * @param string $fileName
     * @param string|null $caption
     * @return array
     */
    public function sendDocument($phone, $filePath, $fileName, $caption = null)
    {
        if (empty($this->instanceId) || empty($this->token)) {
            Log::error('WhatsApp credentials not set.');
            return ['success' => false, 'message' => 'WhatsApp credentials not set.'];
        }

        // Clean phone number (remove non-numeric characters)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        try {
            // Refresh credentials in case they were updated in the same request
            $instanceId = $this->instanceId ?: SystemSetting::getValue('whatsapp_instance_id');
            $token = $this->token ?: SystemSetting::getValue('whatsapp_token');

            if (empty($instanceId) || empty($token)) {
                Log::warning('WhatsApp credentials are not configured. Falling back to wa.me link.');
                return ['success' => false, 'message' => 'WhatsApp credentials are not configured.'];
            }

            // Validate file existence and content
            if (!file_exists($filePath)) {
                Log::error('WhatsApp send error: File does not exist at ' . $filePath);
                return ['success' => false, 'message' => 'Document file not found.'];
            }

            $rawContent = file_get_contents($filePath);
            if ($rawContent === false || strlen($rawContent) === 0) {
                Log::error('WhatsApp send error: File is empty or inaccessible at ' . $filePath);
                return ['success' => false, 'message' => 'Document content is missing or empty.'];
            }

            Log::info("Attempting to send WhatsApp document to {$phone}", [
                'filename' => $fileName,
                'file_size' => strlen($rawContent)
            ]);

            // Encode file as base64. UltraMsg prefers raw base64 string for the 'document' field.
            $fileContent = base64_encode($rawContent);

            $response = Http::withoutVerifying()->post("{$this->baseUrl}/{$instanceId}/messages/document", [
                'token' => $token,
                'to' => $phone,
                'filename' => $fileName,
                'document' => $fileContent,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp document sent successfully.', $response->json());
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error('UltraMsg API error: ' . $response->body(), [
                'status' => $response->status(),
                'payload_keys' => array_keys([
                    'token' => $token,
                    'to' => $phone,
                    'filename' => $fileName,
                    'document' => 'BASE64_CONTENT',
                    'caption' => $caption,
                ])
            ]);

            $errorMsg = 'API Error: ' . ($response->json()['error'] ?? $response->body() ?? 'Unknown error');
            return ['success' => false, 'message' => $errorMsg];
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate a WhatsApp link with a pre-filled message.
     *
     * @param string $phone
     * @param string $message
     * @return string
     */
    public function generateLink($phone, $message)
    {
        // Clean phone number (remove non-numeric characters)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure researchers handle country codes correctly if needed.
        // For now, we assume the phone is stored with country code or as is locally.

        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }

    /**
     * Format a message for a specific document.
     *
     * @param mixed $document
     * @param string $type (quotation, invoice, request)
     * @return string
     */
    public function formatDocumentMessage($document, $type)
    {
        // Ensure branch relationship is loaded for localized names
        if (method_exists($document, 'loadMissing')) {
            $document->loadMissing(['branch', 'customer']);
        }

        $documentNumber = $document->document_number ?? $document->invoice_number ?? 'N/A';
        $totalAmount = isset($document->total_amount) ? number_format($document->total_amount, 2) : '0.00';
        $customerName = $document->customer?->name ?? 'Customer';

        $isCredit = ($document->payment_terms === 'Credit' || (isset($document->balance_amount) && $document->balance_amount > 0));
        $currentBalance = $document->customer?->current_balance ?? 0;
        $formattedBalance = number_format($currentBalance, 2);

        // English part
        $enMessage = "Hello {$customerName},\n\n";
        switch ($type) {
            case 'quotation':
                $enMessage .= "Here is your Quotation #{$documentNumber}.\n";
                $enMessage .= "Total: {$totalAmount} SAR\n";
                break;
            case 'invoice':
                $enMessage .= "Here is your Invoice #{$documentNumber}.\n";
                $enMessage .= "Total: {$totalAmount} SAR\n";
                break;
            case 'request':
                $enMessage .= "Regarding your Customer Request #{$documentNumber}.\n";
                break;
            default:
                $enMessage .= "Regarding document #{$documentNumber}.\n";
        }

        if ($isCredit) {
            $enMessage .= "Total Outstanding Balance: {$formattedBalance} SAR\n";
        }

        $branchNameEn = $document->branch?->name_en ?? 'Aurex ERP';
        $enMessage .= "\nThank you for choosing {$branchNameEn}.";

        // Arabic part
        $arMessage = "مرحباً {$customerName}،\n\n";
        switch ($type) {
            case 'quotation':
                $arMessage .= "إليك عرض السعر رقم #{$documentNumber}.\n";
                $arMessage .= "الإجمالي: {$totalAmount} ريال سعودي\n";
                break;
            case 'invoice':
                $arMessage .= "إليك الفاتورة رقم #{$documentNumber}.\n";
                $arMessage .= "الإجمالي: {$totalAmount} ريال سعودي\n";
                break;
            case 'request':
                $arMessage .= "بخصوص طلب العميل رقم #{$documentNumber}.\n";
                break;
            default:
                $arMessage .= "بخصوص المستند رقم #{$documentNumber}.\n";
        }

        if ($isCredit) {
            $arMessage .= "إجمالي الرصيد المتبقي: {$formattedBalance} ريال سعودي\n";
        }

        $branchNameAr = $document->branch?->name_ar ?? 'أوريكس ERP';
        $arMessage .= "\nشكراً لاختياركم {$branchNameAr}.";

        return $enMessage . "\n\n--------------------------\n\n" . $arMessage;
    }
}
