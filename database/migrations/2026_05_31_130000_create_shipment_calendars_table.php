<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('buyer');
            $table->string('contract_no');
            $table->date('laycan_start');
            $table->date('laycan_end')->nullable();
            $table->date('eta')->nullable();
            $table->string('vessel');
            $table->decimal('qty', 15, 2)->default(0);
            $table->string('spec')->nullable();
            $table->enum('laycan_status', ['Confirmed', 'Loading', 'Complete'])->default('Confirmed');
            $table->string('discharge_port');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_calendars');
    }
};
