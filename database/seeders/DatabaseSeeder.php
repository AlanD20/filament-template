<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
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

        // Clear activity log after seed
        DB::raw('DELETE FROM activity_log');
    }
}
