<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulSeeder extends Seeder
{
    public function run(): void
    {
        // Modul untuk Project 1 (LNG-001)
        DB::table('moduls')->insert([
            ['project_id' => 1, 'nama_modul' => 'Modul 1 - Hull Construction', 'deskripsi' => 'Konstruksi lambung kapal', 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => 1, 'nama_modul' => 'Modul 2 - Piping System', 'deskripsi' => 'Sistem perpipaan', 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => 1, 'nama_modul' => 'Modul 3 - Electrical', 'deskripsi' => 'Sistem kelistrikan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
