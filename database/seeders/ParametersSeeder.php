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
        
        // Insert master parameters
        DB::table('parameter')->insert([
            ['id' => 1, 'nama_parameter' => 'KATEGORI PRODUK', 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_parameter' => 'MODE WARNA A3+', 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_parameter' => 'SATUAN', 'keterangan' => null, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_parameter' => 'SUB KATEGORI BAHAN LEMBARAN', 'keterangan' => 'Sub kategori untuk bahan lembaran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nama_parameter' => 'SUB KATEGORI BAHAN ROLL', 'keterangan' => 'Sub kategori untuk bahan roll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nama_parameter' => 'SUB KATEGORI BAHAN CAIR', 'keterangan' => 'Sub kategori untuk bahan cair', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nama_parameter' => 'SUB KATEGORI BAHAN BERAT', 'keterangan' => 'Sub kategori untuk bahan berat', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'nama_parameter' => 'SUB KATEGORI BAHAN UNIT/BIJI', 'keterangan' => 'Sub kategori untuk bahan unit/biji', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'nama_parameter' => 'SUB KATEGORI BAHAN PAKET/SET', 'keterangan' => 'Sub kategori untuk bahan paket/set', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'nama_parameter' => 'SUB KATEGORI BAHAN WAKTU/JASA', 'keterangan' => 'Sub kategori untuk bahan waktu/jasa', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN LEMBARAN
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 4, 'nama_detail_parameter' => 'Kertas HVS', 'keterangan' => 'Kertas HVS berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 4, 'nama_detail_parameter' => 'Kertas Art Paper', 'keterangan' => 'Kertas art paper berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 4, 'nama_detail_parameter' => 'Kertas Stiker', 'keterangan' => 'Kertas stiker berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 4, 'nama_detail_parameter' => 'Kertas NCR', 'keterangan' => 'Kertas NCR berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 4, 'nama_detail_parameter' => 'Kertas Karton', 'keterangan' => 'Kertas karton berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN ROLL
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 5, 'nama_detail_parameter' => 'Kertas Roll Thermal', 'keterangan' => 'Kertas roll untuk printer thermal', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 5, 'nama_detail_parameter' => 'Kertas Roll Kasir', 'keterangan' => 'Kertas roll untuk printer kasir', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 5, 'nama_detail_parameter' => 'Kertas Roll Label', 'keterangan' => 'Kertas roll untuk label', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN CAIR
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 6, 'nama_detail_parameter' => 'Tinta CMYK', 'keterangan' => 'Tinta warna CMYK', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 6, 'nama_detail_parameter' => 'Tinta Hitam', 'keterangan' => 'Tinta hitam', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 6, 'nama_detail_parameter' => 'Toner', 'keterangan' => 'Toner untuk printer laser', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 6, 'nama_detail_parameter' => 'Cairan Pembersih', 'keterangan' => 'Cairan pembersih printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN BERAT
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 7, 'nama_detail_parameter' => 'Kertas Kiloan', 'keterangan' => 'Kertas dijual per kilogram', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 7, 'nama_detail_parameter' => 'Bahan Baku Kiloan', 'keterangan' => 'Bahan baku dijual per kilogram', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN UNIT/BIJI
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 8, 'nama_detail_parameter' => 'Cartridge', 'keterangan' => 'Cartridge printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 8, 'nama_detail_parameter' => 'Drum Unit', 'keterangan' => 'Drum unit printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 8, 'nama_detail_parameter' => 'Fuser Unit', 'keterangan' => 'Fuser unit printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN PAKET/SET
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 9, 'nama_detail_parameter' => 'Set Tinta', 'keterangan' => 'Set tinta lengkap', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 9, 'nama_detail_parameter' => 'Set Maintenance', 'keterangan' => 'Set perawatan printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SUB KATEGORI BAHAN WAKTU/JASA
        DB::table('detail_parameters')->insert([
            ['master_parameter_id' => 10, 'nama_detail_parameter' => 'Jasa Service', 'keterangan' => 'Jasa service printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['master_parameter_id' => 10, 'nama_detail_parameter' => 'Jasa Training', 'keterangan' => 'Jasa training penggunaan printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
} 