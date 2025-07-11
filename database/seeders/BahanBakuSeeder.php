<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;

class BahanBakuSeeder extends Seeder
{
    public function run()
    {
        $bahanBaku = [
            // Bahan Lembaran
            [
                'kode_bahan' => 'MAT-0001',
                'nama_bahan' => 'Art Carton 260 gsm',
                'keterangan' => 'Kertas Art Carton 260 gsm ukuran A3',
                'kategori_id' => 11, // Bahan Lembaran
                'sub_kategori_id' => 6, // Kertas Art Carton
                'status_aktif' => true,
                'satuan_utama_id' => 1, // lembar
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Berat', 'nilai' => '260', 'satuan' => 'gsm'],
                    ['nama' => 'Tinggi', 'nilai' => '42', 'satuan' => 'cm'],
                    ['nama' => 'Tebal', 'nilai' => '0.26', 'satuan' => 'mm'],
                    ['nama' => 'Warna', 'nilai' => 'Putih', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'rim', 'ke' => 500, 'satuan_ke' => 'lembar'],
                    ['dari' => 1, 'satuan_dari' => 'pak', 'ke' => 100, 'satuan_ke' => 'lembar']
                ]),
                'pemasok_utama_id' => 1,
                'harga_terakhir' => 15000,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 15000, 'pemasok' => 'PT Kertas Nusantara'],
                    ['tanggal' => '2024-02-01', 'harga' => 14500, 'pemasok' => 'PT Kertas Nusantara']
                ]),
                'stok_saat_ini' => 2500,
                'stok_minimum' => 500,
                'stok_maksimum' => 5000,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],
            [
                'kode_bahan' => 'MAT-0002',
                'nama_bahan' => 'HVS A4 80 gsm',
                'keterangan' => 'Kertas HVS A4 80 gsm',
                'kategori_id' => 11, // Bahan Lembaran
                'sub_kategori_id' => 1, // Kertas HVS
                'status_aktif' => true,
                'satuan_utama_id' => 1, // lembar
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Berat', 'nilai' => '80', 'satuan' => 'gsm'],
                    ['nama' => 'Tinggi', 'nilai' => '29.7', 'satuan' => 'cm'],
                    ['nama' => 'Tebal', 'nilai' => '0.08', 'satuan' => 'mm'],
                    ['nama' => 'Warna', 'nilai' => 'Putih', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'rim', 'ke' => 500, 'satuan_ke' => 'lembar'],
                    ['dari' => 1, 'satuan_dari' => 'pak', 'ke' => 100, 'satuan_ke' => 'lembar']
                ]),
                'pemasok_utama_id' => 1,
                'harga_terakhir' => 5000,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 5000, 'pemasok' => 'PT Kertas Nusantara']
                ]),
                'stok_saat_ini' => 5000,
                'stok_minimum' => 1000,
                'stok_maksimum' => 10000,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],

            // Bahan Roll
            [
                'kode_bahan' => 'MAT-0003',
                'nama_bahan' => 'Stiker Vinyl Glossy',
                'keterangan' => 'Stiker vinyl glossy untuk outdoor printing',
                'kategori_id' => 12, // Bahan Roll
                'sub_kategori_id' => 12, // Stiker Vinyl Glossy
                'status_aktif' => true,
                'satuan_utama_id' => 2, // meter
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Tinggi', 'nilai' => '0.5', 'satuan' => 'm'],
                    ['nama' => 'Tebal', 'nilai' => '0.1', 'satuan' => 'mm'],
                    ['nama' => 'Warna', 'nilai' => 'Putih', 'satuan' => '-'],
                    ['nama' => 'Jenis', 'nilai' => 'Vinyl Glossy', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'roll', 'ke' => 50, 'satuan_ke' => 'meter']
                ]),
                'pemasok_utama_id' => 2,
                'harga_terakhir' => 550000,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 550000, 'pemasok' => 'PT Media Cetak']
                ]),
                'stok_saat_ini' => 8,
                'stok_minimum' => 2,
                'stok_maksimum' => 10,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],

            // Bahan Cair
            [
                'kode_bahan' => 'MAT-0004',
                'nama_bahan' => 'Tinta DTF CMYK',
                'keterangan' => 'Tinta DTF untuk printer DTF, isi 1 Liter',
                'kategori_id' => 13, // Bahan Cair
                'sub_kategori_id' => 15, // Tinta DTF
                'status_aktif' => true,
                'satuan_utama_id' => 3, // liter
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Volume', 'nilai' => '1', 'satuan' => 'liter'],
                    ['nama' => 'Warna', 'nilai' => 'CMYK', 'satuan' => '-'],
                    ['nama' => 'Jenis', 'nilai' => 'DTF', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'liter', 'ke' => 1000, 'satuan_ke' => 'ml']
                ]),
                'pemasok_utama_id' => 3,
                'harga_terakhir' => 450000,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 450000, 'pemasok' => 'PT Tinta Prima']
                ]),
                'stok_saat_ini' => 12,
                'stok_minimum' => 5,
                'stok_maksimum' => 20,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],

            // Bahan Berat
            [
                'kode_bahan' => 'MAT-0005',
                'nama_bahan' => 'Kain CVC 30s',
                'keterangan' => 'Kain CVC 30s untuk kaos, bahan katun campur polyester',
                'kategori_id' => 14, // Bahan Berat
                'sub_kategori_id' => 19, // Kain CVC
                'status_aktif' => true,
                'satuan_utama_id' => 4, // kg
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Berat', 'nilai' => '1', 'satuan' => 'kg'],
                    ['nama' => 'Warna', 'nilai' => 'Hitam', 'satuan' => '-'],
                    ['nama' => 'Jenis', 'nilai' => 'CVC 30s', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'kg', 'ke' => 1000, 'satuan_ke' => 'gram']
                ]),
                'pemasok_utama_id' => 4,
                'harga_terakhir' => 85000,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 85000, 'pemasok' => 'PT Tekstil Jaya']
                ]),
                'stok_saat_ini' => 75,
                'stok_minimum' => 20,
                'stok_maksimum' => 100,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],

            // Bahan Unit/Biji
            [
                'kode_bahan' => 'MAT-0006',
                'nama_bahan' => 'Mug Sublimasi',
                'keterangan' => 'Mug keramik untuk sublimasi, diameter 8cm',
                'kategori_id' => 15, // Bahan Unit/Biji
                'sub_kategori_id' => 25, // Mug Sublimasi
                'status_aktif' => true,
                'satuan_utama_id' => 5, // pcs
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Berat', 'nilai' => '300', 'satuan' => 'gram'],
                    ['nama' => 'Diameter', 'nilai' => '8', 'satuan' => 'cm'],
                    ['nama' => 'Warna', 'nilai' => 'Putih', 'satuan' => '-'],
                    ['nama' => 'Jenis', 'nilai' => 'Keramik', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'box', 'ke' => 12, 'satuan_ke' => 'pcs']
                ]),
                'pemasok_utama_id' => 5,
                'harga_terakhir' => 10800,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 10800, 'pemasok' => 'PT Merchandise Indo']
                ]),
                'stok_saat_ini' => 216,
                'stok_minimum' => 50,
                'stok_maksimum' => 300,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],

            // Bahan Paket/Set
            [
                'kode_bahan' => 'MAT-0007',
                'nama_bahan' => 'Lanyard Polyester',
                'keterangan' => 'Lanyard polyester lebar 2cm dengan kaitan besi',
                'kategori_id' => 16, // Bahan Paket/Set
                'sub_kategori_id' => 29, // Lanyard Polyester
                'status_aktif' => true,
                'satuan_utama_id' => 6, // set
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Berat', 'nilai' => '20', 'satuan' => 'gram'],
                    ['nama' => 'Lebar', 'nilai' => '2', 'satuan' => 'cm'],
                    ['nama' => 'Warna', 'nilai' => 'Hitam', 'satuan' => '-'],
                    ['nama' => 'Jenis', 'nilai' => 'Polyester', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'bundle', 'ke' => 50, 'satuan_ke' => 'set']
                ]),
                'pemasok_utama_id' => 5,
                'harga_terakhir' => 5500,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 5500, 'pemasok' => 'PT Aksesoris Indo']
                ]),
                'stok_saat_ini' => 350,
                'stok_minimum' => 100,
                'stok_maksimum' => 500,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ],

            // Bahan Waktu/Jasa
            [
                'kode_bahan' => 'MAT-0008',
                'nama_bahan' => 'Jasa Desain Grafis',
                'keterangan' => 'Jasa desain grafis untuk kebutuhan printing',
                'kategori_id' => 17, // Bahan Waktu/Jasa
                'sub_kategori_id' => 33, // Jasa Desain Grafis
                'status_aktif' => true,
                'satuan_utama_id' => 7, // jam
                'detail_spesifikasi_json' => json_encode([
                    ['nama' => 'Jenis Jasa', 'nilai' => 'Desain Grafis', 'satuan' => '-'],
                    ['nama' => 'Kategori', 'nilai' => 'Printing', 'satuan' => '-']
                ]),
                'konversi_satuan_json' => json_encode([
                    ['dari' => 1, 'satuan_dari' => 'jam', 'ke' => 60, 'satuan_ke' => 'menit']
                ]),
                'pemasok_utama_id' => null,
                'harga_terakhir' => 150000,
                'histori_harga_json' => json_encode([
                    ['tanggal' => '2024-03-01', 'harga' => 150000, 'pemasok' => 'Internal']
                ]),
                'stok_saat_ini' => 0,
                'stok_minimum' => 0,
                'stok_maksimum' => 0,
                'foto_pendukung_json' => [],
                'video_pendukung_json' => [],
                'dokumen_pendukung_json' => [],
                'link_pendukung_json' => []
            ]
        ];

        foreach ($bahanBaku as $bahan) {
            BahanBaku::create($bahan);
        }
    }
} 