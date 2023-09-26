<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\SuperUser::create([
            'name' => 'Anas',
            'email' => 'anas@leadvy.com',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
        ]);

        \App\Models\SuperUser::create([
            'name' => 'Ahmed Abdelsalam',
            'email' => 'a.abdelsalam@leadvy.com',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
        ]);

        $tenant1 = \App\Models\Tenant::create();
        $tenant1->domains()->create(['domain' => 'ahmed.backend.macroify.com']);
    }
}
