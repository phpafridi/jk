<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('owner_ledgers', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('reference');
            $table->string('invoice_path')->nullable()->after('payment_method');
            $table->string('invoice_name')->nullable()->after('invoice_path');
        });
    }

    public function down(): void
    {
        Schema::table('owner_ledgers', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'invoice_path', 'invoice_name']);
        });
    }
};
