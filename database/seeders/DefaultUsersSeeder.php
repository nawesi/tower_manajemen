<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'it-admin'],
            [
                'name' => 'IT Admin',
                'email' => 'it-admin@example.com',
                'password' => Hash::make('H4r17a-0b!'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'vendor'],
            [
                'name' => 'Vendor',
                'email' => 'vendor@example.com',
                'password' => Hash::make('Vend0r-0b!'),
                'role' => 'vendor',
            ]
        );
    }
}
