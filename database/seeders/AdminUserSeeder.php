<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminExists = User::where('email', 'admin@ktmb.com.my')->first();
        
        if (!$adminExists) {
            User::create([
                'name' => 'System Administrator',
                'email' => 'admin@ktmb.com.my',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'vendor_id' => null,
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@ktmb.com.my');
            $this->command->info('Password: admin123');
        } else {
            $this->command->info('Admin user already exists. No action taken.');
        }
    }
}