<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('users')->upsert([
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@mnc-project.local',
                'email_verified_at' => null,
                'password' => Hash::make('Password.1'),
                'remember_token' => null,
                'role' => 'admin',
                'status' => 'ACTIVE',
                'is_active' => true,
                'login_counter' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'User',
                'username' => 'user',
                'email' => 'user@mnc-project.local',
                'email_verified_at' => null,
                'password' => Hash::make('Password.1'),
                'remember_token' => null,
                'role' => 'user',
                'status' => 'ACTIVE',
                'is_active' => true,
                'login_counter' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ], ['email'], [
            'name',
            'username',
            'password',
            'role',
            'status',
            'is_active',
            'updated_at',
        ]);
    }
}
