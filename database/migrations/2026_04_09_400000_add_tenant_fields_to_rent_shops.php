<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add tenant info columns to rent_shops
        Schema::table('rent_shops', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_shops', 'tenant_name')) {
                $table->string('tenant_name')->nullable()->after('shop_number');
            }
            if (!Schema::hasColumn('rent_shops', 'tenant_phone')) {
                $table->string('tenant_phone')->nullable()->after('tenant_name');
            }
            if (!Schema::hasColumn('rent_shops', 'tenant_cnic')) {
                $table->string('tenant_cnic')->nullable()->after('tenant_phone');
            }
        });

        // Make entry_documents polymorphic table support rent_shops
        // (documentable_type will store 'App\Models\RentShop')
        // No schema change needed — morphMany already works with existing entry_documents table
        // Just ensure documentable_id and documentable_type columns exist
        if (Schema::hasTable('entry_documents')) {
            Schema::table('entry_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('entry_documents', 'documentable_type')) {
                    $table->string('documentable_type')->nullable()->after('id');
                }
                if (!Schema::hasColumn('entry_documents', 'documentable_id')) {
                    $table->unsignedBigInteger('documentable_id')->nullable()->after('documentable_type');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('rent_shops', function (Blueprint $table) {
            $table->dropColumn(['tenant_name', 'tenant_phone', 'tenant_cnic']);
        });
    }
};
