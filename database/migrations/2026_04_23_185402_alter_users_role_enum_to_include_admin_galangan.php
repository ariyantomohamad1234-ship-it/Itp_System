<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'admin_galangan', 'yard', 'class', 'os', 'stat') DEFAULT 'yard'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'yard', 'class', 'os', 'stat') DEFAULT 'yard'");
    }
};
