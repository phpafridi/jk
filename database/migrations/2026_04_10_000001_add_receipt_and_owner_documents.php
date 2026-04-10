<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add receipt_number to rent_entries
        Schema::table('rent_entries', function (Blueprint $table) {
            $table->string('receipt_number')->nullable()->after('notes');
        });

        // Add doc_type to customer_documents and entry_documents (for labelling)
        if (Schema::hasTable('customer_documents') && !Schema::hasColumn('customer_documents', 'doc_type')) {
            Schema::table('customer_documents', function (Blueprint $table) {
                $table->string('doc_type')->default('other')->after('type'); // cnic, mou, agreement, photo, other
            });
        }

        if (Schema::hasTable('entry_documents') && !Schema::hasColumn('entry_documents', 'doc_type')) {
            Schema::table('entry_documents', function (Blueprint $table) {
                $table->string('doc_type')->default('other')->after('type');
            });
        }

        // Owner documents table
        Schema::create('owner_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('type')->default('document'); // image | document
            $table->string('doc_type')->default('other'); // cnic | mou | agreement | photo | other
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_documents');
        Schema::table('rent_entries', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });
    }
};
