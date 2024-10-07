<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use Faker\Factory as Faker;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 50; $i++) {
            Pasien::create([
                'nik' => $faker->unique()->numerify('################'),
                'nama' => $faker->name,
                'tanggal_lahir' => $faker->date('Y-m-d', '-18 years'),
                'alamat' => $faker->address,
                'no_hp' => $faker->phoneNumber,
            ]);
        }
    }
}