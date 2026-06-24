<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_pengeluaran')->insert([

            [

                'nama_jenis' => 'Makanan',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ],

            [

                'nama_jenis' => 'Minuman',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ],

            [

                'nama_jenis' => 'Administrasi Lab',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ],

            [

                'nama_jenis' => 'Alat/Bahan Laboratorium',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ],

            [

                'nama_jenis' => 'Kebutuhan Pantry',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ],

            [

                'nama_jenis' => 'Pemeliharaan Inventaris',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ],

            [

                'nama_jenis' => 'Pengeluaran Lainnya',

                'isAktif' => true,

                'created_at' => now(),

                'updated_at' => now(),

            ]

        ]);
    }
}
