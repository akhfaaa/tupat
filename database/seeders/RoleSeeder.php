<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role Dasar
        $roles = ['super-admin', 'tu', 'guru', 'wali-kelas', 'siswa'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // 2. Buat Akun Super Admin
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@smk.com',
            'password' => Hash::make('password'),
        ]);

        // 3. Berikan role super-admin ke akun tersebut
        $admin->assignRole('super-admin');
    }
}
