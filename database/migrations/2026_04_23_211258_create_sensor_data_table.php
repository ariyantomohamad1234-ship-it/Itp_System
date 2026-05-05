<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('itp_id')->nullable()->constrained('itps')->onDelete('set null');
            $table->float('altitude')->default(0);
            $table->float('roll')->default(0);
            $table->float('laser_distance')->default(0);
            $table->string('status')->default('Toleransi'); // Lurus, Toleransi, Tidak Lurus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
