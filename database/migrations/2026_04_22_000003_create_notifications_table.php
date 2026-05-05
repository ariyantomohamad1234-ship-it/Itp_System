<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['submit', 'approved', 'rejected', 'needs_revision'])->default('submit');
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->foreignId('related_itp_id')->nullable()->constrained('itps')->nullOnDelete();
            $table->foreignId('related_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
