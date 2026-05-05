<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssemblyCodeSeeder extends Seeder
{
    public function run(): void
    {
        // Extract unique assembly codes from template_itps
        $codes = DB::table('template_itps')
            ->select('assembly_code', 'assembly_description')
            ->distinct()
            ->orderBy('assembly_code')
            ->get();

        $now = now();
        $rows = [];

        foreach ($codes as $c) {
            $rows[] = [
                'code' => $c->assembly_code,
                'description' => $c->assembly_description,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Also extract from live itps table
        $liveCodes = DB::table('itps')
            ->select('assembly_code', 'assembly_description')
            ->distinct()
            ->orderBy('assembly_code')
            ->get();

        $existingCodes = collect($rows)->pluck('code')->toArray();

        foreach ($liveCodes as $c) {
            if (!in_array($c->assembly_code, $existingCodes)) {
                $rows[] = [
                    'code' => $c->assembly_code,
                    'description' => $c->assembly_description,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $existingCodes[] = $c->assembly_code;
            }
        }

        if (!empty($rows)) {
            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('assembly_codes')->insert($chunk);
            }
        }

        $this->command->info("✅ Assembly codes seeded: " . count($rows) . " unique codes.");
    }
}
