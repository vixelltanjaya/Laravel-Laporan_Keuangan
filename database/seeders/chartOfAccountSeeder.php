<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class chartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coa')->insert([
            'id' => 1,
            'account_id' => '1-100',
            'account_name' => 'Kas',
            'account_group'=> 'Aset',
            'account_detail'=> 'Kas Kecil',
            'account_subgroup'=> '',
            'account_status'=> 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
