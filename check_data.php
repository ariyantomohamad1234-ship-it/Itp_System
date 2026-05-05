<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PROJECTS ===\n";
$projects = DB::table('projects')->select('id','nama_project','kode_project','tanggal_mulai','deadline','status')->get();
foreach ($projects as $p) {
    echo "ID:{$p->id} | {$p->nama_project} | {$p->kode_project} | start:{$p->tanggal_mulai} | deadline:{$p->deadline} | status:{$p->status}\n";
}

echo "\n=== MODULS ===\n";
$moduls = DB::table('moduls')->select('id','project_id','nama_modul','start_day','duration_days')->get();
foreach ($moduls as $m) {
    echo "ID:{$m->id} | project:{$m->project_id} | {$m->nama_modul} | start_day:{$m->start_day} | duration:{$m->duration_days}\n";
}
