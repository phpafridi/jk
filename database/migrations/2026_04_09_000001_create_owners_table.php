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

        // Update shops: owner_id now references owners table
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')->references('id')->on('owners')->nullOnDelete();
        });

        // Update rent_entries: owner_id now references owners table
        Schema::table('rent_entries', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')->references('id')->on('owners')->nullOnDelete();
        });

        // Update owner_ledgers: owner_id now references owners table
        Schema::table('owner_ledgers', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')->references('id')->on('owners')->cascadeOnDelete();
        });

        // Add customer_id and owner_id to sell_purchase_entries for linking
        Schema::table('sell_purchase_entries', function (Blueprint $table) {
            $table->foreignId('seller_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('buyer_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('seller_owner_id')->nullable()->constrained('owners')->nullOnDelete();
            $table->foreignId('buyer_owner_id')->nullable()->constrained('owners')->nullOnDelete();
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
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('rent_entries', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::dropIfExists('owners');
    }
};
