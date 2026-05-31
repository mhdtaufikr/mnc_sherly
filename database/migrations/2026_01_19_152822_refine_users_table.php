<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 45)->nullable()->unique()->after('avatar');
            }

            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            }
        });

        if (Schema::hasColumn('users', 'is_active')) {
            DB::statement("
                UPDATE users
                SET is_active = CASE
                    WHEN is_active IN ('1','true','TRUE','active','ACTIVE') THEN 1
                    ELSE 0
                END
            ");

            DB::statement("
                ALTER TABLE users
                MODIFY is_active BOOLEAN NOT NULL DEFAULT 1
            ");
        }

        if (Schema::hasColumn('users', 'status')) {
            DB::statement("
                UPDATE users
                SET status = CASE
                    WHEN status IS NULL OR status = '' THEN 'ACTIVE'
                    WHEN UPPER(status) IN ('ACTIVE') THEN 'ACTIVE'
                    WHEN UPPER(status) IN ('SUSPENDED','INACTIVE','DISABLED') THEN 'SUSPENDED'
                    WHEN UPPER(status) IN ('LOCKED','BLOCKED') THEN 'LOCKED'
                    ELSE 'ACTIVE'
                END
            ");

            DB::statement("
                ALTER TABLE users
                MODIFY status ENUM('ACTIVE','SUSPENDED','LOCKED')
                NOT NULL DEFAULT 'ACTIVE'
            ");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('users', 'password_changed_at')) {
                $table->dropColumn('password_changed_at');
            }

            if (Schema::hasColumn('users', 'is_active')) {
                DB::statement("
                    ALTER TABLE users
                    MODIFY is_active VARCHAR(255)
                ");
            }

            if (Schema::hasColumn('users', 'status')) {
                DB::statement("
                    ALTER TABLE users
                    MODIFY status VARCHAR(45)
                ");
            }
        });
    }
};
