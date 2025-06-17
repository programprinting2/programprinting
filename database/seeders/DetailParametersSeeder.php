<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DetailParametersSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('detail_parameters')->insert([
            ['id' => 1, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN OUTDOOR', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN ECO SOVENT', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN UV', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN A3+', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN DIRECT TEXTILE', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'BARANG UNTUK DIJUAL', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN LASER', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'master_parameter_id' => 2, 'nama_detail_parameter' => 'COLOR', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'master_parameter_id' => 2, 'nama_detail_parameter' => 'BLACK AND WHITE', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'master_parameter_id' => 2, 'nama_detail_parameter' => 'WHITE', 'isi_parameter' => null, 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
} 