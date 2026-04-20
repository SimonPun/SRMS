<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['role' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['role' => 'service_staff'],
            [
                'name' => 'Service Staff',
                'email' => 'staff@example.com',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['role' => 'client'],
            [
                'name' => 'Client User',
                'email' => 'punjivan44@gmail.com',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
