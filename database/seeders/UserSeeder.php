<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nama' => 'Admin Lab',
                'email' => 'admin@labmoneylens.com',
                'password' => Hash::make('admin123'),
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kepala Lab',
                'email' => 'kepalalab@labmoneylens.com',
                'password' => Hash::make('kepalalab123'),
                'role' => 'Kepala Lab',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}