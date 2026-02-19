<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use App\Models\JournalVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccountingService
{
    /**
     * Generate a new account code based on parent and type.
     */
    public function generateAccountCode($type, $parentId = null)
    {
        $prefix = match ($type) {
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5',
            default => '0',
        };

        if ($parentId) {
            $parent = ChartOfAccount::find($parentId);
            $prefix = $parent->code;
        }

        $lastAccount = ChartOfAccount::where('parent_id', $parentId)
            ->where('type', $type)
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastAccount) {
            return $prefix . '01';
        }

        $lastSeq = substr($lastAccount->code, strlen($prefix));
        $newSeq = str_pad((int) $lastSeq + 1, 2, '0', STR_PAD_LEFT);

        return $prefix . $newSeq;
    }

    /**
     * Post a Journal Voucher to the ledger.
     */
    public function postJournalVoucher(JournalVoucher $voucher)
    {
        return DB::transaction(function () use ($voucher) {
            if ($voucher->status !== 'draft') {
                return false;
            }

            foreach ($voucher->items as $item) {
                $this->createLedgerEntry([
                    'chart_of_account_id' => $item->chart_of_account_id,
                    'transaction_date' => $voucher->voucher_date,
                    'reference_type' => 'journal_voucher',
                    'reference_id' => $voucher->id,
                    'reference_number' => $voucher->voucher_number,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                    'description' => $voucher->description,
                    'customer_id' => $item->customer_id,
                    'vendor_id' => $item->vendor_id,
                    'cost_center_no' => $item->cost_center_no,
                    'activity_no' => $item->activity_no,
                    'lc_no' => $item->lc_no,
                    'rep' => $item->rep,
                    'collector_no' => $item->collector_no,
                    'promoter_code' => $item->promoter_code,
                ]);
            }

            $voucher->update([
                'status' => 'posted',
                'approved_by' => auth()->id(),
            ]);

            return true;
        });
    }

    /**
     * Update ledger entries for a posted Journal Voucher.
     */
    public function updatePostedJournalVoucher(JournalVoucher $voucher)
    {
        return DB::transaction(function () use ($voucher) {
            if ($voucher->status !== 'posted') {
                return false;
            }

            // Delete existing ledger entries
            LedgerEntry::where('reference_type', 'journal_voucher')
                ->where('reference_id', $voucher->id)
                ->delete();

            // Recreate ledger entries
            foreach ($voucher->items as $item) {
                // Ensure we get fresh data if relations are loaded
                $this->createLedgerEntry([
                    'chart_of_account_id' => $item->chart_of_account_id,
                    'transaction_date' => $voucher->voucher_date,
                    'reference_type' => 'journal_voucher',
                    'reference_id' => $voucher->id,
                    'reference_number' => $voucher->voucher_number,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                    'description' => $voucher->description,
                    'customer_id' => $item->customer_id,
                    'vendor_id' => $item->vendor_id,
                    'cost_center_no' => $item->cost_center_no,
                    'activity_no' => $item->activity_no,
                    'lc_no' => $item->lc_no,
                    'rep' => $item->rep,
                    'collector_no' => $item->collector_no,
                    'promoter_code' => $item->promoter_code,
                ]);
            }

            return true;
        });
    }

    /**
     * Create a single ledger entry.
     */
    public function createLedgerEntry(array $data)
    {
        $extraData = [
            'company_id' => Session::get('active_company_id'),
            'branch_id' => Session::get('active_branch_id'),
            'created_by' => auth()->id(),
        ];

        // Merge extra data, but data['customer_id'] and data['vendor_id'] will be preserved if passed
        return LedgerEntry::create(array_merge($data, $extraData));
    }

    /**
     * Post Sales Invoice to ledger automatically.
     */
    public function postSalesInvoice($invoice)
    {
        return DB::transaction(function () use ($invoice) {
            // Find or create standard accounts
            $arAccount = $this->getAccountByCode('103'); // Accounts Receivable (Asset)
            $salesAccount = $this->getAccountByCode('401'); // Sales Revenue (Revenue)
            $taxAccount = $this->getAccountByCode('202'); // Taxes Payable (Liability)

            // Debit AR
            $this->createLedgerEntry([
                'chart_of_account_id' => $arAccount->id,
                'transaction_date' => $invoice->invoice_date,
                'reference_type' => 'sales_invoice',
                'reference_id' => $invoice->id,
                'reference_number' => $invoice->invoice_number,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'description' => 'Sales Invoice: ' . $invoice->invoice_number,
                'customer_id' => $invoice->customer_id,
            ]);

            // Credit Sales
            $this->createLedgerEntry([
                'chart_of_account_id' => $salesAccount->id,
                'transaction_date' => $invoice->invoice_date,
                'reference_type' => 'sales_invoice',
                'reference_id' => $invoice->id,
                'reference_number' => $invoice->invoice_number,
                'debit' => 0,
                'credit' => $invoice->subtotal,
                'description' => 'Sales Revenue from Invoice: ' . $invoice->invoice_number,
            ]);

            // Credit Tax
            if ($invoice->tax_amount > 0) {
                $this->createLedgerEntry([
                    'chart_of_account_id' => $taxAccount->id,
                    'transaction_date' => $invoice->invoice_date,
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                    'reference_number' => $invoice->invoice_number,
                    'debit' => 0,
                    'credit' => $invoice->tax_amount,
                    'description' => 'Tax on Sales Invoice: ' . $invoice->invoice_number,
                ]);
            }

            return true;
        });
    }

    /**
     * Post Purchase Invoice to ledger automatically.
     */
    public function postPurchaseInvoice($invoice)
    {
        return DB::transaction(function () use ($invoice) {
            // Find or create standard accounts
            $apAccount = $this->getAccountByCode('201'); // Accounts Payable (Liability)
            $inventoryAccount = $this->getAccountByCode('104'); // Inventory (Asset)
            $taxAccount = $this->getAccountByCode('202'); // Taxes Payable (Liability)

            // Credit AP
            $this->createLedgerEntry([
                'chart_of_account_id' => $apAccount->id,
                'transaction_date' => $invoice->invoice_date,
                'reference_type' => 'purchase_invoice',
                'reference_id' => $invoice->id,
                'reference_number' => $invoice->invoice_number,
                'debit' => 0,
                'credit' => $invoice->total_amount,
                'description' => 'Purchase Invoice: ' . $invoice->invoice_number,
                'vendor_id' => $invoice->vendor_id,
            ]);

            // Debit Inventory
            $this->createLedgerEntry([
                'chart_of_account_id' => $inventoryAccount->id,
                'transaction_date' => $invoice->invoice_date,
                'reference_type' => 'purchase_invoice',
                'reference_id' => $invoice->id,
                'reference_number' => $invoice->invoice_number,
                'debit' => $invoice->subtotal,
                'credit' => 0,
                'description' => 'Inventory addition from Invoice: ' . $invoice->invoice_number,
            ]);

            // Debit Tax (Input Tax)
            if ($invoice->tax_amount > 0) {
                $this->createLedgerEntry([
                    'chart_of_account_id' => $taxAccount->id,
                    'transaction_date' => $invoice->invoice_date,
                    'reference_type' => 'purchase_invoice',
                    'reference_id' => $invoice->id,
                    'reference_number' => $invoice->invoice_number,
                    'debit' => $invoice->tax_amount,
                    'credit' => 0,
                    'description' => 'Input Tax on Purchase Invoice: ' . $invoice->invoice_number,
                ]);
            }

            return true;
        });
    }

    private function getAccountByCode($code)
    {
        return ChartOfAccount::where('code', $code)->first() ??
            ChartOfAccount::create([
                'code' => $code,
                'name_en' => $this->getDefaultAccountName($code),
                'type' => $this->getAccountTypeByCode($code),
                'sub_ledger_type' => $this->getSubLedgerTypeByCode($code),
                'is_active' => true,
            ]);
    }

    private function getDefaultAccountName($code)
    {
        return match (substr($code, 0, 1)) {
            '1' => 'Accounts Receivable',
            '2' => 'Taxes Payable',
            '3' => 'Owner Capital',
            '4' => 'Sales Revenue',
            '5' => 'Cost of Goods Sold',
            default => 'General Account',
        };
    }

    private function getAccountTypeByCode($code)
    {
        return match (substr($code, 0, 1)) {
            '1' => 'asset',
            '2' => 'liability',
            '3' => 'equity',
            '4' => 'revenue',
            '5' => 'expense',
            default => 'asset',
        };
    }

    private function getSubLedgerTypeByCode($code)
    {
        return match ($code) {
            '103' => 'customer', // Accounts Receivable
            '201' => 'vendor',   // Accounts Payable
            default => 'none',
        };
    }
}
