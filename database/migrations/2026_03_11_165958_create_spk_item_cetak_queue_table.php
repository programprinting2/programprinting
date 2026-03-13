<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spk_item_cetak_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_item_id')->constrained('spk_items')->cascadeOnDelete();
            $table->foreignId('mesin_id')->constrained('mesin')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['spk_item_id', 'mesin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_item_cetak_queue');
    }
};