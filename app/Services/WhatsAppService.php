<?php

namespace App\Services;

class WhatsAppService
{
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
