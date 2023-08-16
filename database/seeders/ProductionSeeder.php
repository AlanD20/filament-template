<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Enums\UserPermission;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserPermission::values() as $permission) {
            Permission::withoutEvents(fn () => Permission::create(['name' => $permission]));
        }

        // Uncomment below if you have default settings

        // $settings = [
        //     DefaultSettings::MY_KEY->value => 0,
        // ];
        // foreach ($settings as $key => $value) {
        //     Settings::factory()->createOneQuietly([
        //         'key' => $key,
        //         'value' => $value,
        //     ]);
        // }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
