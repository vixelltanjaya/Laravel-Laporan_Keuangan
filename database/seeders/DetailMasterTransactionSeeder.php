<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailMasterTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_master_transaction')->insert([
            'id' => 1,
            'master_code' => -99,
            'gl_account' => '1-100',
            'account_position' => 'debit',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
