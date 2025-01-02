<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'ADMIN',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(), // Sets the current timestamp
        ]);
        User::create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'role' => 'USER',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'customer',
            'email' => 'customer@gmail.com',
            'role' => 'CUSTOMER',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}