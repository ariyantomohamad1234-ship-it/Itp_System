<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master template project
        Schema::create('project_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Template modules
        Schema::create('template_moduls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_template_id')->constrained()->onDelete('cascade');
            $table->string('nama_modul');
            $table->text('deskripsi')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Template blocks
        Schema::create('template_bloks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_modul_id')->constrained()->onDelete('cascade');
            $table->string('nama_blok');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Template sub-blocks
        Schema::create('template_sub_bloks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_blok_id')->constrained()->onDelete('cascade');
            $table->string('nama_sub_blok');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Template ITP entries
        Schema::create('template_itps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_sub_blok_id')->constrained()->onDelete('cascade');
            $table->string('assembly_code');
            $table->text('assembly_description')->nullable();
            $table->string('code');
            $table->string('item');
            $table->string('yard_val')->default('-');
            $table->string('class_val')->default('-');
            $table->string('os_val')->default('-');
            $table->string('stat_val')->default('-');
            $table->timestamps();
        });

        // Tambahkan template_id ke projects table (nullable, untuk tracking)
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('status')
                  ->constrained('project_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_id');
        });
        Schema::dropIfExists('template_itps');
        Schema::dropIfExists('template_sub_bloks');
        Schema::dropIfExists('template_bloks');
        Schema::dropIfExists('template_moduls');
        Schema::dropIfExists('project_templates');
    }
};
