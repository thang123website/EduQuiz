<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            QuizSeeder::class,
        ]);

        $adminRole = \App\Models\Role::where('name', 'admin')->first();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@eduquiz.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'role_name' => $adminRole->name,
        ]);
    }
}
