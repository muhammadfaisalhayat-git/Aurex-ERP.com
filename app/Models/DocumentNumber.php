<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class DocumentNumber extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'entity_type',
        'prefix',
        'current_number',
        'padding',
        'year',
    ];

    public static function generate($entityType, $prefix = null)
    {
        $criteria = [
            'entity_type' => $entityType,
            'company_id' => session('active_company_id')
        ];

        if (session()->has('active_branch_id')) {
            $criteria['branch_id'] = session('active_branch_id');
        }

        $sequence = self::firstOrCreate(
            $criteria,
            [
                'prefix' => $prefix ?? self::getDefaultPrefix($entityType),
                'current_number' => 0,
                'padding' => 5,
                'year' => date('Y'),
            ]
        );

        // Reset if year changed
        if ($sequence->year != date('Y')) {
            $sequence->update([
                'year' => date('Y'),
                'current_number' => 0,
            ]);
        }

        $modelClass = self::getModelForEntity($entityType);
        $finalNumber = null;

        // Self-healing loop: increment until we find a number that doesn't exist in the database
        do {
            $sequence->increment('current_number');
            $finalNumber = $sequence->prefix . '-' . date('Y') . '-' . str_pad($sequence->current_number, $sequence->padding, '0', STR_PAD_LEFT);

            // If we have a model mapping, check for uniqueness globally (across all branches)
            if ($modelClass) {
                // We check globally because prefixes like "SI" might be shared but validation is global
                $exists = $modelClass::where('document_number', $finalNumber)
                    ->orWhere('invoice_number', $finalNumber) // Special case for invoices
                    ->exists();
            } else {
                $exists = false;
            }
        } while ($exists);

        return $finalNumber;
    }

    protected static function getModelForEntity($entityType)
    {
        $map = [
            'sales_invoice' => \App\Models\SalesInvoice::class,
            'sales_order' => \App\Models\SalesOrder::class,
            'quotation' => \App\Models\Quotation::class,
            'customer_request' => \App\Models\CustomerRequest::class,
            'purchase_invoice' => \App\Models\PurchaseInvoice::class,
            'supply_order' => \App\Models\SupplyOrder::class,
            'stock_issue' => \App\Models\StockIssueOrder::class,
            'stock_receiving' => \App\Models\StockReceiving::class,
            'stock_transfer' => \App\Models\StockTransfer::class,
            'transport_order' => \App\Models\TransportOrder::class,
        ];

        return $map[$entityType] ?? null;
    }

    protected static function getDefaultPrefix($entityType)
    {
        $prefixes = [
            'sales_invoice' => 'SI',
            'sales_return' => 'SR',
            'quotation' => 'QT',
            'customer_request' => 'CR',
            'sales_contract' => 'SC',
            'purchase_invoice' => 'PI',
            'stock_supply' => 'SS',
            'stock_receiving' => 'SR',
            'stock_transfer' => 'ST',
            'stock_issue' => 'SI',
            'transfer_request' => 'TR',
            'composite_assembly' => 'CA',
            'transport_order' => 'TO',
            'transport_contract' => 'TC',
            'transport_claim' => 'TCM',
            'maintenance_voucher' => 'MV',
            'commission_run' => 'CMR',
        ];

        return $prefixes[$entityType] ?? 'DOC';
    }
}
