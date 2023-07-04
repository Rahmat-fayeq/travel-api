<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
        Role::create(['name' => 'editor', 'created_at' => now(), 'updated_at' => now()]);
    }
}
