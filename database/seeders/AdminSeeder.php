<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@foodhub.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'restaurant_id' => null, 
        ]);
    }
}