<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 45)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('plant', 45)->nullable();
            $table->string('type', 45)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->unsignedInteger('login_counter')->default(0);
            $table->enum('status', ['ACTIVE', 'SUSPENDED', 'LOCKED'])->default('ACTIVE');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
