<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(5)->create();
        // \App\Models\Url::factory(10)->create();

        \App\Models\User::factory()->create([
            'id' => Str::random(36),
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt(2444),
        ]);

    }
}
