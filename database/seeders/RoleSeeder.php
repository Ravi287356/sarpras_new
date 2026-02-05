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
        Role::firstOrCreate(['nama' => 'admin']);
        Role::firstOrCreate(['nama' => 'operator']);
        Role::firstOrCreate(['nama' => 'user']);
    }
}
