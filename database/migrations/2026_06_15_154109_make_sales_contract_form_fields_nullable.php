<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropUnique(['contract_number']);
        });

        DB::statement('ALTER TABLE sales_contracts MODIFY contract_number VARCHAR(255) NULL');
        DB::statement('ALTER TABLE sales_contracts MODIFY buyer_name VARCHAR(255) NULL');
        DB::statement("ALTER TABLE sales_contracts MODIFY seller_entity ENUM('PMC','IBPE','APE') NULL");
        DB::statement("ALTER TABLE sales_contracts MODIFY market_type ENUM('Domestic','Export') NULL");
        DB::statement("ALTER TABLE sales_contracts MODIFY draft_status ENUM('Draft','Under Review','Pending Approval','Confirmed','Cancelled') NULL DEFAULT 'Draft'");
        DB::statement("ALTER TABLE sales_contracts MODIFY commodity ENUM('Cooking Indonesian Origin','Non Cooking Indonesian Origin') NULL");
        DB::statement('ALTER TABLE sales_contracts MODIFY shipment_period VARCHAR(30) NULL');
        DB::statement("ALTER TABLE sales_contracts MODIFY incoterms ENUM('FOB','CIF') NULL");

        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->unique('contract_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropUnique(['contract_number']);
        });

        DB::statement('ALTER TABLE sales_contracts MODIFY contract_number VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE sales_contracts MODIFY buyer_name VARCHAR(255) NOT NULL');
        DB::statement("ALTER TABLE sales_contracts MODIFY seller_entity ENUM('PMC','IBPE','APE') NOT NULL");
        DB::statement("ALTER TABLE sales_contracts MODIFY market_type ENUM('Domestic','Export') NOT NULL");
        DB::statement("ALTER TABLE sales_contracts MODIFY draft_status ENUM('Draft','Under Review','Pending Approval','Confirmed','Cancelled') NOT NULL DEFAULT 'Draft'");
        DB::statement("ALTER TABLE sales_contracts MODIFY commodity ENUM('Cooking Indonesian Origin','Non Cooking Indonesian Origin') NOT NULL");
        DB::statement('ALTER TABLE sales_contracts MODIFY shipment_period CHAR(7) NULL');
        DB::statement("ALTER TABLE sales_contracts MODIFY incoterms ENUM('FOB','CIF') NOT NULL");

        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->unique('contract_number');
        });
    }
};
