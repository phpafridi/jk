<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('cnic')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Now that owners table exists, add the FK on shops.owner_id
        Schema::table('shops', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('owners')->nullOnDelete();
        });

        // Now that owners table exists, add the FK on owner_ledgers.owner_id
        Schema::table('owner_ledgers', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('owners')->nullOnDelete();
        });

        // Add customer/owner linking columns to sell_purchase_entries
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_customer_id')->nullable();
            $table->unsignedBigInteger('buyer_customer_id')->nullable();
            $table->unsignedBigInteger('seller_owner_id')->nullable();
            $table->unsignedBigInteger('buyer_owner_id')->nullable();
            $table->foreign('seller_customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('buyer_customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('seller_owner_id')->references('id')->on('owners')->nullOnDelete();
            $table->foreign('buyer_owner_id')->references('id')->on('owners')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            $table->dropForeign(['seller_customer_id']);
            $table->dropForeign(['buyer_customer_id']);
            $table->dropForeign(['seller_owner_id']);
            $table->dropForeign(['buyer_owner_id']);
            $table->dropColumn(['seller_customer_id','buyer_customer_id','seller_owner_id','buyer_owner_id']);
        });

        Schema::table('owner_ledgers', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::dropIfExists('owners');
    }
};
