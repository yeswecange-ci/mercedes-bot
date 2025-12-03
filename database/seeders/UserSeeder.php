<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@mercedes-bot.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Supervisor User
        User::create([
            'name' => 'Superviseur',
            'email' => 'supervisor@mercedes-bot.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        // Create Agent Users
        User::create([
            'name' => 'Agent 1',
            'email' => 'agent1@mercedes-bot.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
        ]);

        User::create([
            'name' => 'Agent 2',
            'email' => 'agent2@mercedes-bot.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
        ]);
    }
}
