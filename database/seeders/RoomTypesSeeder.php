<?php

namespace Database\Seeders;

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
            [
                'name' => '洋室のFamily',
                'description' => '洋室のファミリールーム',
                'capacity' => 4,
                'number_of_rooms' => 10,
                'is_active' => true,
            ],
            [
                'name' => '洋室のSingle',
                'description' => '洋室のシングルルーム',
                'capacity' => 1,
                'number_of_rooms' => 30,
                'is_active' => true,
            ],
            [
                'name' => '洋室のDouble',
                'description' => '洋室のダブルルーム',
                'capacity' => 2,
                'number_of_rooms' => 20,
                'is_active' => true,
            ],
            [
                'name' => '和室のFamily',
                'description' => '和室のファミリールーム',
                'capacity' => 4,
                'number_of_rooms' => 8,
                'is_active' => true,
            ],
            [
                'name' => '和室のSingle',
                'description' => '和室のシングルルーム',
                'capacity' => 1,
                'number_of_rooms' => 15,
                'is_active' => true,
            ],
            [
                'name' => '和室のDouble',
                'description' => '和室のダブルルーム',
                'capacity' => 2,
                'number_of_rooms' => 12,
                'is_active' => true,
            ],
        ];

        DB::table('room_types')->insert($roomTypes);
    }
}
