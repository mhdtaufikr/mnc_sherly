<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DropdownsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('dropdowns')->upsert([
            [
                'category' => 'role',
                'name_value' => 'Admin',
                'code_format' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'role',
                'name_value' => 'User',
                'code_format' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['category', 'code_format'], [
            'name_value',
            'updated_at',
        ]);
    }
}
