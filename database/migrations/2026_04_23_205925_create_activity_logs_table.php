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
        Schema::create('activity_logs', function (Blueprint $header) {
            $header->id();
            $header->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $header->string('activity_type'); // e.g., 'submit_itp', 'approve_itp', 'create_project'
            $header->text('description');
            $header->string('subject_type')->nullable(); // Model name
            $header->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $header->string('ip_address')->nullable();
            $header->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
