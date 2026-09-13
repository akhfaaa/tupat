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
            'guru-bk',
            'hubin',
            'mentor-industri',
            'bkk',
            'orang-tua',
            'alumni'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Siapkan password akun demo
        $defaultPassword = Hash::make('11111111');

        // 3. Daftar akun yang akan dibuat
        $users = [
            ['name' => 'Super Administrator', 'email' => 'admin@tupat.com', 'role' => 'super-admin'],
            ['name' => 'Staff Tata Usaha', 'email' => 'tu@tupat.com', 'role' => 'tu'],
            ['name' => 'Bapak/Ibu Guru', 'email' => 'guru@tupat.com', 'role' => 'guru'],
            ['name' => 'Siswa Teladan', 'email' => 'siswa@tupat.com', 'role' => 'siswa'],
            ['name' => 'Kepala Sekolah', 'email' => 'kepsek@tupat.com', 'role' => 'kepala-sekolah'],
            ['name' => 'Wali Kelas', 'email' => 'walikelas@tupat.com', 'role' => 'wali-kelas'],
            ['name' => 'Guru BK', 'email' => 'bk@tupat.com', 'role' => 'guru-bk'],
            ['name' => 'Koordinator Hubin', 'email' => 'hubin@tupat.com', 'role' => 'hubin'],
            ['name' => 'Mentor Industri', 'email' => 'mentor@tupat.com', 'role' => 'mentor-industri'],
            ['name' => 'Koordinator BKK', 'email' => 'bkk@tupat.com', 'role' => 'bkk'],
            ['name' => 'Wali Murid', 'email' => 'orangtua@tupat.com', 'role' => 'orang-tua'],
            ['name' => 'Alumni', 'email' => 'alumni@tupat.com', 'role' => 'alumni'],
        ];

        // 4. Eksekusi pembuatan user dan penugasan role
        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword
                ]
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}
