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
        Schema::create('sales_contract_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_contract_id')->constrained('sales_contracts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('approval_order');
            $table->enum('approval_stage', ['initial', 'final']);
            $table->string('approval_group');
            $table->string('position');
            $table->string('approver_name');
            $table->string('approver_username', 45);
            $table->enum('status', ['Pending', 'Approved'])->default('Pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['sales_contract_id', 'approver_username'], 'sca_contract_username_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_contract_approvals');
    }
};
