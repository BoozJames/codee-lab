<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\Items::factory(20)->create();


        \App\Models\User::factory()->create([
            'name' => env('SYS_USERNAME'),
            'email' => env('SYS_EMAIL'),
            'password' => env('SYS_PASSWORD'),
            'role' => env('SYS_ROLE'),
        ]);
    }
}
