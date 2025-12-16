<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web'; // gunakan guard web

        // Buat role Admin & Gudang jika belum ada
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => $guard,
        ]);

        $gudangRole = Role::firstOrCreate([
            'name' => 'Gudang',
            'guard_name' => $guard,
        ]);

        // Buat akun Admin contoh
        $admin = User::firstOrCreate([
            'email' => 'admin@inventaris.com'
        ], [
            'name' => 'Admin Inventaris',
            'password' => bcrypt('admin123')
        ]);

        if (!$admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
        }

        // Buat akun Gudang contoh
        $gudang = User::firstOrCreate([
            'email' => 'gudang@inventaris.com'
        ], [
            'name' => 'Petugas Gudang',
            'password' => bcrypt('gudang123')
        ]);

        if (!$gudang->hasRole('Gudang')) {
            $gudang->assignRole($gudangRole);
        }
    }
}
