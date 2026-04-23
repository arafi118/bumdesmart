<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = tenant();

        $business = \App\Models\Business::create([
            'owner_id' => $owner->id,
            'nama_usaha' => $owner->nama_usaha,
            'alamat' => 'Alamat Usaha',
            'no_telp' => '08xxxxxxxx',
            'email' => 'business@example.com',
        ]);

        $this->call([
            AccountSeeder::class,
            ArusKasSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            AssignMenusToRolesSeeder::class,
        ]);

        // Ensure users are linked to the business
        \App\Models\User::query()->update(['business_id' => $business->id]);
    }
}
