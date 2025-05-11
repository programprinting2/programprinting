<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\MasterKontak;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $tipeList = ['staff', 'customer', 'supplier'];

        foreach (range(1, 20) as $i) {
            MasterKontak::create([
                'tipe' => $tipeList[array_rand($tipeList)],
                'nama' => $faker->name,
                'HP' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'catatan' => $faker->sentence,
            ]);
        }
    }
}
