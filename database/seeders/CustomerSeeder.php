<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customer')->insert([
            'id' => 1,
            'no_telp' => '087832412825',
            'email'=>'bisma@gmail.com',
            'alamat' => 'Bendan Duwur',
            'nama' => 'Yulius Bisma',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
