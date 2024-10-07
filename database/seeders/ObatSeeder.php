<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obats = [
            [
                'kode_obat' => 'OBT001',
                'merk' => 'Paracetamol',
                'nama' => 'Panadol',
                'jenis' => 'Tablet',
                'kegunaan' => 'Pereda nyeri dan penurun demam',
                'stok' => 100.00,
                'harga' => 10000,
                'satuan' => 'Strip',
            ],
            [
                'kode_obat' => 'OBT002',
                'merk' => 'Amoxicillin',
                'nama' => 'Amoxil',
                'jenis' => 'Kapsul',
                'kegunaan' => 'Antibiotik',
                'stok' => 50.00,
                'harga' => 25000,
                'satuan' => 'Botol',
            ],
            [
                'kode_obat' => 'OBT003',
                'merk' => 'Ibuprofen',
                'nama' => 'Proris',
                'jenis' => 'Sirup',
                'kegunaan' => 'Pereda nyeri dan anti-inflamasi',
                'stok' => 30.00,
                'harga' => 35000,
                'satuan' => 'Botol',
            ],
            [
                'kode_obat' => 'OBT004',
                'merk' => 'Omeprazole',
                'nama' => 'Promag',
                'jenis' => 'Tablet',
                'kegunaan' => 'Obat maag dan asam lambung',
                'stok' => 75.00,
                'harga' => 15000,
                'satuan' => 'Strip',
            ],
            [
                'kode_obat' => 'OBT005',
                'merk' => 'Cetirizine',
                'nama' => 'Zyrtec',
                'jenis' => 'Tablet',
                'kegunaan' => 'Antihistamin',
                'stok' => 60.00,
                'harga' => 20000,
                'satuan' => 'Strip',
            ],
        ];

        foreach ($obats as $obat) {
            DB::table('obat')->insert(array_merge($obat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
