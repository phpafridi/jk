<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add payment_method to rent_entries (received_by already exists)
        Schema::table('rent_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_entries', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('received_by');
            }
        });

        // Add payment tracking to sell_purchase_entries
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('sell_purchase_entries', 'amount_paid')) {
                $table->decimal('amount_paid', 15, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('sell_purchase_entries', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('amount_paid');
            }
            if (!Schema::hasColumn('sell_purchase_entries', 'received_by')) {
                $table->string('received_by')->nullable()->after('payment_method');
            }
        });

        // Add received_by to shop_payments (instalment)
        Schema::table('shop_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_payments', 'received_by')) {
                $table->string('received_by')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'payment_method', 'received_by']);
        });
        Schema::table('rent_entries', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('shop_payments', function (Blueprint $table) {
            $table->dropColumn('received_by');
        });
    }
};
