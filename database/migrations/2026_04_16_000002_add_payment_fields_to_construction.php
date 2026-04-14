<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('construction_items', function (Blueprint $table) {
            if (!Schema::hasColumn('construction_items', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('notes');
            }
            if (!Schema::hasColumn('construction_items', 'received_by')) {
                $table->string('received_by')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('construction_items', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('received_by');
            }
        });

        // Table for construction invoices/documents
        if (!Schema::hasTable('construction_documents')) {
            Schema::create('construction_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('construction_item_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('path');
                $table->string('type')->default('other'); // invoice, photo, receipt, other
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('construction_items', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'received_by', 'vendor_name']);
        });
        Schema::dropIfExists('construction_documents');
    }
};
