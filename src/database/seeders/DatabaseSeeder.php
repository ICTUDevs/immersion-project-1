<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Web Support',
            'email' => 'support.web@antiquespride.edu.ph',
            'password' => bcrypt('Shinra23'),
        ]);

        $user = User::where('id', 1)->first();

        $user->assignRole('superadmin');
    }
}
