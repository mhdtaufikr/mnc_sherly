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
        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->id();

            $table->string('contract_number')->unique();
            $table->string('buyer_name');
            $table->string('buyer_reference')->nullable();
            $table->enum('seller_entity', ['PMC', 'IBPE', 'APE']);
            $table->enum('market_type', ['Domestic', 'Export']);
            $table->string('pic_marketing')->nullable();
            $table->date('submission_date')->nullable();
            $table->string('submitted_by')->nullable();
            $table->enum('draft_status', ['Draft', 'Under Review', 'Pending Approval', 'Confirmed', 'Cancelled'])->default('Draft');

            $table->enum('commodity', ['Cooking Indonesian Origin', 'Non Cooking Indonesian Origin']);
            $table->decimal('contract_quantity_mt', 15, 2)->nullable();
            $table->decimal('sales_quantity_mt', 15, 2)->nullable();
            $table->char('shipment_period', 7)->nullable();
            $table->enum('incoterms', ['FOB', 'CIF']);

            $table->enum('gar_gcv', ['2700', '2800', '3000', '3500'])->nullable();
            $table->string('actual_gar')->nullable();
            $table->string('total_moisture')->nullable();
            $table->string('inherent_moisture')->nullable();
            $table->string('ash')->nullable();
            $table->string('ash_limit')->nullable();
            $table->string('sulphur')->nullable();
            $table->string('sulphur_limit')->nullable();
            $table->enum('size', ['No Sizing', 'Sizing'])->nullable();

            $table->string('pricing_basis')->default('ICI');
            $table->enum('price_type', ['Fixed Price', 'Formula'])->nullable();
            $table->decimal('fixed_price', 15, 2)->nullable();
            $table->enum('price_currency', ['USD', 'IDR'])->nullable();
            $table->text('formula_price')->nullable();
            $table->decimal('minus_plus', 15, 2)->nullable();
            $table->text('payment_term_summary')->nullable();

            $table->string('shipment_no')->nullable();
            $table->enum('barges', ['FOB Barge', 'FOB MV GNG', 'FOB MV Gearless'])->nullable();
            $table->date('eta')->nullable();
            $table->date('laycan_start_date')->nullable();
            $table->date('laycan_end_date')->nullable();
            $table->string('load_port')->nullable();
            $table->string('destination_port')->nullable();
            $table->string('tug_boat_name')->nullable();
            $table->string('barge_vessel_name')->nullable();
            $table->string('barge_vessel_agent')->nullable();
            $table->enum('dmo_status', ['DMO', 'Non DMO'])->nullable();
            $table->string('surveyor')->nullable();
            $table->enum('laycan_status', ['Confirm', 'Nego Laycan'])->nullable();

            $table->enum('approval_status', ['Half Signed', 'Full Signed'])->nullable();
            $table->string('approved_by')->nullable();
            $table->date('approval_date')->nullable();
            $table->text('revision_note')->nullable();
            $table->enum('final_status', ['Confirmed', 'Loading', 'On Hold', 'Revision', 'Cancelled', 'Complete'])->nullable();
            $table->string('contract_file_path')->nullable();
            $table->string('contract_file_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_contracts');
    }
};
