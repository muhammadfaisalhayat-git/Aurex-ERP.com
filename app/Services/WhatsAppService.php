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
            // Encode file as base64 or send as attachment. UltraMsg prefers a public URL or multipart.
            // Since we're sending a local file, we'll use base64 in the 'document' field for UltraMsg.
            $fileContent = base64_encode(file_get_contents($filePath));
            $documentData = "data:application/pdf;base64," . $fileContent;

            $response = Http::post("{$this->baseUrl}/{$this->instanceId}/messages/document", [
                'token' => $this->token,
                'to' => $phone,
                'filename' => $fileName,
                'document' => $documentData,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
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
