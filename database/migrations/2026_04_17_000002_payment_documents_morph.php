<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // EntryDocument already has documentable_type + documentable_id (polymorphic).
    // ShopPayment and RentEntry will use the same table via morphMany.
    // No schema change needed — just confirm the columns exist.
    public function up(): void
    {
        // EntryDocument table already supports morphs — nothing to add.
    }
    public function down(): void {}
};
