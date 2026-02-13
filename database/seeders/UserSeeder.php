<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('nama', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'username' => 'Admin Sarpras',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
            ]
        );
    }
}
