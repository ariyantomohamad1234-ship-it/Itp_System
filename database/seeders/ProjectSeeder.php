<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('projects')->insert([
            'nama_project' => 'Mini LNG Vessel Hull 001',
            'kode_project' => 'LNG-001',
            'deskripsi' => 'Proyek pembangunan kapal Mini LNG pertama',
            'status' => 'active',
            'tanggal_kontrak' => '2026-03-01',
            'tanggal_mulai' => '2026-04-01',
            'deadline' => '2026-09-30',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
