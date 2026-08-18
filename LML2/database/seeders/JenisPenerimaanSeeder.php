<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPenerimaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_penerimaan')->insert([
            [
                'nama_jenis' => 'Operasional Lab',
                'isAktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Penerimaan Lainnya',
                'isAktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
