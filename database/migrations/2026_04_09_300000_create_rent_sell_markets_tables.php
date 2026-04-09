<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Rent Markets ──────────────────────────────────────────
        Schema::create('rent_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Rent Shops ────────────────────────────────────────────
        Schema::create('rent_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_market_id')->constrained()->cascadeOnDelete();
            $table->string('shop_number');
            $table->string('status')->default('available'); // available, rented, inactive
            $table->decimal('rent_amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Sell Markets ──────────────────────────────────────────
        Schema::create('sell_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Sell Shops ────────────────────────────────────────────
        Schema::create('sell_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_market_id')->constrained()->cascadeOnDelete();
            $table->string('shop_number');
            $table->string('type')->default('shop'); // shop, plot
            $table->string('status')->default('available'); // available, sold, inactive
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Migrate rent_entries: swap shop_id → rent_shop_id ────
        Schema::table('rent_entries', function (Blueprint $table) {
            if (Schema::hasColumn('rent_entries', 'shop_id')) {
                $table->dropConstrainedForeignId('shop_id');
            }
            if (Schema::hasColumn('rent_entries', 'owner_id')) {
                $table->dropConstrainedForeignId('owner_id');
            }
            if (!Schema::hasColumn('rent_entries', 'rent_shop_id')) {
                $table->foreignId('rent_shop_id')->nullable()->constrained('rent_shops')->nullOnDelete();
            }
            if (!Schema::hasColumn('rent_entries', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            }
        });

        // ── Migrate sell_purchase_entries: swap market_id → sell_market_id ──
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            if (Schema::hasColumn('sell_purchase_entries', 'market_id')) {
                $table->dropConstrainedForeignId('market_id');
            }
            if (Schema::hasColumn('sell_purchase_entries', 'owner_id')) {
                $table->dropConstrainedForeignId('owner_id');
            }
            if (!Schema::hasColumn('sell_purchase_entries', 'sell_market_id')) {
                $table->foreignId('sell_market_id')->nullable()->constrained('sell_markets')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sell_market_id');
        });
        Schema::table('rent_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rent_shop_id');
            $table->dropConstrainedForeignId('customer_id');
        });
        Schema::dropIfExists('sell_shops');
        Schema::dropIfExists('sell_markets');
        Schema::dropIfExists('rent_shops');
        Schema::dropIfExists('rent_markets');
    }
};
