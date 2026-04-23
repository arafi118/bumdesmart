<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        User::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $businessId = \App\Models\Business::first()->id ?? 1;

        $roles = [
            [
                'business_id' => $businessId,
                'nama_role' => 'owner',
                'deskripsi' => 'Role owner',
            ],
            [
                'business_id' => $businessId,
                'nama_role' => 'admin',
                'deskripsi' => 'Role admin',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $users = [
            [
                'business_id' => $businessId,
                'role_id' => 1,
                'is_master' => false, // Only central_users are system masters
                'nama_lengkap' => 'Tenant Owner',
                'initial' => 'OWN',
                'no_hp' => '08123456789',
                'username' => 'owner',
                'password' => Hash::make('password'),
            ],
            [
                'business_id' => $businessId,
                'role_id' => 2,
                'nama_lengkap' => 'Admin Toko',
                'initial' => 'ADM',
                'no_hp' => '08123456780',
                'username' => 'admin',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
