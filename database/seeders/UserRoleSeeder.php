<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Daftarkan semua role SIAKAD
        $roles = [
            'super-admin',
            'tu',
            'guru',
            'siswa',
            'kepala-sekolah',
            'wali-kelas',
            'orang-tua'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Siapkan password default
        $defaultPassword = Hash::make('password123');

        // 3. Daftar akun yang akan dibuat
        $users = [
            ['name' => 'Super Administrator', 'email' => 'admin@tupat.com', 'role' => 'super-admin'],
            ['name' => 'Staff Tata Usaha', 'email' => 'tu@tupat.com', 'role' => 'tu'],
            ['name' => 'Bapak/Ibu Guru', 'email' => 'guru@tupat.com', 'role' => 'guru'],
            ['name' => 'Siswa Teladan', 'email' => 'siswa@tupat.com', 'role' => 'siswa'],
            ['name' => 'Kepala Sekolah', 'email' => 'kepsek@tupat.com', 'role' => 'kepala-sekolah'],
            ['name' => 'Wali Kelas', 'email' => 'walikelas@tupat.com', 'role' => 'wali-kelas'],
            ['name' => 'Wali Murid', 'email' => 'orangtua@tupat.com', 'role' => 'orang-tua'],
        ];

        // 4. Eksekusi pembuatan user dan penugasan role
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword
                ]
            );

            // Assign role ke user tersebut menggunakan Spatie
            $user->assignRole($userData['role']);
        }
    }
}
