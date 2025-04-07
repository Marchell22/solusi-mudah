<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari role Administrator
        $adminRole = Role::where('name', 'Administrator')->first();

        if (!$adminRole) {
            // Buat role Administrator jika belum ada
            $adminRole = Role::create([
                'name' => 'Administrator',
                'description' => 'Administrator dengan akses penuh'
            ]);
        }

        // Buat user admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Assign role Administrator ke user admin
        $admin->roles()->attach($adminRole);
    }
}