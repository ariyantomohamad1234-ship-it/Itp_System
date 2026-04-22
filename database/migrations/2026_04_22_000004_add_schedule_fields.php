<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add standard_duration to project_templates (for auto-deadline calculation)
        Schema::table('project_templates', function (Blueprint $table) {
            $table->integer('standard_duration')->nullable()->after('description')
                  ->comment('Standard project duration in days');
        });

        // Add schedule fields to template_moduls
        Schema::table('template_moduls', function (Blueprint $table) {
            $table->integer('start_day')->nullable()->after('sort_order')
                  ->comment('Module start day relative to project start');
            $table->integer('duration_days')->nullable()->after('start_day')
                  ->comment('Module duration in days');
        });

        // Add schedule fields to moduls (actual project modules)
        Schema::table('moduls', function (Blueprint $table) {
            $table->integer('start_day')->nullable()->after('deskripsi')
                  ->comment('Module start day relative to project start');
            $table->integer('duration_days')->nullable()->after('start_day')
                  ->comment('Module duration in days');
        });
    }

    public function down(): void
    {
        Schema::table('project_templates', function (Blueprint $table) {
            $table->dropColumn('standard_duration');
        });
        Schema::table('template_moduls', function (Blueprint $table) {
            $table->dropColumn(['start_day', 'duration_days']);
        });
        Schema::table('moduls', function (Blueprint $table) {
            $table->dropColumn(['start_day', 'duration_days']);
        });
    }
};
