<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'IT',
                'username' => 'it', // Added username
                'email' => 'it@ptmkm.co.id',
                'email_verified_at' => null,
                'password' => Hash::make('Password.1'), // Example hash for demonstration
                'remember_token' => null,
                'role' => 'IT',
                'last_login' => '2025-03-06 08:52:48', // Updated date to match your current data
                'login_counter' => 60, // Updated login counter
                'is_active' => '1',
                'created_at' => '2023-07-08 05:42:25',
                'updated_at' => '2025-03-06 08:52:48', // Updated date to match your current data
            ],
            [
                'name' => 'Admin',
                'username' => 'admin', // Added username
                'email' => 'admin@ptmkm.co.id',
                'email_verified_at' => null,
                'password' => Hash::make('Password.1'), // Example hash for demonstration
                'remember_token' => null,
                'role' => 'Super Admin',
                'last_login' => '2023-08-15 11:38:49',
                'login_counter' => 1,
                'is_active' => '1',
                'created_at' => '2023-07-08 05:42:25',
                'updated_at' => '2023-08-15 11:38:49',
            ],
            [
                'name' => 'User',
                'username' => 'user', // Added username
                'email' => 'user@ptmkm.co.id',
                'email_verified_at' => null,
                'password' => Hash::make('Password.1'), // Example hash for demonstration
                'remember_token' => null,
                'role' => 'User',
                'last_login' => '2023-08-15 11:38:49',
                'login_counter' => 1,
                'is_active' => '1',
                'created_at' => '2023-07-08 05:42:25',
                'updated_at' => '2023-08-15 11:38:49',
            ]
        ]);
    }
}
