<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@agrisupply.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@agrisupply.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'status' => 'active',
                'can_create' => true,
                'can_read' => true,
                'can_update' => true,
                'can_delete' => true,
                'can_stock_in' => true,
                'can_stock_out' => true,
                'avatar' => null,
            ]);
        }
    }
}