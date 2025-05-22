<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParametersSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('parameter')->insert([
            ['id' => 1, 'nama_parameter' => 'KATEGORI B', 'keterangan' => 'test', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_parameter' => 'KATEGORI PRODUK', 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_parameter' => 'MODE WARNA A3+', 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_parameter' => 'SATUAN', 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
} 