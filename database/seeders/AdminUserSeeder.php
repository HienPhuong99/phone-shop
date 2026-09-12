<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@phoneshop.test'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );
    }
}
