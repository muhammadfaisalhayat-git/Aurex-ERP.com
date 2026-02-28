<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $blueprint) {
            // Identification
            $blueprint->string('name_foreign')->nullable()->after('name_ar');
            $blueprint->string('gtin')->nullable()->after('barcode');
            $blueprint->string('hsn_code')->nullable()->after('gtin');
            $blueprint->string('manufacturer_code')->nullable()->after('hsn_code');
            $blueprint->string('ref_code')->nullable()->after('manufacturer_code');

            // Financials
            $blueprint->decimal('primary_cost', 15, 2)->default(0)->after('cost_price');
            $blueprint->string('purchase_inv_no')->nullable()->after('primary_cost');
            $blueprint->integer('return_period')->default(0)->after('purchase_inv_no');

            // Physical Attributes
            $blueprint->decimal('length', 10, 2)->default(0)->after('volume');
            $blueprint->decimal('width', 10, 2)->default(0)->after('length');
            $blueprint->decimal('height', 10, 2)->default(0)->after('width');
            $blueprint->decimal('area', 10, 2)->default(0)->after('height');
            $blueprint->decimal('size_dimension', 10, 2)->default(0)->after('area');
            $blueprint->integer('decimals_count')->default(2)->after('size_dimension');

            // Other Data
            $blueprint->string('item_activity')->nullable();
            $blueprint->string('level')->nullable();
            $blueprint->string('measure')->nullable();
            $blueprint->string('color')->nullable();
            $blueprint->string('season')->nullable();
            $blueprint->string('material')->nullable();
            $blueprint->string('brand')->nullable();
            $blueprint->string('manufacturer_company')->nullable();
            $blueprint->string('country_of_origin')->nullable();
            $blueprint->string('items_storage')->nullable();
            $blueprint->string('weights_base')->nullable();
            $blueprint->date('inactivation_date')->nullable();
            $blueprint->text('deactivation_reason')->nullable();

            // Flags (Booleans)
            $blueprint->boolean('is_weighted')->default(false);
            $blueprint->boolean('is_reserved')->default(false);
            $blueprint->boolean('is_not_for_sale')->default(false);
            $blueprint->boolean('is_controlled')->default(false);
            $blueprint->boolean('allow_fractions')->default(false);
            $blueprint->boolean('sold_in_cash')->default(false);
            $blueprint->boolean('is_asset')->default(false);
            $blueprint->boolean('use_partition')->default(false);
            $blueprint->boolean('is_compound')->default(false);
            $blueprint->boolean('is_component')->default(false);
            $blueprint->boolean('is_non_returnable')->default(false);
            $blueprint->boolean('use_expiry_date')->default(false);
            $blueprint->boolean('is_requirement')->default(false);
            $blueprint->boolean('show_in_vss')->default(false);
            $blueprint->boolean('use_custodians')->default(false);
            $blueprint->boolean('use_in_crm')->default(false);
            $blueprint->boolean('has_alternatives')->default(false);
            $blueprint->boolean('item_code_as_serial')->default(false);
            $blueprint->boolean('show_in_css')->default(false);

            // Operational
            $blueprint->integer('return_period_before_expiry')->default(0);
            $blueprint->integer('no_of_printing_times')->default(0);
            $blueprint->integer('no_of_modifications')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'name_foreign',
                'gtin',
                'hsn_code',
                'manufacturer_code',
                'ref_code',
                'primary_cost',
                'purchase_inv_no',
                'return_period',
                'length',
                'width',
                'height',
                'area',
                'size_dimension',
                'decimals_count',
                'item_activity',
                'level',
                'measure',
                'color',
                'season',
                'material',
                'brand',
                'manufacturer_company',
                'country_of_origin',
                'items_storage',
                'weights_base',
                'inactivation_date',
                'deactivation_reason',
                'is_weighted',
                'is_reserved',
                'is_not_for_sale',
                'is_controlled',
                'allow_fractions',
                'sold_in_cash',
                'is_asset',
                'use_partition',
                'is_compound',
                'is_component',
                'is_non_returnable',
                'use_expiry_date',
                'is_requirement',
                'show_in_vss',
                'use_custodians',
                'use_in_crm',
                'has_alternatives',
                'item_code_as_serial',
                'show_in_css',
                'return_period_before_expiry',
                'no_of_printing_times',
                'no_of_modifications'
            ]);
        });
    }
};
