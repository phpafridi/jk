<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Instalment shops: need a start date and monthly instalment amount
        Schema::table('shops', function (Blueprint $table) {
            $table->date('instalment_start_date')->nullable()->after('date_of_payment');
            $table->decimal('monthly_instalment', 15, 2)->nullable()->after('instalment_start_date');
        });

        // Rent shops: need a start date to track missed months
        Schema::table('rent_shops', function (Blueprint $table) {
            $table->date('rent_start_date')->nullable()->after('rent_amount');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['instalment_start_date', 'monthly_instalment']);
        });
        Schema::table('rent_shops', function (Blueprint $table) {
            $table->dropColumn('rent_start_date');
        });
    }
};
