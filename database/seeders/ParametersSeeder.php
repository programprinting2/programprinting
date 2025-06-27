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
            ['id' => 4, 'nama_parameter' => 'KATEGORI BAHAN BAKU', 'keterangan' => 'Kategori utama bahan baku', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for SATUAN
        DB::table('detail_parameters')->insert([
            ['id' => 1, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'lembar', 'keterangan' => 'Satuan untuk bahan lembaran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'meter', 'keterangan' => 'Satuan untuk bahan roll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'liter', 'keterangan' => 'Satuan untuk bahan cair', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'kg', 'keterangan' => 'Satuan untuk bahan berat', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'pcs', 'keterangan' => 'Satuan untuk bahan unit/biji', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'set', 'keterangan' => 'Satuan untuk bahan paket/set', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'master_parameter_id' => 3, 'nama_detail_parameter' => 'jam', 'keterangan' => 'Satuan untuk jasa/waktu', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for KATEGORI PRODUK
        DB::table('detail_parameters')->insert([
            ['id' => 8, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN OUTDOOR', 'keterangan' => 'Produk cetakan outdoor', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN ECO SOVENT', 'keterangan' => 'Produk cetakan eco solvent', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'master_parameter_id' => 1, 'nama_detail_parameter' => 'CETAKAN UV', 'keterangan' => 'Produk cetakan UV', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for MODE WARNA A3+
        DB::table('detail_parameters')->insert([
            ['id' => 18, 'master_parameter_id' => 2, 'nama_detail_parameter' => 'COLOR', 'keterangan' => 'Mode warna full color', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 19, 'master_parameter_id' => 2, 'nama_detail_parameter' => 'BLACK AND WHITE', 'keterangan' => 'Mode warna hitam putih', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'master_parameter_id' => 2, 'nama_detail_parameter' => 'WHITE', 'keterangan' => 'Mode warna putih', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert detail parameters for KATEGORI BAHAN BAKU
        DB::table('detail_parameters')->insert([
            ['id' => 11, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Lembaran', 'keterangan' => 'Bahan dalam bentuk lembaran seperti kertas, karton, dll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Roll', 'keterangan' => 'Bahan dalam bentuk roll seperti stiker, banner, dll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Cair', 'keterangan' => 'Bahan dalam bentuk cair seperti tinta, toner, dll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Berat', 'keterangan' => 'Bahan yang diukur berdasarkan berat seperti kain, dll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Unit/Biji', 'keterangan' => 'Bahan dalam bentuk unit atau biji seperti mug, dll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Paket/Set', 'keterangan' => 'Bahan dalam bentuk paket atau set', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'master_parameter_id' => 4, 'nama_detail_parameter' => 'Bahan Waktu/Jasa', 'keterangan' => 'Bahan berupa jasa atau waktu kerja', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Insert sub detail parameters untuk menghubungkan kategori dengan sub kategori
        DB::table('sub_detail_parameter')->insert([
            // Bahan Lembaran (kategori_id: 11) - Sub kategori bahan lembaran
            ['id' => 1, 'detail_parameter_id' => 11, 'nama_sub_detail_parameter' => 'Kertas HVS', 'keterangan' => 'Kertas HVS berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'detail_parameter_id' => 11, 'nama_sub_detail_parameter' => 'Kertas Art Paper', 'keterangan' => 'Kertas art paper berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'detail_parameter_id' => 11, 'nama_sub_detail_parameter' => 'Kertas Stiker', 'keterangan' => 'Kertas stiker berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'detail_parameter_id' => 11, 'nama_sub_detail_parameter' => 'Kertas NCR', 'keterangan' => 'Kertas NCR berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'detail_parameter_id' => 11, 'nama_sub_detail_parameter' => 'Kertas Karton', 'keterangan' => 'Kertas karton berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'detail_parameter_id' => 11, 'nama_sub_detail_parameter' => 'Kertas Art Carton', 'keterangan' => 'Kertas art carton berbagai ukuran', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            
            // Bahan Roll (kategori_id: 12) - Sub kategori bahan roll
            ['id' => 7, 'detail_parameter_id' => 12, 'nama_sub_detail_parameter' => 'Kertas Roll Thermal', 'keterangan' => 'Kertas roll untuk printer thermal', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'detail_parameter_id' => 12, 'nama_sub_detail_parameter' => 'Kertas Roll Kasir', 'keterangan' => 'Kertas roll untuk printer kasir', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'detail_parameter_id' => 12, 'nama_sub_detail_parameter' => 'Kertas Roll Label', 'keterangan' => 'Kertas roll untuk label', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'detail_parameter_id' => 12, 'nama_sub_detail_parameter' => 'Stiker Vinyl', 'keterangan' => 'Stiker vinyl untuk outdoor', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'detail_parameter_id' => 12, 'nama_sub_detail_parameter' => 'Banner', 'keterangan' => 'Banner untuk outdoor', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'detail_parameter_id' => 12, 'nama_sub_detail_parameter' => 'Stiker Vinyl Glossy', 'keterangan' => 'Stiker vinyl glossy untuk outdoor printing', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            
            // Bahan Cair (kategori_id: 13) - Sub kategori bahan cair
            ['id' => 13, 'detail_parameter_id' => 13, 'nama_sub_detail_parameter' => 'Tinta CMYK', 'keterangan' => 'Tinta warna CMYK', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'detail_parameter_id' => 13, 'nama_sub_detail_parameter' => 'Tinta Hitam', 'keterangan' => 'Tinta hitam', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'detail_parameter_id' => 13, 'nama_sub_detail_parameter' => 'Tinta DTF', 'keterangan' => 'Tinta DTF untuk printer DTF', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'detail_parameter_id' => 13, 'nama_sub_detail_parameter' => 'Toner', 'keterangan' => 'Toner untuk printer laser', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'detail_parameter_id' => 13, 'nama_sub_detail_parameter' => 'Cairan Pembersih', 'keterangan' => 'Cairan pembersih printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            
            // Bahan Berat (kategori_id: 14) - Sub kategori bahan berat
            ['id' => 18, 'detail_parameter_id' => 14, 'nama_sub_detail_parameter' => 'Kertas Kiloan', 'keterangan' => 'Kertas dijual per kilogram', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 19, 'detail_parameter_id' => 14, 'nama_sub_detail_parameter' => 'Kain CVC', 'keterangan' => 'Kain CVC untuk kaos', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'detail_parameter_id' => 14, 'nama_sub_detail_parameter' => 'Kain', 'keterangan' => 'Kain untuk tekstil', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 21, 'detail_parameter_id' => 14, 'nama_sub_detail_parameter' => 'Bahan Baku Kiloan', 'keterangan' => 'Bahan baku dijual per kilogram', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            
            // Bahan Unit/Biji (kategori_id: 15) - Sub kategori bahan unit/biji
            ['id' => 22, 'detail_parameter_id' => 15, 'nama_sub_detail_parameter' => 'Cartridge', 'keterangan' => 'Cartridge printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 23, 'detail_parameter_id' => 15, 'nama_sub_detail_parameter' => 'Drum Unit', 'keterangan' => 'Drum unit printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 24, 'detail_parameter_id' => 15, 'nama_sub_detail_parameter' => 'Fuser Unit', 'keterangan' => 'Fuser unit printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 25, 'detail_parameter_id' => 15, 'nama_sub_detail_parameter' => 'Mug Sublimasi', 'keterangan' => 'Mug keramik untuk sublimasi', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 26, 'detail_parameter_id' => 15, 'nama_sub_detail_parameter' => 'Merchandise', 'keterangan' => 'Merchandise seperti mug, kaos, dll', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            
            // Bahan Paket/Set (kategori_id: 16) - Sub kategori bahan paket/set
            ['id' => 27, 'detail_parameter_id' => 16, 'nama_sub_detail_parameter' => 'Set Tinta', 'keterangan' => 'Set tinta lengkap', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 28, 'detail_parameter_id' => 16, 'nama_sub_detail_parameter' => 'Set Maintenance', 'keterangan' => 'Set perawatan printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 29, 'detail_parameter_id' => 16, 'nama_sub_detail_parameter' => 'Lanyard Polyester', 'keterangan' => 'Lanyard polyester dengan kaitan besi', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 30, 'detail_parameter_id' => 16, 'nama_sub_detail_parameter' => 'Set Aksesoris', 'keterangan' => 'Set aksesoris printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            
            // Bahan Waktu/Jasa (kategori_id: 17) - Sub kategori bahan waktu/jasa
            ['id' => 31, 'detail_parameter_id' => 17, 'nama_sub_detail_parameter' => 'Jasa Service', 'keterangan' => 'Jasa service printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 32, 'detail_parameter_id' => 17, 'nama_sub_detail_parameter' => 'Jasa Training', 'keterangan' => 'Jasa training penggunaan printer', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 33, 'detail_parameter_id' => 17, 'nama_sub_detail_parameter' => 'Jasa Desain Grafis', 'keterangan' => 'Jasa desain grafis untuk kebutuhan printing', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 34, 'detail_parameter_id' => 17, 'nama_sub_detail_parameter' => 'Jasa Desain', 'keterangan' => 'Jasa desain grafis', 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
} 