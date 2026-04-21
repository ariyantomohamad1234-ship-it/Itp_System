<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubBlokSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sub_bloks')->insert([
            // Blok 1
            ['blok_id' => 1, 'nama_sub_blok' => 'Assembly B1-A', 'created_at' => now(), 'updated_at' => now()],
            ['blok_id' => 1, 'nama_sub_blok' => 'Assembly B1-B', 'created_at' => now(), 'updated_at' => now()],
            // Blok 2
            ['blok_id' => 2, 'nama_sub_blok' => 'Assembly B2-A', 'created_at' => now(), 'updated_at' => now()],
            // Blok 3
            ['blok_id' => 3, 'nama_sub_blok' => 'Assembly B3-Piping', 'created_at' => now(), 'updated_at' => now()],
            // Blok 4
            ['blok_id' => 4, 'nama_sub_blok' => 'Assembly B4-Piping', 'created_at' => now(), 'updated_at' => now()],
            // Blok 5
            ['blok_id' => 5, 'nama_sub_blok' => 'Assembly B5-Elec', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}