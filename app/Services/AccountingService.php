<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use App\Models\JournalVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccountingService
{
    public function generateAccountCode($type, $parentId = null)
    {
        $baseType = $this->getBaseType($type);
        $prefix = match ($baseType) {
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
            ->where('type', $baseType)
            ->where('company_id', session('active_company_id'))
            ->orderBy('code', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastAccount) {
            // Get the part of the code after the prefix
            $lastSeqStr = substr($lastAccount->code, strlen($prefix));
            if (is_numeric($lastSeqStr)) {
                $nextSeq = (int) $lastSeqStr + 1;
            }
        }

        // Loop until we find a code that isn't taken globally by this company
        // Increase limit to 1000 to handle 3-digit sequences (common in 4-digit codes)
        $limit = 1000;
        do {
            $code = $prefix . str_pad($nextSeq, 2, '0', STR_PAD_LEFT);
            $exists = ChartOfAccount::where('code', $code)
                ->where('company_id', session('active_company_id'))
                ->exists();
            if (!$exists) {
                break;
            }
            $nextSeq++;
        } while ($nextSeq < $limit);

        return $code;
    }

    /**
     * Map an account type code or prefix to the base enum types.
     */
    public function getBaseType($code)
    {
        $code = strtolower($code);

        // Direct matches
        if (in_array($code, ['asset', 'liability', 'equity', 'revenue', 'expense'])) {
            return $code;
        }

        // Prefix based matches
        if (str_starts_with($code, '1'))
            return 'asset';
        if (str_starts_with($code, '2'))
            return 'liability';
        if (str_starts_with($code, '3'))
            return 'equity';
        if (str_starts_with($code, '4'))
            return 'revenue';
        if (str_starts_with($code, '5'))
            return 'expense';

        // Known legacy/custom codes
        if ($code === 'supp_cust')
            return 'asset';

        return 'asset'; // Fallback
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
                    'employee_id' => $item->employee_id,
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
                    'employee_id' => $item->employee_id,
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
            'company_id' => $data['company_id'] ?? Session::get('active_company_id'),
            'branch_id' => $data['branch_id'] ?? Session::get('active_branch_id'),
            'created_by' => $data['created_by'] ?? auth()->id(),
        ];

        return LedgerEntry::create(array_merge($extraData, $data));
    }

    /**
     * Post Sales Invoice to ledger automatically.
     */
    public function postSalesInvoice($invoice)
    {
        return DB::transaction(function () use ($invoice) {
            // Find mapped accounts or defaults
            $arAccount = $this->getMappedAccount('sales', 'receivable'); // Priority: Mapping -> Code '103' -> sub_ledger_type 'customer'
            $salesAccount = $this->getMappedAccount('sales', 'revenue'); // Priority: Mapping -> Code '401'
            $taxAccount = $this->getMappedAccount('tax', 'payable');     // Priority: Mapping -> Code '202'

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
            $apAccount = $this->getMappedAccount('purchase', 'payable'); // Priority: Mapping -> Code '201' -> sub_ledger_type 'vendor'
            $inventoryAccount = $this->getMappedAccount('stock', 'inventory'); // Priority: Mapping -> Code '104'
            $taxAccount = $this->getMappedAccount('tax', 'payable'); // Priority: Mapping -> Code '202'

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
            $ledgerData = [
                'chart_of_account_id' => $targetAccountId,
                'transaction_date' => $voucher->voucher_date,
                'reference_type' => 'payment_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => $voucher->amount,
                'credit' => 0,
                'description' => $voucher->description ?? 'Payment Voucher: ' . $voucher->voucher_number,
                'beneficiary_id' => $voucher->beneficiary_id,
                'beneficiary_type' => $voucher->beneficiary_type,
            ];

            if ($voucher->beneficiary_type === 'App\Models\Employee') {
                $ledgerData['employee_id'] = $voucher->beneficiary_id;
            } elseif ($voucher->beneficiary_type === 'App\Models\Vendor') {
                $ledgerData['vendor_id'] = $voucher->beneficiary_id;
            } elseif ($voucher->beneficiary_type === 'App\Models\Customer') {
                $ledgerData['customer_id'] = $voucher->beneficiary_id;
            }

            if (!isset($ledgerData['vendor_id']) && $voucher->vendor_id) {
                $ledgerData['vendor_id'] = $voucher->vendor_id;
            }

            $this->createLedgerEntry($ledgerData);

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
            $bankAccount->decrement('current_balance', (float) $voucher->amount);

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
            $ledgerData = [
                'chart_of_account_id' => $targetAccountId,
                'transaction_date' => $voucher->voucher_date,
                'reference_type' => 'receipt_voucher',
                'reference_id' => $voucher->id,
                'reference_number' => $voucher->voucher_number,
                'debit' => 0,
                'credit' => $voucher->amount,
                'description' => $voucher->description ?? 'Receipt Voucher: ' . $voucher->voucher_number,
                'beneficiary_id' => $voucher->beneficiary_id,
                'beneficiary_type' => $voucher->beneficiary_type,
            ];

            if ($voucher->beneficiary_type === 'App\Models\Employee') {
                $ledgerData['employee_id'] = $voucher->beneficiary_id;
            } elseif ($voucher->beneficiary_type === 'App\Models\Vendor') {
                $ledgerData['vendor_id'] = $voucher->beneficiary_id;
            } elseif ($voucher->beneficiary_type === 'App\Models\Customer') {
                $ledgerData['customer_id'] = $voucher->beneficiary_id;
            }

            if (!isset($ledgerData['customer_id']) && $voucher->customer_id) {
                $ledgerData['customer_id'] = $voucher->customer_id;
            }

            $this->createLedgerEntry($ledgerData);

            // Update balances
            $bankAccount->increment('current_balance', (float) $voucher->amount);

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
            $inventoryAcc = $this->getMappedAccount('stock', 'inventory'); // Inventory Asset
            $adjustmentAcc = $this->getMappedAccount('stock', 'adjustment'); // Inventory Adjustment Expense

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
        $companyId = session('active_company_id') ?? auth()->user()?->company_id;
        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id;

        $account = ChartOfAccount::withoutGlobalScope('tenant')
            ->where('code', $code)
            ->where('company_id', $companyId)
            ->first();

        if ($account) {
            return $account;
        }

        return ChartOfAccount::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'code' => $code,
            'name_en' => $this->getDefaultAccountName($code),
            'type' => $this->getAccountTypeByCode($code),
            'sub_ledger_type' => $this->getSubLedgerTypeByCode($code),
            'is_active' => true,
            'is_posting_allowed' => true,
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


    /**
     * Get account by mapping or default code.
     */
    public function getMappedAccount($module, $key)
    {
        $mapping = \App\Models\AccountMapping::where('module', $module)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if ($mapping) {
            return $mapping->chartOfAccount;
        }

        // Default mappings if not explicitly defined
        $defaults = [
            'stock' => [
                'inventory' => '104',
                'adjustment' => '508',
            ],
            'sales' => [
                'receivable' => '103',
                'revenue' => '401',
            ],
            'purchase' => [
                'payable' => '201',
                'clearing' => '101',
            ],
            'transport' => [
                'expense' => '506',
                'clearing' => '101',
            ],
            'tax' => [
                'payable' => '202',
            ],
        ];

        $code = $defaults[$module][$key] ?? null;

        if ($code) {
            // Priority 1: Check if an account with this code already exists (Tenant aware)
            $companyId = session('active_company_id') ?? auth()->user()?->company_id;
            $existingByCode = ChartOfAccount::where('code', $code)
                ->where('company_id', $companyId)
                ->first();

            if ($existingByCode) {
                return $existingByCode;
            }

            // Priority 2: If it's a sub-ledger account, check if ANY account has that sub-ledger type (to avoid duplication like 103 vs 00101)
            $subLedgerType = $this->getSubLedgerTypeByCode($code);
            if ($subLedgerType !== 'none') {
                $existingBySubLedger = ChartOfAccount::where('sub_ledger_type', $subLedgerType)
                    ->where('company_id', $companyId)
                    ->orderBy('code', 'asc') // Use the first one (likely the one user/seeder intended)
                    ->first();

                if ($existingBySubLedger) {
                    return $existingBySubLedger;
                }
            }

            // Priority 3: Create it if it doesn't exist
            return $this->getAccountByCode($code);
        }

        return null;
    }

    private function getSubLedgerTypeByCode($code)
    {
        return match ($code) {
            '103' => 'customer',
            '105' => 'employee', // Accounts Receivable / Advances
            '201' => 'vendor',
            '501' => 'employee', // Accounts Payable / Salaries
            default => 'none',
        };
    }

    /**
     * Post Stock Receiving to ledger.
     */
    public function postStockReceiving($receiving)
    {
        return DB::transaction(function () use ($receiving) {
            $inventoryAcc = $this->getMappedAccount('stock', 'inventory');
            $apAccount = $this->getMappedAccount('stock', 'ap');

            $totalValue = 0;
            foreach ($receiving->items as $item) {
                // If unit_cost is not directly on receiving_item, we might need to fetch from product or stock_supply
                $unitCost = $item->product->average_cost ?? 0;
                $totalValue += $unitCost * $item->received_quantity;
            }

            if ($totalValue <= 0)
                return true;

            // Debit Inventory
            $this->createLedgerEntry([
                'chart_of_account_id' => $inventoryAcc->id,
                'transaction_date' => $receiving->receiving_date,
                'reference_type' => 'stock_receiving',
                'reference_id' => $receiving->id,
                'reference_number' => $receiving->document_number,
                'debit' => $totalValue,
                'credit' => 0,
                'description' => 'Stock Receiving: ' . $receiving->document_number,
            ]);

            // Credit AP
            $this->createLedgerEntry([
                'chart_of_account_id' => $apAccount->id,
                'transaction_date' => $receiving->receiving_date,
                'reference_type' => 'stock_receiving',
                'reference_id' => $receiving->id,
                'reference_number' => $receiving->document_number,
                'debit' => 0,
                'credit' => $totalValue,
                'description' => 'Payable for Receiving: ' . $receiving->document_number,
                'vendor_id' => $receiving->vendor_id,
            ]);

            return true;
        });
    }

    /**
     * Post Stock Supply to ledger.
     */
    public function postStockSupply($supply)
    {
        return DB::transaction(function () use ($supply) {
            $inventoryAcc = $this->getMappedAccount('stock', 'inventory');
            $apAccount = $this->getMappedAccount('stock', 'ap');

            // Credit AP
            $this->createLedgerEntry([
                'chart_of_account_id' => $apAccount->id,
                'transaction_date' => $supply->supply_date,
                'reference_type' => 'stock_supply',
                'reference_id' => $supply->id,
                'reference_number' => $supply->document_number,
                'debit' => 0,
                'credit' => $supply->total_amount,
                'description' => 'Stock Supply: ' . $supply->document_number,
                'vendor_id' => $supply->vendor_id,
            ]);

            // Debit Inventory
            $this->createLedgerEntry([
                'chart_of_account_id' => $inventoryAcc->id,
                'transaction_date' => $supply->supply_date,
                'reference_type' => 'stock_supply',
                'reference_id' => $supply->id,
                'reference_number' => $supply->document_number,
                'debit' => $supply->total_amount,
                'credit' => 0,
                'description' => 'Inventory from Supply: ' . $supply->document_number,
            ]);

            return true;
        });
    }

    /**
     * Post Stock Issue to ledger.
     */
    public function postStockIssue($issueOrder)
    {
        return DB::transaction(function () use ($issueOrder) {
            $inventoryAcc = $this->getMappedAccount('stock', 'inventory');
            $adjustmentAcc = $this->getMappedAccount('stock', 'adjustment');

            $totalCost = 0;
            foreach ($issueOrder->items as $item) {
                $unitCost = $item->product->average_cost ?? 0;
                $totalCost += $unitCost * $item->issued_quantity;
            }

            if ($totalCost <= 0)
                return true;

            // Credit Inventory
            $this->createLedgerEntry([
                'chart_of_account_id' => $inventoryAcc->id,
                'transaction_date' => $issueOrder->issue_date,
                'reference_type' => 'stock_issue',
                'reference_id' => $issueOrder->id,
                'reference_number' => $issueOrder->document_number,
                'debit' => 0,
                'credit' => $totalCost,
                'description' => 'Stock Issue: ' . $issueOrder->document_number,
            ]);

            // Debit Adjustment Expense (or Cost Center account)
            $this->createLedgerEntry([
                'chart_of_account_id' => $adjustmentAcc->id,
                'transaction_date' => $issueOrder->issue_date,
                'reference_type' => 'stock_issue',
                'reference_id' => $issueOrder->id,
                'reference_number' => $issueOrder->document_number,
                'debit' => $totalCost,
                'credit' => 0,
                'description' => 'Inventory Issue Expense: ' . $issueOrder->document_number,
                'cost_center_no' => $issueOrder->cost_center_no,
            ]);

            return true;
        });
    }

    /**
     * Post Sales Return to ledger automatically.
     */
    public function postSalesReturn($return)
    {
        return DB::transaction(function () use ($return) {
            // Find mapped accounts or defaults
            $arAccount = $this->getMappedAccount('sales', 'receivable');
            $salesAccount = $this->getMappedAccount('sales', 'revenue');
            $taxAccount = $this->getMappedAccount('tax', 'payable');

            // Credit Target Account (AR or Cash/Bank)
            if ($return->return_type === 'cash' && $return->bank_account_id) {
                $bankAccount = \App\Models\BankAccount::find($return->bank_account_id);
                $targetAccountId = $bankAccount->chart_of_account_id;
                $description = 'Sales Return (Cash): ' . $return->return_number;
            } else {
                $targetAccountId = $arAccount->id;
                $description = 'Sales Return (Credit): ' . $return->return_number . ' (Ref: ' . ($return->salesInvoice->invoice_number ?? 'N/A') . ')';
            }

            $this->createLedgerEntry([
                'chart_of_account_id' => $targetAccountId,
                'transaction_date' => $return->return_date,
                'reference_type' => 'sales_return',
                'reference_id' => $return->id,
                'reference_number' => $return->return_number,
                'debit' => 0,
                'credit' => $return->total_amount,
                'description' => $description,
                'customer_id' => $return->customer_id,
            ]);

            // Debit Sales (Revenue Reversal)
            $this->createLedgerEntry([
                'chart_of_account_id' => $salesAccount->id,
                'transaction_date' => $return->return_date,
                'reference_type' => 'sales_return',
                'reference_id' => $return->id,
                'reference_number' => $return->return_number,
                'debit' => $return->subtotal,
                'credit' => 0,
                'description' => 'Revenue Reversal from Sales Return: ' . $return->return_number,
            ]);

            // Debit Tax (Tax Reversal)
            if ($return->tax_amount > 0) {
                $this->createLedgerEntry([
                    'chart_of_account_id' => $taxAccount->id,
                    'transaction_date' => $return->return_date,
                    'reference_type' => 'sales_return',
                    'reference_id' => $return->id,
                    'reference_number' => $return->return_number,
                    'debit' => $return->tax_amount,
                    'credit' => 0,
                    'description' => 'Tax Reversal on Sales Return: ' . $return->return_number,
                ]);
            }

            return true;
        });
    }

    /**
     * Post Local Purchase to ledger automatically.
     */
    public function postLocalPurchase($purchase)
    {
        return DB::transaction(function () use ($purchase) {
            $inventoryAccount = $this->getMappedAccount('stock', 'inventory');
            $taxAccount = $this->getMappedAccount('tax', 'payable');
            // For local purchases, we usually credit a cash/bank clearing account
            $clearingAccount = $this->getMappedAccount('purchase', 'clearing');

            // Debit Inventory
            $this->createLedgerEntry([
                'chart_of_account_id' => $inventoryAccount->id,
                'transaction_date' => $purchase->invoice_date,
                'reference_type' => 'local_purchase',
                'reference_id' => $purchase->id,
                'reference_number' => $purchase->document_number,
                'debit' => $purchase->subtotal,
                'credit' => 0,
                'description' => 'Local Purchase: ' . $purchase->document_number . ' - ' . $purchase->supplier_name,
            ]);

            // Debit Tax (Input Tax)
            if ($purchase->tax_amount > 0) {
                $this->createLedgerEntry([
                    'chart_of_account_id' => $taxAccount->id,
                    'transaction_date' => $purchase->invoice_date,
                    'reference_type' => 'local_purchase',
                    'reference_id' => $purchase->id,
                    'reference_number' => $purchase->document_number,
                    'debit' => $purchase->tax_amount,
                    'credit' => 0,
                    'description' => 'Input Tax on Local Purchase: ' . $purchase->document_number,
                ]);
            }

            // Credit Clearing Account
            $this->createLedgerEntry([
                'chart_of_account_id' => $clearingAccount->id,
                'transaction_date' => $purchase->invoice_date,
                'reference_type' => 'local_purchase',
                'reference_id' => $purchase->id,
                'reference_number' => $purchase->document_number,
                'debit' => 0,
                'credit' => $purchase->total_amount,
                'description' => 'Clearing for Local Purchase: ' . $purchase->document_number,
            ]);

            return true;
        });
    }

    /**
     * Unpost Local Purchase from ledger.
     */
    public function unpostLocalPurchase($purchase)
    {
        return DB::transaction(function () use ($purchase) {
            LedgerEntry::where('reference_type', 'local_purchase')
                ->where('reference_id', $purchase->id)
                ->delete();

            return true;
        });
    }

    /**
     * Post Transport Claim to ledger.
     */
    public function postTransportClaim($claim)
    {
        return DB::transaction(function () use ($claim) {
            // 506: Fuel & Transport Expense
            $expenseAccount = $this->getMappedAccount('transport', 'expense');
            $clearingAccount = $this->getMappedAccount('transport', 'clearing');

            $amount = $claim->settled_amount ?? $claim->claim_amount;

            if ($amount <= 0) {
                return true;
            }

            // Debit Transport Expense
            $this->createLedgerEntry([
                'chart_of_account_id' => $expenseAccount->id,
                'transaction_date' => $claim->settled_at ?? $claim->claim_date,
                'reference_type' => 'transport_claim',
                'reference_id' => $claim->id,
                'reference_number' => $claim->claim_number,
                'debit' => $amount,
                'credit' => 0,
                'description' => 'Transport Claim Expense: ' . $claim->claim_number . ' (Order: ' . $claim->transportOrder->document_number . ')',
            ]);

            // Credit Clearing Account
            $this->createLedgerEntry([
                'chart_of_account_id' => $clearingAccount->id,
                'transaction_date' => $claim->settled_at ?? $claim->claim_date,
                'reference_type' => 'transport_claim',
                'reference_id' => $claim->id,
                'reference_number' => $claim->claim_number,
                'debit' => 0,
                'credit' => $amount,
                'description' => 'Clearing for Transport Claim: ' . $claim->claim_number,
            ]);

            return true;
        });
    }
}
