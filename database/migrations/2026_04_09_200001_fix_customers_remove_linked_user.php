<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // linked_user_id was stored as plain unsignedBigInteger (no FK), nothing to drop
    }

    public function down(): void
    {
        //
    }
};
