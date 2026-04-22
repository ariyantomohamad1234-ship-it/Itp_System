<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MiniLngTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // ====================================================================
        // 1. CREATE TEMPLATE
        // ====================================================================
        $templateId = DB::table('project_templates')->insertGetId([
            'name'        => 'Mini LNG 36 TEU',
            'slug'        => 'mini-lng-36-teu',
            'description' => 'Template bawaan untuk Inspection and Test Plan kapal Mini LNG 36 TEU. Mencakup 6 modul, 10 blok, 46 sub-blok, dan seluruh kode inspeksi hull construction.',
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ====================================================================
        // 2. CREATE MODULES (6 modules)
        // ====================================================================
        $modulData = [
            ['nama' => 'LNG-M1', 'desc' => 'Modular 1'],
            ['nama' => 'LNG-M2', 'desc' => 'Modular 2'],
            ['nama' => 'LNG-M3', 'desc' => 'Modular 3'],
            ['nama' => 'LNG-M4', 'desc' => 'Modular 4'],
            ['nama' => 'LNG-M5', 'desc' => 'Modular 5'],
            ['nama' => 'LNG-M6', 'desc' => 'Modular 6'],
        ];

        $modulIds = []; // index 0-5 → new DB IDs
        foreach ($modulData as $i => $m) {
            $modulIds[$i] = DB::table('template_moduls')->insertGetId([
                'project_template_id' => $templateId,
                'nama_modul'          => $m['nama'],
                'deskripsi'           => $m['desc'],
                'sort_order'          => $i + 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // ====================================================================
        // 3. CREATE BLOCKS (10 blocks)
        // Block index → modul index: B1,B2→M1, B3,B4→M2, B5,B6→M3, B7,B8→M4, B9→M5, B10→M6
        // ====================================================================
        $blokData = [
            ['nama' => 'Block 1',  'modul' => 0],
            ['nama' => 'Block 2',  'modul' => 0],
            ['nama' => 'Block 3',  'modul' => 1],
            ['nama' => 'Block 4',  'modul' => 1],
            ['nama' => 'Block 5',  'modul' => 2],
            ['nama' => 'Block 6',  'modul' => 2],
            ['nama' => 'Block 7',  'modul' => 3],
            ['nama' => 'Block 8',  'modul' => 3],
            ['nama' => 'Block 9',  'modul' => 4],
            ['nama' => 'Block 10', 'modul' => 5],
        ];

        $blokIds = []; // index 0-9 → new DB IDs
        foreach ($blokData as $i => $b) {
            $blokIds[$i] = DB::table('template_bloks')->insertGetId([
                'template_modul_id' => $modulIds[$b['modul']],
                'nama_blok'         => $b['nama'],
                'sort_order'        => $i + 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // ====================================================================
        // 4. CREATE SUB-BLOCKS (46 sub-blocks)
        // old_subblok_id (1-46) → blok index (0-9)
        // ====================================================================
        $subBlokData = [
            // Block 1 (index 0)
            1  => ['blok' => 0, 'nama' => 'B1'],
            2  => ['blok' => 0, 'nama' => 'B1-MD'],
            3  => ['blok' => 0, 'nama' => 'Assembly B1-MD'],
            4  => ['blok' => 0, 'nama' => 'Join B1 & B1-MD'],
            // Block 2 (index 1)
            5  => ['blok' => 1, 'nama' => 'B2'],
            6  => ['blok' => 1, 'nama' => 'B2-MD'],
            7  => ['blok' => 1, 'nama' => 'Assembly B2-MD'],
            8  => ['blok' => 1, 'nama' => 'Join B2 & B2-MD'],
            9  => ['blok' => 1, 'nama' => 'Join B1 & B2'],
            // Block 3 (index 2)
            10 => ['blok' => 2, 'nama' => 'B3'],
            11 => ['blok' => 2, 'nama' => 'Assembly RLDK-P'],
            12 => ['blok' => 2, 'nama' => 'Assembly RLDK-S'],
            13 => ['blok' => 2, 'nama' => 'Join B3 & RLDK'],
            // Block 4 (index 3)
            14 => ['blok' => 3, 'nama' => 'B4'],
            15 => ['blok' => 3, 'nama' => 'Assembly RLDK-P'],
            16 => ['blok' => 3, 'nama' => 'Assembly RLDK-S'],
            17 => ['blok' => 3, 'nama' => 'Join B4 & RLDK'],
            18 => ['blok' => 3, 'nama' => 'Join B3 & B4'],
            // Block 5 (index 4)
            19 => ['blok' => 4, 'nama' => 'B5'],
            20 => ['blok' => 4, 'nama' => 'Assembly RLDK-P'],
            21 => ['blok' => 4, 'nama' => 'Assembly RLDK-S'],
            22 => ['blok' => 4, 'nama' => 'Join B5 & RLDK'],
            // Block 6 (index 5)
            23 => ['blok' => 5, 'nama' => 'B6'],
            24 => ['blok' => 5, 'nama' => 'Assembly RLDK-P'],
            25 => ['blok' => 5, 'nama' => 'Assembly RLDK-S'],
            26 => ['blok' => 5, 'nama' => 'Join B6 & RLDK'],
            27 => ['blok' => 5, 'nama' => 'Join B5 & B6'],
            // Block 7 (index 6)
            28 => ['blok' => 6, 'nama' => 'B7'],
            29 => ['blok' => 6, 'nama' => 'Assembly RLDK-P'],
            30 => ['blok' => 6, 'nama' => 'Assembly RLDK-S'],
            31 => ['blok' => 6, 'nama' => 'Join B7 & RLDK'],
            // Block 8 (index 7)
            32 => ['blok' => 7, 'nama' => 'B8'],
            33 => ['blok' => 7, 'nama' => 'Assembly RLDK-P'],
            34 => ['blok' => 7, 'nama' => 'Assembly RLDK-S'],
            35 => ['blok' => 7, 'nama' => 'Assembly Forecastle'],
            36 => ['blok' => 7, 'nama' => "Join B8, RLDK, F'Castle Deck"],
            37 => ['blok' => 7, 'nama' => 'Join B7 & B8'],
            // Block 9 (index 8)
            38 => ['blok' => 8, 'nama' => 'B9'],
            39 => ['blok' => 8, 'nama' => 'Assembly Forecastle'],
            40 => ['blok' => 8, 'nama' => "Join B9, RLDK, F'Castle Deck"],
            41 => ['blok' => 8, 'nama' => 'Join B9 (Modul 5)'],
            // Block 10 (index 9)
            42 => ['blok' => 9, 'nama' => 'B10-PD'],
            43 => ['blok' => 9, 'nama' => 'Assembly B10-PD'],
            44 => ['blok' => 9, 'nama' => 'B10-WH'],
            45 => ['blok' => 9, 'nama' => 'Assembly B10-WH'],
            46 => ['blok' => 9, 'nama' => 'Join B10-PD & B10-WH'],
        ];

        // Map old subblok ID → new template_sub_blok DB ID
        $subBlokIdMap = [];
        foreach ($subBlokData as $oldId => $s) {
            $subBlokIdMap[$oldId] = DB::table('template_sub_bloks')->insertGetId([
                'template_blok_id' => $blokIds[$s['blok']],
                'nama_sub_blok'    => $s['nama'],
                'sort_order'       => $oldId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // ====================================================================
        // 5. ASSEMBLY CODES grouped by old subblok ID
        //    [old_subblok_id => [[code, description], ...]]
        // ====================================================================
        $assemblyCodes = [
            1 => [
                ['LNG-M1-B1-BTM', 'Bottom Panel Block 1 (Portside)'],
                ['LNG-M1-B1-LBHD4200-P', 'Longitudinal Bulkhead 4200 of Center Line Panel Block 1 (Portside)'],
                ['LNG-M1-B1-LBHD4200-S', 'Longitudinal Bulkhead 4200 of Center Line Panel Block 1 (Starboard)'],
                ['LNG-M1-B1-MD4000', 'Main Deck Panel Block 1'],
                ['LNG-M1-B1-SS-P', 'Side Shell Panel Block 1 (Portside)'],
                ['LNG-M1-B1-SS-S', 'Side Shell Panel Block 1 (Starboard)'],
                ['LNG-M1-B1-TBHD3', 'Transverse Bulkhead FR.3 Panel Block 1'],
                ['LNG-M1-B1-TRSM', 'Transom Bulkhead Panel Block 1'],
            ],
            2 => [
                ['LNG-M1-B1-MD-LBHD2100-P', 'Longitudinal Bulkhead 2100 OCL Above Main Deck Panel Block 1 (Portside)'],
                ['LNG-M1-B1-MD-LBHD2100-S', 'Longitudinal Bulkhead 2100 OCL Above Main Deck Panel Block 1 (Starboard)'],
                ['LNG-M1-B1-MD-PD6300', 'Poop Deck Panel 6300 Above Base line Block 1'],
                ['LNG-M1-B1-MD-SS-P', 'Side Shell Above Main Deck Block 1 (Portside)'],
                ['LNG-M1-B1-MD-SS-S', 'Side Shell Above Main Deck Block 1 (Starboard)'],
                ['LNG-M1-B1-MD-TBHD3', 'Transverse Bulkhead FR.3 Above Main Deck Panel Block 1'],
                ['LNG-M1-B1-MD-TRSM', 'Transom Bulkhead Above Main Deck Panel Block 1'],
            ],
            3 => [['LNG-M1-B1-MD', 'Up to Upper Main Deck Sub-Block Block 1']],
            4 => [['LNG-M1-B1', 'Join Block 1']],
            5 => [
                ['LNG-M1-B2-BTM', 'Bottom Panel Block 2'],
                ['LNG-M1-B2-LBHD4200-P', 'Longitudinal Bulkhead 4200 of Center Line Panel Block 2 (Portside)'],
                ['LNG-M1-B2-LBHD4200-S', 'Longitudinal Bulkhead 4200 of Center Line Panel Block 2 (Starboard)'],
                ['LNG-M1-B2-MD4000', 'Main Deck Panel Block 2'],
                ['LNG-M1-B2-SS-P', 'Side Shell Panel Block 2 (Portside)'],
                ['LNG-M1-B2-SS-S', 'Side Shell Panel Block 2 (Starboard)'],
            ],
            6 => [
                ['LNG-M1-B2-MD-LBHD2100-P', 'Longitudinal Bulkhead 2100 OCL Above Main Deck Panel Block 2 (Portside)'],
                ['LNG-M1-B2-MD-LBHD2100-S', 'Longitudinal Bulkhead 2100 OCL Above Main Deck Panel Block 2 (Starboard)'],
                ['LNG-M1-B2-MD-PD6300', 'Poop Deck Panel 6300 Above Base line Block 2'],
                ['LNG-M1-B2-MD-SS-P', 'Side Shell Above Main Deck Block 2 (Portside)'],
                ['LNG-M1-B2-MD-SS-S', 'Side Shell Above Main Deck Block 2 (Starboard)'],
                ['LNG-M1-B2-MD-TBHD11', 'Transverse Bulkhead FR.11 Above Main Deck Panel Block 2'],
                ['LNG-M1-B2-MD-TBHD16-SLTD', 'Transverse Bulkhead FR.11 Slanted Above Main Deck Panel Block 2'],
                ['LNG-M1-B2-MD-TBHD8', 'Transverse Bulkhead FR.8 Above Main Deck Panel Block 2'],
                ['LNG-M1-B2-MD-TBHD9+300', 'Transverse Bulkhead FR.9+300 Above Main Deck Panel Block 2'],
            ],
            7 => [['LNG-M1-B2-MD', 'Up to Upper Main Deck Sub-Block Block 2']],
            8 => [['LNG-M1-B2', 'Assembly Block 2']],
            9 => [['LNG-M1', 'Assembly Modular M1']],
            10 => [
                ['LNG-M2-B3-BTM', 'Bottom Panel Block 3'],
                ['LNG-M2-B3-LBHD2100-P', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 3 (Portside)'],
                ['LNG-M2-B3-LBHD2100-S', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 3 (Starboard)'],
                ['LNG-M2-B3-MD3500', 'Main Deck Panel Block 3'],
                ['LNG-M2-B3-SS-P', 'Side Shell Panel Block 3 (Portside)'],
                ['LNG-M2-B3-SS-S', 'Side Shell Panel Block 3 (Starboard)'],
                ['LNG-M2-B3-TBHD18', 'Transverse Bulkhead FR.18 Panel Block 3'],
                ['LNG-M2-B3-TBHD23', 'Transverse Bulkhead FR.23 Panel Block 3'],
            ],
            11 => [['LNG-M2-B3-RLDK-P', 'Assembly Rail Deck Block 3 (Portside)']],
            12 => [['LNG-M2-B3-RLDK-S', 'Assembly Rail Deck Block 3 (Starboard)']],
            13 => [['LNG-M2-B3', 'Up to Main Deck Sub-Block B-3']],
            14 => [
                ['LNG-M2-B4-BTM', 'Bottom Panel Block 4'],
                ['LNG-M2-B4-LBHD2100-P', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 4 (Portside)'],
                ['LNG-M2-B4-LBHD2100-S', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 4 (Starboard)'],
                ['LNG-M2-B4-MD3500', 'Main Deck Panel Block 4'],
                ['LNG-M2-B4-SS-P', 'Side Shell Panel Block 4 (Portside)'],
                ['LNG-M2-B4-SS-S', 'Side Shell Panel Block 4 (Starboard)'],
                ['LNG-M2-B4-TBHD28', 'Transverse Bulkhead FR.28 Panel Block 4'],
            ],
            15 => [['LNG-M2-B4-RLDK-P', 'Assembly Rail Deck Block 4 (Portside)']],
            16 => [['LNG-M2-B4-RLDK-S', 'Assembly Rail Deck Block 4 (Starboard)']],
            17 => [['LNG-M2-B4', 'Up to Main Deck Sub-Block B-4']],
            18 => [['LNG-M2', 'Join Block 3 dan Block 4']],
            19 => [
                ['LNG-M3-B5-BTM', 'Bottom Panel Block 5'],
                ['LNG-M3-B5-LBHD2100-P', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 5 (Portside)'],
                ['LNG-M3-B5-LBHD2100-S', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 5 (Starboard)'],
                ['LNG-M3-B5-MD3500', 'Main Deck Panel Block 5'],
                ['LNG-M3-B5-SS-P', 'Side Shell Panel Block 5 (Portside)'],
                ['LNG-M3-B5-SS-S', 'Side Shell Panel Block 5 (Starboard)'],
                ['LNG-M3-B5-TBHD38', 'Transverse Bulkhead FR.38 Panel Block 5'],
            ],
            20 => [['LNG-M3-B5-RLDK-P', 'Assembly Rail Deck Block 5 (Portside)']],
            21 => [['LNG-M3-B5-RLDK-S', 'Assembly Rail Deck Block 5 (Starboard)']],
            22 => [['LNG-M3-B5', 'Up to Main Deck Sub-Block B-5']],
            23 => [
                ['LNG-M3-B6-BTM', 'Bottom Panel Block 6'],
                ['LNG-M3-B6-LBHD2100-P', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 6 (Portside)'],
                ['LNG-M3-B6-LBHD2100-S', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 6 (Starboard)'],
                ['LNG-M3-B6-MD3500', 'Main Deck Panel Block 6'],
                ['LNG-M3-B6-SS-P', 'Side Shell Panel Block 6 (Portside)'],
                ['LNG-M3-B6-SS-S', 'Side Shell Panel Block 6 (Starboard)'],
                ['LNG-M3-B6-TBHD48', 'Transverse Bulkhead FR.48 Panel Block 6'],
            ],
            24 => [['LNG-M3-B6-RLDK-P', 'Assembly Rail Deck Block 6 (Portside)']],
            25 => [['LNG-M3-B6-RLDK-S', 'Assembly Rail Deck Block 6 (Starboard)']],
            26 => [['LNG-M3-B6', 'Up to Main Deck Sub-Block B-6']],
            27 => [['LNG-M3', 'Join Block 5 dan Block 6']],
            28 => [
                ['LNG-M4-B7-BTM', 'Bottom Panel Block 7'],
                ['LNG-M4-B7-LBHD2100-P', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 7 (Portside)'],
                ['LNG-M4-B7-LBHD2100-S', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 7 (Starboard)'],
                ['LNG-M4-B7-MD3500', 'Main Deck Panel Block 7'],
                ['LNG-M4-B7-SS-P', 'Side Shell Panel Block 7 (Portside)'],
                ['LNG-M4-B7-SS-S', 'Side Shell Panel Block 7 (Starboard)'],
                ['LNG-M4-B7-TBHD58', 'Transverse Bulkhead FR.58 Panel Block 7'],
            ],
            29 => [['LNG-M4-B7-RLDK-P', 'Assembly Rail Deck Block 7 (Portside)']],
            30 => [['LNG-M4-B7-RLDK-S', 'Assembly Rail Deck Block 7 (Starboard)']],
            31 => [['LNG-M4-B7', 'Up to Main Deck Sub-Block B-7']],
            32 => [
                ['LNG-M4-B8-BTM', 'Bottom Panel Block 8'],
                ['LNG-M4-B8-LBHD2100-P', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 8 (Portside)'],
                ['LNG-M4-B8-LBHD2100-S', 'Longitudinal Bulkhead 2100 of Center Line Panel Block 8 (Starboard)'],
                ['LNG-M4-B8-MD3500', 'Main Deck Panel Block 8'],
                ['LNG-M4-B8-SS-P', 'Side Shell Panel Block 8 (Portside)'],
                ['LNG-M4-B8-SS-S', 'Side Shell Panel Block 8 (Starboard)'],
                ['LNG-M4-B8-TBHD68', 'Transverse Bulkhead FR.68 Panel Block 8'],
                ['LNG-M4-B8-TBHD76', 'Transverse Bulkhead FR.76 Panel Block 8'],
            ],
            33 => [['LNG-M4-B8-RLDK-P', 'Assembly Rail Deck Block 8 (Portside)']],
            34 => [['LNG-M4-B8-RLDK-S', 'Assembly Rail Deck Block 8 (Starboard)']],
            35 => [['LNG-M4-B8-FCL', 'Assembly Forecastle Deck Block 8']],
            36 => [['LNG-M4-B8', 'Up to Main Deck Sub-Block B-8']],
            37 => [['LNG-M4', 'Join Block 7 dan Block 8']],
            38 => [
                ['LNG-M5-B9-BTM', 'Bottom Panel Block 9'],
                ['LNG-M5-B9-LBHD4200-P', 'Longitudinal Bulkhead 4200 of Center Line Panel Block 9 (Portside)'],
                ['LNG-M5-B9-LBHD4200-S', 'Longitudinal Bulkhead 4200 of Center Line Panel Block 9 (Starboard)'],
                ['LNG-M5-B9-MD3500', 'Main Deck Panel Block 9'],
                ['LNG-M5-B9-SS-P', 'Side Shell Panel Block 9 (Portside)'],
                ['LNG-M5-B9-SS-S', 'Side Shell Panel Block 9 (Starboard)'],
            ],
            39 => [['LNG-M5-B9-FCL', 'Assembly Forecastle Deck Block 9']],
            40 => [['LNG-M5-B9', 'Up to Main Deck Sub-Block B-9']],
            41 => [['LNG-M5', 'Join Block 9 dan Forecastle Deck']],
            42 => [
                ['LNG-M6-B10-PD-BD8700', 'Bridge Deck Panel 8700 Above Base line Block 10'],
                ['LNG-M6-B10-PD-LBHD2100-P', 'Longitudinal Bulkhead 2100 OCL Upper Poop Deck Panel Block 10 (Portside)'],
                ['LNG-M6-B10-PD-LBHD2100-S', 'Longitudinal Bulkhead 2100 OCL Upper Poop Deck Panel Block 10 (Starboard)'],
                ['LNG-M6-B10-PD-SS-P', 'Side Shell Above Poop Deck Block 10 (Portside)'],
                ['LNG-M6-B10-PD-SS-S', 'Side Shell Above Poop Deck Block 10 (Starboard)'],
                ['LNG-M6-B10-PD-TBHD0', 'Transverse Bulkhead FR.0 Above Poop Deck Panel Block 10'],
                ['LNG-M6-B10-PD-TBHD11', 'Transverse Bulkhead FR.11 Above Poop Deck Panel Block 10'],
                ['LNG-M6-B10-PD-TBHD16-SLTD', 'Transverse Bulkhead FR.16 Slanted Above Poop Deck Panel Block 10'],
                ['LNG-M6-B10-PD-TBHD8', 'Transverse Bulkhead FR.8 Above Poop Deck Panel Block 10'],
                ['LNG-M6-B10-PD-TBHD9+300', 'Transverse Bulkhead FR.9+300 Above Poop Deck Panel Block 10'],
            ],
            43 => [['LNG-M6-B10-PD', 'Assembly Block B10-PD']],
            44 => [
                ['LNG-M6-B10-PD-TBHD15-KNKL', 'Transverse Bulkhead FR.15 Knuckle Above Bridge Deck Panel Block 10'],
                ['LNG-M6-B10-WH-FWD9700', 'Floor Wheel House Deck Panel 9700 Above Base line Block 10'],
                ['LNG-M6-B10-WH-SS-P', 'Side Shell Above Bridge Deck Block 10 (Portside)'],
                ['LNG-M6-B10-WH-SS-S', 'Side Shell Above Bridge Deck Block 10 (Starboard)'],
                ['LNG-M6-B10-WH-TBHD11', 'Transverse Bulkhead FR.11 Above Bridge Deck Panel Block 10'],
                ['LNG-M6-B10-WH-TBHD3+300', 'Transverse Bulkhead FR.3+300 Above Bridge Deck Panel Block 10'],
                ['LNG-M6-B10-WH-TBHD8', 'Transverse Bulkhead FR.8 Above Bridge Deck Panel Block 10'],
                ['LNG-M6-B10-WH-TBHD9+300', 'Transverse Bulkhead FR.9+300 Above Bridge Deck Panel Block 10'],
                ['LNG-M6-B10-WH-TD11900', 'Top Deck Panel 8700 Above Base line Block 10'],
            ],
            45 => [['LNG-M6-B10-WH', 'Assembly Block B10-WH']],
            46 => [['LNG-M6', 'Join Block 10 (Poop Deck) & Block 10 (Wheel House)']],
        ];

        // Build lookup: assembly_code string → [old_subblok_id, description]
        $asmLookup = [];
        foreach ($assemblyCodes as $oldSbId => $codes) {
            foreach ($codes as [$code, $desc]) {
                $asmLookup[$code] = ['subblok' => $oldSbId, 'desc' => $desc];
            }
        }

        // ====================================================================
        // 6. INSPECTION CODES (kode) with role values
        //    Only codes that appear in kode_assembly_code mappings.
        //    Role values resolved from role_code_assignment (WORKSHOP for fabrication, SHIP for erection)
        // ====================================================================
        $kodes = [
            '1.00.01' => ['item' => 'Material Verification',           'yard' => 'W',  'class' => 'RV', 'os' => 'RV', 'stat' => '-'],
            '1.00.02' => ['item' => 'Visual Inspection',               'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '1.00.03' => ['item' => 'Heat & Dimension Check',          'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.01.01' => ['item' => 'Fit-up & Scantling',              'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.01.02' => ['item' => 'Welding Check before shell plate', 'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.01.05' => ['item' => 'Dimensional Check',               'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.02.01' => ['item' => 'Fit-up',                          'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.02.02' => ['item' => 'Welding preparation for down hand','yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.02.03' => ['item' => 'Welding of Joint Plate down hand', 'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.02.04' => ['item' => 'Back gouging',                    'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.02.05' => ['item' => 'Bottom Inspection',               'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.02.07' => ['item' => 'Hull measurement',                'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => 'W'],
            '3.02.08' => ['item' => 'Keel Deflection',                 'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.03.02' => ['item' => 'Leak Test',                       'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
            '3.03.03' => ['item' => 'Hose Test',                       'yard' => 'W',  'class' => 'W',  'os' => 'W',  'stat' => '-'],
        ];

        // ====================================================================
        // 7. KODE → SUB-BLOCK GROUP MAPPINGS
        //    Defines which inspection codes apply to which sub-block groups
        // ====================================================================

        // Panel/fabrication sub-blocks (for codes 1.xx, 3.01.xx)
        $panelSubbloks = [1,2,5,6,10,11,12,14,15,16,19,20,21,23,24,25,28,29,30,32,33,34,38,39,42,44];

        // Block 1 panel sub-blocks only (for codes 1.00.xx)
        $b1PanelSubbloks = [1,2];

        // Join/erection sub-blocks (for codes 3.02.xx)
        $joinSubbloks = [3,4,7,8,9,13,17,18,22,26,27,31,35,36,37,40,41,43,45,46];

        // Block 1 join sub-blocks only (for codes 3.03.xx)
        $b1JoinSubbloks = [3,4];

        // Map kode → applicable sub-block groups
        $kodeSubblokGroups = [
            '1.00.01' => $b1PanelSubbloks,
            '1.00.02' => $b1PanelSubbloks,
            '1.00.03' => $b1PanelSubbloks,
            '3.01.01' => $panelSubbloks,
            '3.01.02' => $panelSubbloks,
            '3.01.05' => $panelSubbloks,
            '3.02.01' => $joinSubbloks,
            '3.02.02' => $joinSubbloks,
            '3.02.03' => $joinSubbloks,
            '3.02.04' => $joinSubbloks,
            '3.02.05' => $joinSubbloks,
            '3.02.07' => $joinSubbloks,
            '3.02.08' => $joinSubbloks,
            '3.03.02' => $b1JoinSubbloks,
            '3.03.03' => $b1JoinSubbloks,
        ];

        // ====================================================================
        // 8. GENERATE TEMPLATE ITP ENTRIES
        // ====================================================================
        $itpRows = [];
        $now = now();

        foreach ($kodeSubblokGroups as $kode => $subblokIds) {
            $kodeInfo = $kodes[$kode];

            foreach ($subblokIds as $oldSbId) {
                if (!isset($assemblyCodes[$oldSbId])) continue;

                $newSubBlokId = $subBlokIdMap[$oldSbId];

                foreach ($assemblyCodes[$oldSbId] as [$asmCode, $asmDesc]) {
                    $itpRows[] = [
                        'template_sub_blok_id' => $newSubBlokId,
                        'assembly_code'        => $asmCode,
                        'assembly_description' => $asmDesc,
                        'code'                 => $kode,
                        'item'                 => $kodeInfo['item'],
                        'yard_val'             => $kodeInfo['yard'],
                        'class_val'            => $kodeInfo['class'],
                        'os_val'               => $kodeInfo['os'],
                        'stat_val'             => $kodeInfo['stat'],
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ];
                }
            }
        }

        // Batch insert in chunks of 200
        foreach (array_chunk($itpRows, 200) as $chunk) {
            DB::table('template_itps')->insert($chunk);
        }

        $this->command->info("✅ Mini LNG 36 TEU template seeded: 6 modules, 10 blocks, 46 sub-blocks, " . count($itpRows) . " ITP entries.");
    }
}
