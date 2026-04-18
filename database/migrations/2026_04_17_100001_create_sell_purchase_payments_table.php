<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Table for tracking multiple payment installments on sell/purchase entries
        Schema::create('sell_purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_purchase_entry_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('cash');
            $table->date('date');
            $table->string('received_by')->nullable();
            $table->string('notes')->nullable();
            // Invoice image / document for this payment
            $table->string('invoice_path')->nullable();
            $table->string('invoice_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_purchase_payments');
    }
};
