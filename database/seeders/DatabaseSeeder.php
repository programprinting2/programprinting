<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\MesinSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\ParametersSeeder;
use Database\Seeders\DetailParametersSeeder;
use Database\Seeders\KaryawanSeeder;
use Database\Seeders\BahanBakuSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MesinSeeder::class,
            ContactSeeder::class,
            ParametersSeeder::class,
            DetailParametersSeeder::class,
            KaryawanSeeder::class,
            BahanBakuSeeder::class
        ]);
    }
}
