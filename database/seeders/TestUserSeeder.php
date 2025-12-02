<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Musician;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the user
        $user = User::create([
            'email' => 'test@user.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(), // Auto-verify email
        ]);

        // Create the musician profile
        Musician::create([
            'user_id' => $user->id,
            'stage_name' => 'Test',
            'first_name' => 'test',
            'last_name' => 'test',
            'location' => 'balibago',
            'genre' => 'rock',
            'instrument' => 'guitar',
        ]);

        $this->command->info('Test user created successfully!');
        $this->command->info('Email: test@user.com');
        $this->command->info('Password: password');
    }
}
