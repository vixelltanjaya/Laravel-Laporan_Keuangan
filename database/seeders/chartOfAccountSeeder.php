<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
        DB::table('chart_of_account')->insert([
            'id' => 1,
            'account_id' => '1-100',
            'account_name' => 'Kas Kecil',
            'account_sign' => 'Debit',
            'account_type' => 'Aset',
            'account_group' => 'Kas',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
