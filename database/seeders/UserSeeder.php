<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;                 // Import Model User
use Illuminate\Support\Facades\Hash; // Import Hash untuk password

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun ADMIN UTAMA (Login pakai username: admin)
        User::create([
            'nik'      => '11111',            // NIK Admin
            'name'     => 'Admin Utama',
            'username' => 'admin',            // Username untuk login
            'email'    => 'admin@sigap.com',
            'password' => Hash::make('password'),
            'telp'     => '08123456789',       // Nomor Telepon
            'role'     => 'admin',
        ]);

        // 2. Akun WARGA CONTOH (Login pakai username: warga)
        User::create([
            'nik'      => '32010001',
            'name'     => 'Warga Test',
            'username' => 'warga',             // Username warga
            'email'    => 'warga@sigap.com',
            'password' => Hash::make('password'),
            'telp'     => '08987654321',
            'role'     => 'masyarakat',
        ]);
    }
}
