<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rent_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_market_id')->constrained()->cascadeOnDelete();
            $table->string('shop_number');
            $table->string('status')->default('available');
            $table->decimal('rent_amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sell_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sell_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_market_id')->constrained()->cascadeOnDelete();
            $table->string('shop_number');
            $table->string('type')->default('shop');
            $table->string('status')->default('available');
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // rent_entries: shop_id was plain unsignedBigInteger (no FK to drop)
        // just add rent_shop_id and customer_id
        Schema::table('rent_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_entries', 'rent_shop_id')) {
                $table->foreignId('rent_shop_id')->nullable()->constrained('rent_shops')->nullOnDelete();
            }
            if (!Schema::hasColumn('rent_entries', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            }
        });

        // sell_purchase_entries: market_id was plain unsignedBigInteger (no FK to drop)
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('sell_purchase_entries', 'sell_market_id')) {
                $table->foreignId('sell_market_id')->nullable()->constrained('sell_markets')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            $table->dropForeign(['sell_market_id']);
            $table->dropColumn('sell_market_id');
        });
        Schema::table('rent_entries', function (Blueprint $table) {
            $table->dropForeign(['rent_shop_id']);
            $table->dropColumn('rent_shop_id');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
        Schema::dropIfExists('sell_shops');
        Schema::dropIfExists('sell_markets');
        Schema::dropIfExists('rent_shops');
        Schema::dropIfExists('rent_markets');
    }
};
