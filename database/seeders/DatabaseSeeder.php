<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\KategoriSarpras;
use App\Models\Lokasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create([
            'id' => (string) Str::uuid(),
            'nama' => 'admin'
        ]);
        $operatorRole = Role::create([
            'id' => (string) Str::uuid(),
            'nama' => 'operator'
        ]);
        $userRole = Role::create([
            'id' => (string) Str::uuid(),
            'nama' => 'user'
        ]);

        // Create Users
        User::create([
            'id' => (string) Str::uuid(),
            'username' => 'admin',
            'email' => 'admin@sarpras.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'username' => 'operator',
            'email' => 'operator@sarpras.test',
            'password' => Hash::make('password'),
            'role_id' => $operatorRole->id,
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'username' => 'user',
            'email' => 'user@sarpras.test',
            'password' => Hash::make('password'),
            'role_id' => $userRole->id,
        ]);

        // Create Kategori Sarpras
        KategoriSarpras::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Alat Tulis',
            'deskripsi' => 'Peralatan tulis kantor',
        ]);

        KategoriSarpras::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Elektronik',
            'deskripsi' => 'Peralatan elektronik',
        ]);

        KategoriSarpras::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Furniture',
            'deskripsi' => 'Peralatan furniture',
        ]);

        // Create Lokasi
        Lokasi::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Ruang 101'
        ]);
        Lokasi::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Ruang 102'
        ]);
        Lokasi::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Lab Komputer'
        ]);
        Lokasi::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Perpustakaan'
        ]);

        $this->command->info('Database seeded successfully!');
    }
}
