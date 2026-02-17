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

        $sequence->increment('current_number');

        return $sequence->prefix . '-' . date('Y') . '-' . str_pad($sequence->current_number, $sequence->padding, '0', STR_PAD_LEFT);
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
