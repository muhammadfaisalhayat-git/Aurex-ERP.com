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
        $newSeq = str_pad((int)$lastSeq + 1, 2, '0', STR_PAD_LEFT);

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

    /**
     * Post Production Order completion to ledger.
     */
    public function postProductionCompletion($order)
    {
        return DB::transaction(function () use ($order) {
            $finishedGoodsAcc = $this->getAccountByCode('104-FG'); // Finished Goods (Asset)
            $wipAccount = $this->getAccountByCode('104-WIP'); // WIP (Asset)

            // Debit Finished Goods
            $this->createLedgerEntry([
                'chart_of_account_id' => $finishedGoodsAcc->id,
                'transaction_date' => $order->end_date ?? now(),
                'reference_type' => 'production_order',
                'reference_id' => $order->id,
                'reference_number' => $order->document_number,
                'debit' => $order->total_cost,
                'credit' => 0,
                'description' => 'Finished Goods from Production: ' . $order->document_number,
            ]);

            // Credit WIP
            $this->createLedgerEntry([
                'chart_of_account_id' => $wipAccount->id,
                'transaction_date' => $order->end_date ?? now(),
                'reference_type' => 'production_order',
                'reference_id' => $order->id,
                'reference_number' => $order->document_number,
                'debit' => 0,
                'credit' => $order->total_cost,
                'description' => 'Clearing WIP for Production: ' . $order->document_number,
            ]);

            return true;
        });
    }

    /**
     * Post Fuel Log to ledger.
     */
    public function postFuelLog($log)
    {
        return DB::transaction(function () use ($log) {
            $fuelExpenseAcc = $this->getAccountByCode('506'); // Fuel Expense (Expense)
            $bankAccount = $this->getAccountByCode('101'); // Bank/Cash (Asset)

            // Debit Fuel Expense
            $this->createLedgerEntry([
                'chart_of_account_id' => $fuelExpenseAcc->id,
                'transaction_date' => $log->entry_date,
                'reference_type' => 'fuel_log',
                'reference_id' => $log->id,
                'reference_number' => 'FUEL-' . $log->id,
                'debit' => $log->total_cost,
                'credit' => 0,
                'description' => 'Fuel Expense for Vehicle: ' . ($log->vehicle->plate_number ?? 'Unknown'),
            ]);

            // Credit Bank/Cash
            $this->createLedgerEntry([
                'chart_of_account_id' => $bankAccount->id,
                'transaction_date' => $log->entry_date,
                'reference_type' => 'fuel_log',
                'reference_id' => $log->id,
                'reference_number' => 'FUEL-' . $log->id,
                'debit' => 0,
                'credit' => $log->total_cost,
                'description' => 'Payment for Fuel - Log ID: ' . $log->id,
            ]);

            return true;
        });
    }

    /**
     * Post Payment Voucher to ledger.
     */
    public function postPaymentVoucher(\App\Models\PaymentVoucher $voucher)
    {
        return DB::transaction(function () use ($voucher) {
            if ($voucher->status !== 'draft') {
                return false;
            }

            $bankAccount = $voucher->bankAccount;
            $targetAccountId = $voucher->chart_of_account_id;

            // Debit Target Account (Expense or Vendor AR)
            $this->createLedgerEntry([
                'chart_of_account_id' => $targetAccountId,
                'transaction_date' => $voucher->voucher_date,
                'reference_type' => 'payment_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => $voucher->amount,
                'credit' => 0,
                'description' => $voucher->description ?? 'Payment Voucher: ' . $voucher->voucher_number,
                'vendor_id' => $voucher->vendor_id,
                'beneficiary_id' => $voucher->beneficiary_id,
                'beneficiary_type' => $voucher->beneficiary_type,
            ]);

            // Credit Bank/Cash Account
            $this->createLedgerEntry([
                'chart_of_account_id' => $bankAccount->chart_of_account_id,
                'transaction_date' => $voucher->voucher_date,
                'reference_type' => 'payment_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => 0,
                'credit' => $voucher->amount,
                'description' => $voucher->description ?? 'Payment for: ' . $voucher->payee_name,
            ]);

            // Update balances
            $bankAccount->decrement('current_balance', $voucher->amount);

            $voucher->update([
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
    public function postReceiptVoucher(\App\Models\ReceiptVoucher $voucher)
    {
        return DB::transaction(function () use ($voucher) {
            if ($voucher->status !== 'draft') {
                return false;
            }

            $bankAccount = $voucher->bankAccount;
            $targetAccountId = $voucher->chart_of_account_id;

            // Debit Bank/Cash Account
            $this->createLedgerEntry([
                'chart_of_account_id' => $bankAccount->chart_of_account_id,
                'transaction_date' => $voucher->voucher_date,
                'reference_type' => 'receipt_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => $voucher->amount,
                'credit' => 0,
                'description' => $voucher->description ?? 'Receipt from: ' . $voucher->payer_name,
            ]);

            // Credit Target Account (Income or Customer AR)
            $this->createLedgerEntry([
                'chart_of_account_id' => $targetAccountId,
                'transaction_date' => $voucher->voucher_date,
                'reference_type' => 'receipt_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => 0,
                'credit' => $voucher->amount,
                'description' => $voucher->description ?? 'Receipt Voucher: ' . $voucher->voucher_number,
                'customer_id' => $voucher->customer_id,
                'beneficiary_id' => $voucher->beneficiary_id,
                'beneficiary_type' => $voucher->beneficiary_type,
            ]);

            // Update balances
            $bankAccount->increment('current_balance', $voucher->amount);

            $voucher->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Post Maintenance Voucher to ledger.
     */
    public function postMaintenanceVoucher($voucher)
    {
        return DB::transaction(function () use ($voucher) {
            $maintenanceExpenseAcc = $this->getAccountByCode('507'); // Maintenance Expense
            $bankAcc = $this->getAccountByCode('101'); // Bank/Cash

            $this->createLedgerEntry([
                'chart_of_account_id' => $maintenanceExpenseAcc->id,
                'transaction_date' => $voucher->completion_date ?? now(),
                'reference_type' => 'maintenance_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => $voucher->actual_cost,
                'credit' => 0,
                'description' => 'Maintenance: ' . $voucher->problem_description,
                'customer_id' => $voucher->customer_id,
                'vendor_id' => $voucher->vendor_id,
            ]);

            $this->createLedgerEntry([
                'chart_of_account_id' => $bankAcc->id,
                'transaction_date' => $voucher->completion_date ?? now(),
                'reference_type' => 'maintenance_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => 0,
                'credit' => $voucher->actual_cost,
                'description' => 'Payment for Maintenance: ' . $voucher->voucher_number,
            ]);

            return true;
        });
    }

    /**
     * Post Stock Adjustment/Wastage to ledger.
     */
    public function postStockAdjustment($issueOrder)
    {
        return DB::transaction(function () use ($issueOrder) {
            $inventoryAcc = $this->getAccountByCode('104'); // Inventory Asset
            $adjustmentAcc = $this->getAccountByCode('508'); // Inventory Adjustment Expense

            $totalCost = 0;
            foreach ($issueOrder->items as $item) {
                $totalCost += ($item->product->average_cost ?? 0) * $item->quantity;
            }

            if ($totalCost <= 0)
                return true;

            // Credit Inventory (Reduction)
            $this->createLedgerEntry([
                'chart_of_account_id' => $inventoryAcc->id,
                'transaction_date' => $issueOrder->issue_date,
                'reference_type' => 'stock_issue',
                'reference_id' => $issueOrder->id,
                'reference_number' => $issueOrder->document_number,
                'debit' => 0,
                'credit' => $totalCost,
                'description' => 'Inventory Reduction (' . $issueOrder->issue_type . '): ' . $issueOrder->document_number,
                'customer_id' => $issueOrder->customer_id,
                'vendor_id' => $issueOrder->vendor_id,
            ]);

            // Debit Adjustment Expense
            $this->createLedgerEntry([
                'chart_of_account_id' => $adjustmentAcc->id,
                'transaction_date' => $issueOrder->issue_date,
                'reference_type' => 'stock_issue',
                'reference_id' => $issueOrder->id,
                'reference_number' => $issueOrder->document_number,
                'debit' => $totalCost,
                'credit' => 0,
                'description' => 'Inventory Adjustment Expense: ' . $issueOrder->document_number,
                'customer_id' => $issueOrder->customer_id,
                'vendor_id' => $issueOrder->vendor_id,
            ]);

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
                '507' => 'Maintenance Expense',
                '508' => 'Inventory Adjustment',
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
                '201' => 'vendor', // Accounts Payable
                default => 'none',
            };
    }
}
