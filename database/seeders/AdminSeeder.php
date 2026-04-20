<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Service Staff',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'role' => 'service_staff',
        ]);

        User::create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);
    }
}
