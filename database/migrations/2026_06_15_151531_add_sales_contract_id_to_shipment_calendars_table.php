<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipment_calendars', function (Blueprint $table) {
            $table->foreignId('sales_contract_id')
                ->nullable()
                ->after('id')
                ->constrained('sales_contracts')
                ->nullOnDelete();

            $table->unique('sales_contract_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_calendars', function (Blueprint $table) {
            $table->dropUnique(['sales_contract_id']);
            $table->dropConstrainedForeignId('sales_contract_id');
        });
    }
};
