<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_satuan_id')->nullable()->after('satuan_utama_id');
            $table->foreign('sub_satuan_id')->references('id')->on('sub_detail_parameter')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['sub_satuan_id']);
            $table->dropColumn('sub_satuan_id');
        });
    }
};