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
                'kategori' => 'Bahan Lembaran',
                'sub_kategori' => 'Kertas Art Carton',
                'status_aktif' => true,
                'satuan_utama' => 'lembar',
                'pilihan_warna' => 'putih',
                'berat' => 260,
                'tinggi' => 42,
                'tebal' => 0.26,
                'gramasi_densitas' => 260,
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
                'stok_maksimum' => 5000
            ],
            [
                'kode_bahan' => 'MAT-0002',
                'nama_bahan' => 'HVS A4 80 gsm',
                'keterangan' => 'Kertas HVS A4 80 gsm',
                'kategori' => 'Bahan Lembaran',
                'sub_kategori' => 'Kertas HVS',
                'status_aktif' => true,
                'satuan_utama' => 'lembar',
                'pilihan_warna' => 'putih',
                'berat' => 80,
                'tinggi' => 29.7,
                'tebal' => 0.08,
                'gramasi_densitas' => 80,
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
                'stok_maksimum' => 10000
            ],

            // Bahan Roll
            [
                'kode_bahan' => 'MAT-0003',
                'nama_bahan' => 'Stiker Vinyl Glossy',
                'keterangan' => 'Stiker vinyl glossy untuk outdoor printing',
                'kategori' => 'Bahan Roll',
                'sub_kategori' => 'Stiker',
                'status_aktif' => true,
                'satuan_utama' => 'meter',
                'pilihan_warna' => 'putih',
                'berat' => 0,
                'tinggi' => 0.5,
                'tebal' => 0.1,
                'gramasi_densitas' => 0,
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
                'stok_maksimum' => 10
            ],

            // Bahan Cair
            [
                'kode_bahan' => 'MAT-0004',
                'nama_bahan' => 'Tinta DTF CMYK',
                'keterangan' => 'Tinta DTF untuk printer DTF, isi 1 Liter',
                'kategori' => 'Bahan Cair',
                'sub_kategori' => 'Tinta',
                'status_aktif' => true,
                'satuan_utama' => 'liter',
                'pilihan_warna' => 'custom',
                'nama_warna_custom' => 'CMYK',
                'volume' => 1,
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
                'stok_maksimum' => 20
            ],

            // Bahan Berat
            [
                'kode_bahan' => 'MAT-0005',
                'nama_bahan' => 'Kain CVC 30s',
                'keterangan' => 'Kain CVC 30s untuk kaos, bahan katun campur polyester',
                'kategori' => 'Bahan Berat',
                'sub_kategori' => 'Kain',
                'status_aktif' => true,
                'satuan_utama' => 'kg',
                'pilihan_warna' => 'custom',
                'nama_warna_custom' => 'Hitam',
                'berat' => 1,
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
                'stok_maksimum' => 100
            ],

            // Bahan Unit/Biji
            [
                'kode_bahan' => 'MAT-0006',
                'nama_bahan' => 'Mug Sublimasi',
                'keterangan' => 'Mug keramik untuk sublimasi, diameter 8cm',
                'kategori' => 'Bahan Unit/Biji',
                'sub_kategori' => 'Merchandise',
                'status_aktif' => true,
                'satuan_utama' => 'pcs',
                'pilihan_warna' => 'putih',
                'berat' => 300,
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
                'stok_maksimum' => 300
            ],

            // Bahan Paket/Set
            [
                'kode_bahan' => 'MAT-0007',
                'nama_bahan' => 'Lanyard Polyester',
                'keterangan' => 'Lanyard polyester lebar 2cm dengan kaitan besi',
                'kategori' => 'Bahan Paket/Set',
                'sub_kategori' => 'Merchandise',
                'status_aktif' => true,
                'satuan_utama' => 'set',
                'pilihan_warna' => 'custom',
                'nama_warna_custom' => 'Hitam',
                'berat' => 20,
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
                'stok_maksimum' => 500
            ],

            // Bahan Waktu/Jasa
            [
                'kode_bahan' => 'MAT-0008',
                'nama_bahan' => 'Jasa Desain Grafis',
                'keterangan' => 'Jasa desain grafis untuk kebutuhan printing',
                'kategori' => 'Bahan Waktu/Jasa',
                'sub_kategori' => 'Jasa',
                'status_aktif' => true,
                'satuan_utama' => 'jam',
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
                'stok_maksimum' => 0
            ]
        ];

        foreach ($bahanBaku as $bahan) {
            BahanBaku::create($bahan);
        }
    }
} 