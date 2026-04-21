<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_blok_id')->constrained()->onDelete('cascade');
            $table->string('assembly_code');
            $table->text('assembly_description')->nullable(); // penjelas assembly code
            $table->string('code');              // kode inspeksi, e.g. 01.1
            $table->string('item');              // deskripsi pekerjaan
            // Kolom penanda tipe partisipasi per role: W, RV, -, NA
            $table->string('yard_val')->default('-');
            $table->string('class_val')->default('-');
            $table->string('os_val')->default('-');
            $table->string('stat_val')->default('-');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itps');
    }
};
