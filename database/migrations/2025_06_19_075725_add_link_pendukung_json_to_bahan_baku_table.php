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
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->json('link_pendukung_json')->nullable()->after('dokumen_pendukung_json');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn('link_pendukung_json');
        });
    }
};
