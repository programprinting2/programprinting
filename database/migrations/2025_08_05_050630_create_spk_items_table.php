<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spk')->onDelete('cascade');
            $table->string('nama_produk');
            $table->integer('jumlah');
            $table->string('satuan'); // pcs, lembar, meter, dll
            $table->text('keterangan')->nullable();
            $table->foreignId('bahan_id')->nullable()->constrained('bahan_baku')->onDelete('set null');
            $table->decimal('lebar', 10, 2)->nullable(); // dalam cm
            $table->decimal('panjang', 10, 2)->nullable(); // dalam cm
            $table->integer('biaya_desain')->default(0);
            $table->integer('biaya_finishing')->default(0);
            $table->string('preview_image_path')->nullable();
            $table->json('tipe_finishing')->nullable(); // laminating, cutting, binding, dll
            $table->json('tugas_produksi')->nullable(); // array of tasks
            $table->json('file_pendukung')->nullable(); // array of files
            // $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_biaya')->default(0);
            $table->timestamps();
            
            $table->index(['spk_id']);
            $table->index('nama_produk');
            $table->index('bahan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spk_items');
    }
};
