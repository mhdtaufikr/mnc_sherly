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
        DB::statement("ALTER TABLE sales_contracts MODIFY approval_status ENUM('Request Sign','Half Signed','Full Signed') NULL");
        DB::statement("ALTER TABLE sales_contracts MODIFY final_status ENUM('Wait for Approval','On Hold','Revision Approved') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE sales_contracts MODIFY approval_status ENUM('Half Signed','Full Signed') NULL");
        DB::statement("ALTER TABLE sales_contracts MODIFY final_status ENUM('Confirmed','Loading','On Hold','Revision','Cancelled','Complete') NULL");
    }
};
