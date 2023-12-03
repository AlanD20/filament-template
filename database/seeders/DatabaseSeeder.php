<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            ProductionSeeder::class,
        ];

        if (! app()->environment('production')) {
            $seeders = \array_merge($seeders, [
                LocalSeeder::class,
            ]);
        }

        $this->call(
            array_merge($seeders, [SuperAdminSeeder::class])
        );

        if (! app()->environment('testing')) {
            // Clear activity log after seed
            Artisan::call('activitylog:clean');
        }
    }
}
