<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dropdowns', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name_value');
            $table->string('code_format');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dropdowns');
    }
};

