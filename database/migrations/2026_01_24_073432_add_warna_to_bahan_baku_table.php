<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->unsignedBigInteger('warna_id')->nullable()->after('keterangan');
            $table->foreign('warna_id')->references('id')->on('detail_parameters');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['warna_id']); 
            $table->dropColumn('warna_id'); 
        });
    }
};