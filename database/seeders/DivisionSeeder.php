<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('division')->insert([
            [
                'id' => 1,
                'description' => 'pariwisata',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'description' => 'harian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'description' => 'pembelian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'description' => 'penjualan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'description' => 'keuangan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 99,
                'description' => 'lini bisnis lainnya',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
