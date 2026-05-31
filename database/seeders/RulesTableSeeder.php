<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RulesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('rules')->insert([
            [
                'rule_name' => 'EMAIL_REQUEST_ACCESS',
                'rule_value' => 'algifari.ramdhani@ptmkm.co.id',
            ],
            [
                'rule_name' => 'EMAIL_REQUEST_ACCESS',
                'rule_value' => 'muhammad.taufik@ptmkm.co.id',
            ]
        ]);
    }
}
