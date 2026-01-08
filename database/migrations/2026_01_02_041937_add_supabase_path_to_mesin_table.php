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
        Schema::table('mesin', function (Blueprint $table) {
            $table->string('supabase_path')->nullable()->after('cloudinary_public_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            $table->dropColumn('supabase_path');
        });
    }
};
