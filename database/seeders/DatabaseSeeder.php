<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create([
            "name"=> "superadmin",
        ]);

        User::create([
            'name' => 'Web Support',
            'email' => 'dev.admin@antiquespride.edu.ph',
            'password' => bcrypt('Shinra23'),
        ]);

        $user = User::where('id', 1)->first();

        $user->assignRole('superadmin');
    }
}
