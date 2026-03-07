<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spk_item_cetak_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('spk_item_id')
                ->constrained('spk_items')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedInteger('jumlah');
            // $table->boolean('is_bulk_complete')->default(false);

            $table->timestamps();

            $table->index(['spk_item_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_item_cetak_logs');
    }
};