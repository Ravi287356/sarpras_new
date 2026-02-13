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
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // Create Kategori Sarpras if not exists
        if (KategoriSarpras::count() === 0) {
            KategoriSarpras::create([
                'nama' => 'Alat Tulis',
                'deskripsi' => 'Peralatan tulis kantor',
            ]);

            KategoriSarpras::create([
                'nama' => 'Elektronik',
                'deskripsi' => 'Peralatan elektronik',
            ]);

            KategoriSarpras::create([
                'nama' => 'Furniture',
                'deskripsi' => 'Peralatan furniture',
            ]);
        }

        // Create Lokasi if not exists
        if (Lokasi::count() === 0) {
            Lokasi::create(['nama' => 'Ruang 101']);
            Lokasi::create(['nama' => 'Ruang 102']);
            Lokasi::create(['nama' => 'Lab Komputer']);
            Lokasi::create(['nama' => 'Perpustakaan']);
        }

        $this->command->info('Database seeded successfully!');
    }
}
