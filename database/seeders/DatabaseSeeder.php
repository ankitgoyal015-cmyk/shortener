<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \DB::table('roles')->insert([
            ['name' => 'SuperAdmin'],
            ['name' => 'Admin'],
            ['name' => 'Member'],
            ['name' => 'Sales'],
            ['name' => 'Manager'],
        ]);

        // raw SQL insert SuperAdmin user (password hashed)
        \DB::statement("
            INSERT INTO users (name, email, password, role_id, created_at, updated_at)
            VALUES ('Super Admin', 'superadmin@example.com', '".\Illuminate\Support\Facades\Hash::make('password123')."',
            (SELECT id FROM roles WHERE name = 'SuperAdmin'), NOW(), NOW())
        ");
    }

}
