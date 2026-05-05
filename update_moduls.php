<?php
/**
 * Script to update project modules with proper scheduling
 * Schedule:
 *   Phase 1: Module 2 (day 1-35)
 *   Phase 2: Module 1 (day 36-126) + Module 3 (day 36-112) — parallel
 *   Phase 3: Module 6 (day 127-267)
 *   Phase 4: Module 4 (day 268-386)
 *   Phase 5: Module 5 (day 387-533)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$projectId = 1; // LNG-001

// Check existing modules
$existingModuls = DB::table('moduls')->where('project_id', $projectId)->get();
echo "Existing modules: {$existingModuls->count()}\n";

// Add missing modules if needed
$moduleNames = [
    1 => 'Modul 1 - Hull Construction',
    2 => 'Modul 2 - Piping System',
    3 => 'Modul 3 - Electrical',
    4 => 'Modul 4 - Outfitting',
    5 => 'Modul 5 - Painting & Coating',
    6 => 'Modul 6 - Machinery Installation',
];

// Schedule config
$schedule = [
    2 => ['start_day' => 1,   'duration_days' => 35],   // Phase 1
    1 => ['start_day' => 36,  'duration_days' => 91],   // Phase 2
    3 => ['start_day' => 36,  'duration_days' => 77],   // Phase 2
    6 => ['start_day' => 127, 'duration_days' => 141],  // Phase 3
    4 => ['start_day' => 268, 'duration_days' => 119],  // Phase 4
    5 => ['start_day' => 387, 'duration_days' => 147],  // Phase 5
];

// Add Modul 4, 5, 6 if they don't exist
foreach ([4, 5, 6] as $num) {
    $name = $moduleNames[$num];
    $exists = DB::table('moduls')
        ->where('project_id', $projectId)
        ->where('nama_modul', 'LIKE', "Modul {$num}%")
        ->exists();

    if (!$exists) {
        DB::table('moduls')->insert([
            'project_id' => $projectId,
            'nama_modul' => $name,
            'deskripsi' => null,
            'start_day' => $schedule[$num]['start_day'],
            'duration_days' => $schedule[$num]['duration_days'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Added: {$name}\n";
    } else {
        echo "Already exists: Modul {$num}\n";
    }
}

// Update schedule for existing modules (1, 2, 3)
foreach ([1, 2, 3] as $num) {
    DB::table('moduls')
        ->where('project_id', $projectId)
        ->where('nama_modul', 'LIKE', "Modul {$num}%")
        ->update([
            'start_day' => $schedule[$num]['start_day'],
            'duration_days' => $schedule[$num]['duration_days'],
            'updated_at' => now(),
        ]);
    echo "Updated schedule: Modul {$num} (start_day={$schedule[$num]['start_day']}, duration={$schedule[$num]['duration_days']})\n";
}

// Verify
echo "\n=== FINAL STATE ===\n";
$moduls = DB::table('moduls')->where('project_id', $projectId)->orderBy('start_day')->get();
foreach ($moduls as $m) {
    $endDay = ($m->start_day ?? 0) + ($m->duration_days ?? 0) - 1;
    echo "ID:{$m->id} | {$m->nama_modul} | day {$m->start_day}-{$endDay} ({$m->duration_days} days)\n";
}
