<?php

namespace Database\Seeders;

use App\Models\Settings;
use App\Models\Permission;
use App\Enums\UserPermission;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (UserPermission::values() as $permission) {
            Permission::withoutEvents(fn () => Permission::create(['name' => $permission]));
        }

        // Uncomment if you have default settings

        // $settings = [];
        // foreach ($settings as $key => $value) {
        //     Settings::factory()->createOneQuietly([
        //         'key' => $key,
        //         'value' => $value,
        //     ]);
        // }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
