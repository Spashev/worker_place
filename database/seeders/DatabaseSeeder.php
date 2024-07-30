<?php

namespace Database\Seeders;

use Bloomex\Common\Blca\Database\seeders\MainDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MainDatabaseSeeder::class,
        ]);
    }
}
