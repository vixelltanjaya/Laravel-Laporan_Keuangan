<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees')->insert([
            'id' => 1,
            'username' => 'bisma',
            'email' => 'bisma@gmail.com',
            'role'=> 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
