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
        Schema::create('detail_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_parameter_id')->constrained('parameter')->onDelete('cascade');
            $table->string('nama_detail_parameter');
            $table->string('isi_parameter')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_parameters');
    }
};
