<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ObatMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obatMasuks = [
            [
                'obat_id' => 1,
                'nomor_batch' => 'BATCH001',
                'jumlah' => 50.00,
                'harga_beli' => 8000,
                'tanggal_kadaluarsa' => Carbon::now()->addYears(2),
            ],
            [
                'obat_id' => 2,
                'nomor_batch' => 'BATCH002',
                'jumlah' => 30.00,
                'harga_beli' => 20000,
                'tanggal_kadaluarsa' => Carbon::now()->addYears(3),
            ],
            [
                'obat_id' => 3,
                'nomor_batch' => 'BATCH003',
                'jumlah' => 20.00,
                'harga_beli' => 30000,
                'tanggal_kadaluarsa' => Carbon::now()->addYears(2),
            ],
            [
                'obat_id' => 4,
                'nomor_batch' => 'BATCH004',
                'jumlah' => 40.00,
                'harga_beli' => 12000,
                'tanggal_kadaluarsa' => Carbon::now()->addYears(3),
            ],
            [
                'obat_id' => 5,
                'nomor_batch' => 'BATCH005',
                'jumlah' => 35.00,
                'harga_beli' => 18000,
                'tanggal_kadaluarsa' => Carbon::now()->addYears(2),
            ],
        ];

        foreach ($obatMasuks as $obatMasuk) {
            DB::table('obat_masuk')->insert(array_merge($obatMasuk, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
