<?php
// database/migrations/2026_01_20_054321_add_cascade_columns_to_produk.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Tambah kolom mesin_ids sebagai ARRAY
        DB::statement('ALTER TABLE produk ADD COLUMN mesin_ids integer[]');

        Schema::table('produk', function (Blueprint $table) {
            $table->boolean('needs_recalc')->default(false)->after('total_modal_keseluruhan');
        });

        // Function
        DB::statement("
            CREATE OR REPLACE FUNCTION update_produk_mesin_ids()
            RETURNS trigger AS $$
            BEGIN
                NEW.mesin_ids :=
                    CASE
                        WHEN NEW.parameter_modal_json IS NULL
                        OR json_array_length(NEW.parameter_modal_json) = 0
                        THEN '{}'
                        ELSE (
                            SELECT ARRAY(
                                SELECT DISTINCT (elem->>'mesin_id')::int
                                FROM json_array_elements(NEW.parameter_modal_json) elem
                                WHERE elem->>'mesin_id' IS NOT NULL
                                AND elem->>'mesin_id' != ''
                            )
                        )
                    END;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger
        DB::statement("
            CREATE TRIGGER trg_update_produk_mesin_ids
            BEFORE INSERT OR UPDATE OF parameter_modal_json
            ON produk
            FOR EACH ROW
            EXECUTE FUNCTION update_produk_mesin_ids();
        ");

        // GIN index (SEKARANG AMAN)
        DB::statement('
            CREATE INDEX idx_produk_mesin_ids
            ON produk USING GIN (mesin_ids)
        ');

        DB::statement('
            CREATE INDEX idx_produk_needs_recalc
            ON produk (needs_recalc)
        ');
    }


    public function down()
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_update_produk_mesin_ids ON produk');
DB::statement('DROP FUNCTION IF EXISTS update_produk_mesin_ids()');

        Schema::table('produk', function (Blueprint $table) {
            DB::statement('DROP INDEX IF EXISTS idx_produk_mesin_ids');
            DB::statement('DROP INDEX IF EXISTS idx_produk_needs_recalc');
            $table->dropColumn(['mesin_ids', 'needs_recalc']);
        });
    }
};