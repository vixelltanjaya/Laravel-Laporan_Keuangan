<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_transaction')->insert([
            'id' => 1,
            'code' => -99,
            'description' => 'Penerimaan Pelunasan Pariwisata',
            'evidence_id' => 2,
            'business_type_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
