<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengaduan;
use Illuminate\Database\Seeder;

class PengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test users
        $userRole = \App\Models\Role::where('nama', 'user')->first();
        
        if (!$userRole) {
            return; // Exit if roles not seeded
        }

        // Get or create test users
        $user1 = User::firstOrCreate(
            ['email' => 'user1@test.com'],
            [
                'username' => 'user.test1',
                'role_id' => $userRole->id,
                'password' => bcrypt('password'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'user2@test.com'],
            [
                'username' => 'user.test2',
                'role_id' => $userRole->id,
                'password' => bcrypt('password'),
            ]
        );

        // Create test pengaduan data for user 1
        Pengaduan::create([
            'user_id' => $user1->id,
            'judul' => 'Projector ruang 101 tidak menyala',
            'deskripsi' => 'Projector di ruang 101 tidak menyala ketika digunakan. Sudah dicoba dihubungkan ke power dan laptop, tapi masih tidak merespons.',
            'lokasi' => 'Ruang 101, Lantai 1',
            'status' => 'Belum Ditindaklanjuti',
        ]);

        Pengaduan::create([
            'user_id' => $user1->id,
            'judul' => 'AC ruang 102 tidak dingin',
            'deskripsi' => 'AC di ruang 102 sudah dinyalakan sejak pagi namun ruangan masih panas. Kemungkinan ada kebocoran freon atau kompressor rusak.',
            'lokasi' => 'Ruang 102, Lantai 1',
            'status' => 'Sedang Diproses',
        ]);

        Pengaduan::create([
            'user_id' => $user1->id,
            'judul' => 'Meja rusak - laci tidak bisa ditutup',
            'deskripsi' => 'Meja di ruang 103 sudah rusak, laci tidak bisa ditutup dengan sempurna. Tidak mempengaruhi fungsi tapi kurang rapi.',
            'lokasi' => 'Ruang 103, Lantai 1',
            'status' => 'Selesai',
        ]);

        // Create test pengaduan data for user 2
        Pengaduan::create([
            'user_id' => $user2->id,
            'judul' => 'Kabel LAN putus',
            'deskripsi' => 'Kabel LAN di lab komputer terputus/tergigit tikus. Perlu diganti dengan kabel baru.',
            'lokasi' => 'Lab Komputer, Lantai 2',
            'status' => 'Belum Ditindaklanjuti',
        ]);

        Pengaduan::create([
            'user_id' => $user2->id,
            'judul' => 'Papan tulis pecah',
            'deskripsi' => 'Papan tulis di kelas 5 pecah di bagian kanan. Sudah berbahaya untuk digunakan. Perlu diganti atau diperbaiki.',
            'lokasi' => 'Kelas 5, Lantai 2',
            'status' => 'Ditutup',
        ]);
    }
}
