<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // property_dealer on shops (instalment market)
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'property_dealer'))
                $table->string('property_dealer')->nullable()->after('shop_number');
        });

        // property_dealer on rent_shops
        Schema::table('rent_shops', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_shops', 'property_dealer'))
                $table->string('property_dealer')->nullable()->after('shop_number');
        });

        // paid_to + authorized_by on shop_payments (instalment)
        Schema::table('shop_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_payments', 'paid_to'))
                $table->string('paid_to')->nullable()->after('received_by');
            if (!Schema::hasColumn('shop_payments', 'authorized_by'))
                $table->string('authorized_by')->nullable()->after('paid_to');
        });

        // paid_to + authorized_by on rent_entries
        Schema::table('rent_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_entries', 'paid_to'))
                $table->string('paid_to')->nullable()->after('received_by');
            if (!Schema::hasColumn('rent_entries', 'authorized_by'))
                $table->string('authorized_by')->nullable()->after('paid_to');
        });

        // paid_to + authorized_by on sell_purchase_entries
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('sell_purchase_entries', 'paid_to'))
                $table->string('paid_to')->nullable()->after('received_by');
            if (!Schema::hasColumn('sell_purchase_entries', 'authorized_by'))
                $table->string('authorized_by')->nullable()->after('paid_to');
        });
    }

    public function down(): void
    {
        Schema::table('shops', fn($t) => $t->dropColumn('property_dealer'));
        Schema::table('rent_shops', fn($t) => $t->dropColumn('property_dealer'));
        Schema::table('shop_payments', fn($t) => $t->dropColumn(['paid_to','authorized_by']));
        Schema::table('rent_entries', fn($t) => $t->dropColumn(['paid_to','authorized_by']));
        Schema::table('sell_purchase_entries', fn($t) => $t->dropColumn(['paid_to','authorized_by']));
    }
};
