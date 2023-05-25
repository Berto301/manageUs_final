<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\Role::factory(10)->create();

        // \App\Models\Role::factory()->create([
        //     'name' => 'founder',
        //     'description' => 'CEO - Founder',
        // ]);
        \App\Models\Role::factory()->create([
            'name' => 'editor',
            'description' => 'Editor',
        ]);
        \App\Models\Role::factory()->create([
            'name' => 'admin',
            'description' => 'Admnistrator',
        ]);
    }
}
