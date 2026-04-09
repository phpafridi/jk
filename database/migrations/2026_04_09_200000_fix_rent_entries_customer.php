<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rent_entries', function (Blueprint $table) {
            // Drop old owner_id if it exists
            if (Schema::hasColumn('rent_entries', 'owner_id')) {
                $table->dropConstrainedForeignId('owner_id');
            }
            // Add customer_id if not already present
            if (!Schema::hasColumn('rent_entries', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rent_entries', function (Blueprint $table) {
            if (Schema::hasColumn('rent_entries', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }
            if (!Schema::hasColumn('rent_entries', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }
};
