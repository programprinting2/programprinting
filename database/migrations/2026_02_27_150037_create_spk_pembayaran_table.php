<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spk_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spk')->cascadeOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->string('metode', 50);
            $table->date('tanggal');
            $table->string('referensi', 100)->nullable();
            $table->string('catatan', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['spk_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_pembayaran');
    }
};