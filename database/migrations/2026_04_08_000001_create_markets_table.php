<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('shop_number');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('instalment'); // instalment, rent, sell, purchase
            $table->date('date_of_payment')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('rent_amount', 15, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shop_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // who recorded
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash');
            $table->text('notes')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('type')->default('image'); // image, pdf, doc
            $table->timestamps();
        });

        Schema::create('rent_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('shop_number');
            $table->decimal('rent', 15, 2);
            $table->date('date');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('received_by')->nullable();
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sell_purchase_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_type'); // car, shop, plot
            $table->string('transaction_type'); // sell, purchase
            $table->foreignId('market_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->string('shop_or_item_number')->nullable();
            $table->decimal('per_sqft_rate', 15, 2)->nullable();
            $table->decimal('sqft', 10, 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->string('seller_name')->nullable();
            $table->string('seller_cnic')->nullable();
            $table->string('seller_phone')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_cnic')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('car_make')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_year')->nullable();
            $table->string('car_registration')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('construction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_name');
            $table->string('item_name');
            $table->decimal('quantity', 10, 2);
            $table->string('unit'); // sqft, meters, kg, pieces etc.
            $table->string('measurement')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('owner_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('market_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_type'); // debit, credit
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('cnic')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('linked_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('type')->default('document');
            $table->timestamps();
        });

        Schema::create('entry_documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('name');
            $table->string('path');
            $table->string('type')->default('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_documents');
        Schema::dropIfExists('customer_documents');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('owner_ledgers');
        Schema::dropIfExists('construction_items');
        Schema::dropIfExists('sell_purchase_entries');
        Schema::dropIfExists('rent_entries');
        Schema::dropIfExists('shop_documents');
        Schema::dropIfExists('shop_payments');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('markets');
    }
};
