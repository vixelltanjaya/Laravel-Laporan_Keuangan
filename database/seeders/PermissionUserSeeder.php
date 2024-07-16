<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions_user')->insert([
            'id' => 1,
            'permission_id' => 1,
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
    }
}
