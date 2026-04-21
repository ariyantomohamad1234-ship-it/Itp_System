<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItpSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('itps')->insert([
            // Assembly B1-A (sub_blok_id = 1)
            [
                'sub_blok_id' => 1, 'assembly_code' => 'LNG-H1-B1A-MD3500',
                'code' => '01.1', 'item' => 'Material Identification Check',
                'yard_val' => 'W', 'class_val' => 'RV', 'os_val' => 'RV', 'stat_val' => '-',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 1, 'assembly_code' => 'LNG-H1-B1A-MD3500',
                'code' => '01.2', 'item' => 'Marking & Cutting Check',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => '-', 'stat_val' => '-',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 1, 'assembly_code' => 'LNG-H1-B1A-MD3500',
                'code' => '01.3', 'item' => 'Fit-up Inspection',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => 'W', 'stat_val' => 'RV',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 1, 'assembly_code' => 'LNG-H1-B1A-MD3500',
                'code' => '01.4', 'item' => 'Welding Visual Inspection',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => 'RV', 'stat_val' => 'NA',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 1, 'assembly_code' => 'LNG-H1-B1A-SS-P',
                'code' => '02.1', 'item' => 'Sub Assembly Dimension Check',
                'yard_val' => 'W', 'class_val' => 'RV', 'os_val' => '-', 'stat_val' => '-',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 1, 'assembly_code' => 'LNG-H1-B1A-SS-P',
                'code' => '02.2', 'item' => 'NDT (Non-Destructive Test)',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => 'W', 'stat_val' => 'W',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // Assembly B1-B (sub_blok_id = 2)
            [
                'sub_blok_id' => 2, 'assembly_code' => 'LNG-H1-B1B-FC',
                'code' => '03.1', 'item' => 'Frame Check',
                'yard_val' => 'W', 'class_val' => 'RV', 'os_val' => 'RV', 'stat_val' => '-',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 2, 'assembly_code' => 'LNG-H1-B1B-FC',
                'code' => '03.2', 'item' => 'Panel Alignment Check',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => '-', 'stat_val' => 'NA',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // Assembly B3-Piping (sub_blok_id = 4)
            [
                'sub_blok_id' => 4, 'assembly_code' => 'LNG-P2-B3-PIPE01',
                'code' => '04.1', 'item' => 'Pipe Material Verification',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => 'RV', 'stat_val' => 'RV',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 4, 'assembly_code' => 'LNG-P2-B3-PIPE01',
                'code' => '04.2', 'item' => 'Pressure Test',
                'yard_val' => 'W', 'class_val' => 'W', 'os_val' => 'W', 'stat_val' => 'W',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sub_blok_id' => 4, 'assembly_code' => 'LNG-P2-B3-PIPE01',
                'code' => '04.3', 'item' => 'Leak Test',
                'yard_val' => 'W', 'class_val' => 'RV', 'os_val' => '-', 'stat_val' => 'NA',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}