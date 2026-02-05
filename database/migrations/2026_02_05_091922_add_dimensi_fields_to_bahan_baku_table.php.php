<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->boolean('is_metric')->default(false)->after('harga_terakhir');
            $table->string('metric_unit', 5)->nullable()->after('is_metric');
            $table->decimal('lebar', 10, 2)->nullable()->after('metric_unit');
            $table->decimal('panjang', 10, 2)->nullable()->after('lebar');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['is_metric', 'metric_unit', 'lebar', 'panjang']);
        });
    }
};