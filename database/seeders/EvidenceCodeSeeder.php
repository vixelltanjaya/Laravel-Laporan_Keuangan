<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvidenceCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('evidence_code')->insert([
            'id' => 1,
            'prefix_code' => 'BKK',
            'code_title' => 'Bukti Kas Keluar',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
