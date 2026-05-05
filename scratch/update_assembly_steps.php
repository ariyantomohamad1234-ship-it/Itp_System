<?php
use Illuminate\Support\Facades\DB;

$steps = "Lay down transversal Bulkhead (THD) FR. 3 on jig.\nJoin Bulkhead vertical stiffener (TBHDV) profile\nJoin Bulkhead center girder (BHDCG) and Bulkhead side girder (BHDSG).";

// Update LNG-M1-B1 as an example
DB::table('itps')
    ->where('assembly_code', 'LNG-M1-B1')
    ->update(['assembly_description' => $steps]);

echo "Database updated for LNG-M1-B1 with work steps.\n";
