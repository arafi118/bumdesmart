<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CentralUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CentralUser::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'password' => \Hash::make('password'),
            'is_master' => true,
        ]);
    }
}
