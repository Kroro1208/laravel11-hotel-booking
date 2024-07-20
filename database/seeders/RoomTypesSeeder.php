<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            ['name' => '洋室のFamily', 'description' => '洋室のファミリールーム'],
            ['name' => '洋室のSingle', 'description' => '洋室のシングルルーム'],
            ['name' => '洋室のDouble', 'description' => '洋室のダブルルーム'],
            ['name' => '和室のFamily', 'description' => '和室のファミリールーム'],
            ['name' => '和室のSingle', 'description' => '和室のシングルルーム'],
            ['name' => '和室のDouble', 'description' => '和室のダブルルーム'],
        ];

        DB::table('room_types')->insert($roomTypes);
    }
}
