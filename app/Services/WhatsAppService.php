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

            Log::info("Attempting to send WhatsApp document to {$phone}", ['filename' => $fileName]);

            // Encode file as base64. UltraMsg prefers raw base64 string for the 'document' field.
            $fileContent = base64_encode(file_get_contents($filePath));

            $response = Http::post("{$this->baseUrl}/{$instanceId}/messages/document", [
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

            Log::error('UltraMsg API error: ' . $response->body());
            return ['success' => false, 'message' => 'API Error: ' . ($response->json()['error'] ?? 'Unknown error')];
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
        $documentNumber = $document->document_number ?? $document->invoice_number ?? 'N/A';
        $totalAmount = isset($document->total_amount) ? number_format($document->total_amount, 2) : '0.00';
        $customerName = $document->customer?->name ?? 'Customer';

        $message = "Hello {$customerName},\n\n";

        switch ($type) {
            case 'quotation':
                $message .= "Here is your Quotation #{$documentNumber}.\n";
                $message .= "Total: {$totalAmount} SAR\n";
                break;
            case 'invoice':
                $message .= "Here is your Invoice #{$documentNumber}.\n";
                $message .= "Total: {$totalAmount} SAR\n";
                break;
            case 'request':
                $message .= "Regarding your Customer Request #{$documentNumber}.\n";
                break;
            default:
                $message .= "Regarding document #{$documentNumber}.\n";
        }

        $message .= "\nThank you for choosing Aurex ERP.";

        return $message;
    }
}
