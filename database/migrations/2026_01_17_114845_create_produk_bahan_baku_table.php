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
        Schema::create('produk_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('restrict');
            $table->decimal('jumlah', 10, 2)->default(1)->comment('Jumlah bahan baku per unit produk');
            $table->integer('harga_snapshot')->comment('Harga bahan baku saat snapshot');
            $table->timestamp('harga_updated_at')->nullable()->comment('Terakhir sync harga');
            $table->timestamps();

            $table->unique(['produk_id', 'bahan_baku_id'], 'unique_produk_bahan_baku');

             // Performance indexes
             $table->index(['bahan_baku_id', 'produk_id'], 'idx_bahan_baku_produk');
             $table->index(['harga_updated_at'], 'idx_harga_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_bahan_baku');
    }
};
