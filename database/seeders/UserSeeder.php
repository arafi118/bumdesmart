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
        $roles = [
            [
                'business_id' => 1,
                'nama_role' => 'owner',
                'deskripsi' => 'Role owner',
            ],
            [
                'business_id' => 1,
                'nama_role' => 'admin',
                'deskripsi' => 'Role admin',
            ],
        ];

        Role::insert($roles);

        $users = [
            [
                'business_id' => 1,
                'role_id' => 1,
                'nama_lengkap' => 'Owner',
                'initial' => 'Owner',
                'no_hp' => '08123456789',
                'username' => 'owner',
                'password' => Hash::make('password'),
            ],
            [
                'business_id' => 1,
                'role_id' => 2,
                'nama_lengkap' => 'Admin',
                'initial' => 'Admin',
                'no_hp' => '08123456789',
                'username' => 'admin',
                'password' => Hash::make('password'),
            ],
        ];

        User::insert($users);
    }
}
