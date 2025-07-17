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
        Schema::create('rak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudang')->onDelete('cascade');
            $table->string('kode_rak', 20)->unique();
            $table->string('nama_rak', 100);
            $table->unsignedInteger('kapasitas');
            $table->unsignedInteger('jumlah_level')->default(1);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->decimal('lebar', 6, 2)->nullable();
            $table->decimal('tinggi', 6, 2)->nullable();
            $table->decimal('kedalaman', 6, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rak');
    }
}; 