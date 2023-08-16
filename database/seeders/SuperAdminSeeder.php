<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aland = \App\Models\User::factory()->createOneQuietly([
            'full_name' => env('P_USER', 'aland20'),
            'username' => env('P_USER', 'aland20'),
            'email' => env('P_EMAIL', 'aland20@pm.me'),
            'password' => Hash::make(env('P_PASSWORD', 'password')),
        ]);

        $aland->givePermissionTo(UserPermission::DEVELOPER->value);

        $admin = \App\Models\User::factory()->createOneQuietly([
            'full_name' => env('ADMIN_USER', 'admin'),
            'username' => env('ADMIN_USER', 'admin'),
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);

        $admin->givePermissionTo(UserPermission::DEVELOPER->value);
    }
}
