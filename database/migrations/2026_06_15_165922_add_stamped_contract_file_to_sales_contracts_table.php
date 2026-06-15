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
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->string('stamped_contract_file_path')->nullable()->after('contract_file_name');
            $table->string('stamped_contract_file_name')->nullable()->after('stamped_contract_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropColumn(['stamped_contract_file_path', 'stamped_contract_file_name']);
        });
    }
};
