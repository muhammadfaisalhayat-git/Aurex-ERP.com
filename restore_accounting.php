<?php

$path = 'app/Services/AccountingService.php';
$content = file_get_contents($path);

$postPaymentVoucher = <<<PHP
    /**
     * Post Payment Voucher to ledger.
     */
    public function postPaymentVoucher(\App\Models\PaymentVoucher \$voucher)
    {
        return DB::transaction(function () use (\$voucher) {
            if (\$voucher->status !== 'draft') {
                return false;
            }

            \$bankAccount = \$voucher->bankAccount;
            \$targetAccountId = \$voucher->chart_of_account_id;

            // Debit Target Account (Expense or Vendor AR)
            \$ledgerData = [
                'chart_of_account_id' => \$targetAccountId,
                'transaction_date' => \$voucher->voucher_date,
                'reference_type' => 'payment_voucher',
                'reference_id' => \$voucher->id,
                'reference_number' => \$voucher->voucher_number,
                'debit' => \$voucher->amount,
                'credit' => 0,
                'description' => \$voucher->description ?? 'Payment Voucher: ' . \$voucher->voucher_number,
                'beneficiary_id' => \$voucher->beneficiary_id,
                'beneficiary_type' => \$voucher->beneficiary_type,
            ];

            if (\$voucher->beneficiary_type === 'App\Models\Employee') {
                \$ledgerData['employee_id'] = \$voucher->beneficiary_id;
            } elseif (\$voucher->beneficiary_type === 'App\Models\Vendor') {
                \$ledgerData['vendor_id'] = \$voucher->beneficiary_id;
            } elseif (\$voucher->beneficiary_type === 'App\Models\Customer') {
                \$ledgerData['customer_id'] = \$voucher->beneficiary_id;
            }

            if (!isset(\$ledgerData['vendor_id']) && \$voucher->vendor_id) {
                \$ledgerData['vendor_id'] = \$voucher->vendor_id;
            }

            \$this->createLedgerEntry(\$ledgerData);

            // Credit Bank/Cash Account
            \$this->createLedgerEntry([
                'chart_of_account_id' => \$bankAccount->chart_of_account_id,
                'transaction_date' => \$voucher->voucher_date,
                'reference_type' => 'payment_voucher',
                'reference_id' => \$voucher->id,
                'reference_number' => \$voucher->voucher_number,
                'debit' => 0,
                'credit' => \$voucher->amount,
                'description' => \$voucher->description ?? 'Payment for: ' . \$voucher->payee_name,
            ]);

            // Update balances
            \$bankAccount->decrement('current_balance', (float) \$voucher->amount);

            \$voucher->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Post Receipt Voucher to ledger.
     */
    public function postReceiptVoucher(\App\Models\ReceiptVoucher \$voucher)
    {
        return DB::transaction(function () use (\$voucher) {
            if (\$voucher->status !== 'draft') {
                return false;
            }

            \$bankAccount = \$voucher->bankAccount;
            \$targetAccountId = \$voucher->chart_of_account_id;

            // Debit Bank/Cash Account
            \$this->createLedgerEntry([
                'chart_of_account_id' => \$bankAccount->chart_of_account_id,
                'transaction_date' => \$voucher->voucher_date,
                'reference_type' => 'receipt_voucher',
                'reference_id' => \$voucher->id,
                'reference_number' => \$voucher->voucher_number,
                'debit' => \$voucher->amount,
                'credit' => 0,
                'description' => \$voucher->description ?? 'Receipt from: ' . \$voucher->payer_name,
            ]);

            // Credit Target Account (Income or Customer AR)
            \$ledgerData = [
                'chart_of_account_id' => \$targetAccountId,
                'transaction_date' => \$voucher->voucher_date,
                'reference_type' => 'receipt_voucher',
                'reference_id' => \$voucher->id,
                'reference_number' => \$voucher->voucher_number,
                'debit' => 0,
                'credit' => \$voucher->amount,
                'description' => \$voucher->description ?? 'Receipt Voucher: ' . \$voucher->voucher_number,
                'beneficiary_id' => \$voucher->beneficiary_id,
                'beneficiary_type' => \$voucher->beneficiary_type,
            ];

            if (\$voucher->beneficiary_type === 'App\Models\Employee') {
                \$ledgerData['employee_id'] = \$voucher->beneficiary_id;
            } elseif (\$voucher->beneficiary_type === 'App\Models\Vendor') {
                \$ledgerData['vendor_id'] = \$voucher->beneficiary_id;
            } elseif (\$voucher->beneficiary_type === 'App\Models\Customer') {
                \$ledgerData['customer_id'] = \$voucher->beneficiary_id;
            }

            if (!isset(\$ledgerData['customer_id']) && \$voucher->customer_id) {
                \$ledgerData['customer_id'] = \$voucher->customer_id;
            }

            \$this->createLedgerEntry(\$ledgerData);

            // Update balances
            \$bankAccount->increment('current_balance', (float) \$voucher->amount);

            \$voucher->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            return true;
        });
    }
PHP;

// Find the broken postPaymentVoucher method
$pattern = '/    public function postPaymentVoucher\(\\\\App\\\\Models\\\\PaymentVoucher \$voucher\)\s*\{\s*return DB::transaction\(function \(\) use \(\$voucher\) \{\s*if \(\$voucher->status !== \'draft\'\) \{\s*return false;\s*\'status\' => \'posted\',\s*\'posted_by\' => auth\(\)->id\(\),\s*\'posted_at\' => now\(\),\s*\]\);\s*return true;\s*\}\);\s*\}/s';

if (preg_match($pattern, $content)) {
    $newContent = preg_replace($pattern, $postPaymentVoucher, $content);
    file_put_contents($path, $newContent);
    echo "Fixed corrupted methods.\n";
} else {
    echo "Could not find corrupted pattern. Checking alternative...\n";
    // Fallback if the pattern is slightly different
    $altPattern = '/    public function postPaymentVoucher\(\\\\App\\\\Models\\\\PaymentVoucher \$voucher\)\s*\{.*?return true;\s*\}\);\s*\}/s';
    if (preg_match($altPattern, $content, $matches)) {
        $newContent = preg_replace($altPattern, $postPaymentVoucher, $content);
        file_put_contents($path, $newContent);
        echo "Fixed corrupted methods (alt pattern).\n";
    } else {
        echo "ERROR: Failed to find corrupted method.\n";
        echo "DEBUG: Content around 331:\n";
        echo substr($content, strpos($content, 'postPaymentVoucher'), 200);
    }
}
