<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter enum to add 'needs_revision' status
        DB::statement("ALTER TABLE itp_data MODIFY COLUMN status ENUM('pending', 'done', 'approved', 'rejected', 'needs_revision') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE itp_data MODIFY COLUMN status ENUM('pending', 'done', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
