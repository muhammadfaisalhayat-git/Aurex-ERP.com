<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add sub_ledger_type to Chart of Accounts
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            // 'none' = normal account
            // 'customer' = control account for Customers (AR)
            // 'vendor' = control account for Vendors (AP)
            if (!Schema::hasColumn('chart_of_accounts', 'sub_ledger_type')) {
                $table->enum('sub_ledger_type', ['none', 'customer', 'vendor'])->default('none')->after('type');
            }
        });

        // 2. Add customer_id and vendor_id to Journal Voucher Items
        Schema::table('journal_voucher_items', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_voucher_items', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('chart_of_account_id');
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            }
            if (!Schema::hasColumn('journal_voucher_items', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('customer_id');
                $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
            }
        });

        // 3. Add customer_id and vendor_id to Ledger Entries (for easy reporting)
        Schema::table('ledger_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('ledger_entries', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('chart_of_account_id');
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            }
            if (!Schema::hasColumn('ledger_entries', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('customer_id');
                $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            if (Schema::hasColumn('ledger_entries', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('ledger_entries', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('journal_voucher_items', function (Blueprint $table) {
            if (Schema::hasColumn('journal_voucher_items', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('journal_voucher_items', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('chart_of_accounts', 'sub_ledger_type')) {
                $table->dropColumn('sub_ledger_type');
            }
        });
    }
};
