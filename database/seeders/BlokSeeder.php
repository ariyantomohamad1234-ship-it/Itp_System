<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlokSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bloks')->insert([
            // Modul 1 - Hull Construction
            ['modul_id' => 1, 'nama_blok' => 'BLOK 1', 'created_at' => now(), 'updated_at' => now()],
            ['modul_id' => 1, 'nama_blok' => 'BLOK 2', 'created_at' => now(), 'updated_at' => now()],
            // Modul 2 - Piping
            ['modul_id' => 2, 'nama_blok' => 'BLOK 3', 'created_at' => now(), 'updated_at' => now()],
            ['modul_id' => 2, 'nama_blok' => 'BLOK 4', 'created_at' => now(), 'updated_at' => now()],
            // Modul 3 - Electrical
            ['modul_id' => 3, 'nama_blok' => 'BLOK 5', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
