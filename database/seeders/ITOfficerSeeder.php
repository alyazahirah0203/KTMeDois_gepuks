<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Officer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ITOfficerSeeder extends Seeder
{
    public function run()
    {
        // Check if IT Officer already exists
        $existing = User::where('email', 'it@ktmb.gov.my')->first();
        
        if (!$existing) {
            $user = User::create([
                'name' => 'IT Officer',
                'email' => 'it@ktmb.gov.my',
                'password' => Hash::make('password123'),
                'role' => 'it_officer',
                'vendor_id' => null,
            ]);

            Officer::create([
                'user_id' => $user->id,
                'staff_name' => 'IT Officer',
                'department' => 'Information Technology Department',
                'position' => 'IT Officer'
            ]);

            $this->command->info('IT Officer created successfully!');
            $this->command->info('Email: it@ktmb.gov.my');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('IT Officer already exists.');
        }
    }
}