<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterMesin;
use Carbon\Carbon;

class MesinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data Printer Large Format
        MasterMesin::create([
            'nama_mesin' => 'Roland TrueVIS VG3-640',
            'tipe_mesin' => 'Printer Large Format',
            'merek' => 'Roland',
            'model' => 'TrueVIS VG3-640',
            'nomor_seri' => 'VG3640-2023001',
            'status' => 'Aktif',
            'tanggal_pembelian' => Carbon::create(2023, 1, 15),
            'harga_pembelian' => 385000000,
            'lebar_media_maksimum' => 1625,
            'detail_mesin' => [
                ['nama' => 'Resolusi', 'nilai' => '1440 x 1440', 'satuan' => 'dpi'],
                ['nama' => 'Kecepatan Cetak', 'nilai' => '12.3', 'satuan' => 'm²/jam'],
                ['nama' => 'Jenis Tinta', 'nilai' => 'Eco-Solvent', 'satuan' => '-'],
                ['nama' => 'Jumlah Head', 'nilai' => '8', 'satuan' => 'buah']
            ],
            'deskripsi' => 'Printer eco-solvent generasi terbaru dari Roland dengan kualitas cetak premium.',
            'catatan_tambahan' => 'Maintenance rutin setiap hari Senin.'
        ]);

        MasterMesin::create([
            'nama_mesin' => 'Epson SureColor S80670',
            'tipe_mesin' => 'Printer Large Format',
            'merek' => 'Epson',
            'model' => 'SureColor S80670',
            'nomor_seri' => 'SC80670-2022105',
            'status' => 'Maintenance',
            'tanggal_pembelian' => Carbon::create(2022, 8, 20),
            'harga_pembelian' => 298000000,
            'lebar_media_maksimum' => 1626,
            'detail_mesin' => [
                ['nama' => 'Resolusi', 'nilai' => '1440 x 1440', 'satuan' => 'dpi'],
                ['nama' => 'Kecepatan Cetak', 'nilai' => '15.5', 'satuan' => 'm²/jam'],
                ['nama' => 'Jenis Tinta', 'nilai' => 'Solvent', 'satuan' => '-'],
                ['nama' => 'Jumlah Head', 'nilai' => '10', 'satuan' => 'buah']
            ],
            'deskripsi' => 'Printer solvent Epson dengan 10 warna untuk hasil cetak yang akurat.',
            'catatan_tambahan' => 'Head cleaning setiap pagi sebelum operasional.'
        ]);

        // Data Digital Printer A3+
        MasterMesin::create([
            'nama_mesin' => 'Epson SureColor P8570D',
            'tipe_mesin' => 'Digital Printer A3+',
            'merek' => 'Epson',
            'model' => 'SureColor P8570D',
            'nomor_seri' => 'P8570D-2023056',
            'status' => 'Aktif',
            'tanggal_pembelian' => Carbon::create(2023, 3, 10),
            'harga_pembelian' => 85000000,
            'lebar_media_maksimum' => 32.9,
            'detail_mesin' => [
                ['nama' => 'Resolusi', 'nilai' => '2400 x 1200', 'satuan' => 'dpi'],
                ['nama' => 'Kecepatan Cetak', 'nilai' => '60', 'satuan' => 'lembar/jam'],
                ['nama' => 'Jenis Tinta', 'nilai' => 'Pigment', 'satuan' => '-'],
                ['nama' => 'Jumlah Warna', 'nilai' => '6', 'satuan' => 'warna']
            ],
            'deskripsi' => 'Printer A3+ dengan teknologi terbaru untuk hasil cetak foto profesional.',
            'catatan_tambahan' => 'Khusus untuk pencetakan foto dan proof desain.'
        ]);

        // Data Mesin Finishing
        MasterMesin::create([
            'nama_mesin' => 'Mesin Laminating GMP EXCELAM-355Q',
            'tipe_mesin' => 'Mesin Finishing',
            'merek' => 'GMP',
            'model' => 'EXCELAM-355Q',
            'nomor_seri' => 'EXL355Q-2022089',
            'status' => 'Aktif',
            'tanggal_pembelian' => Carbon::create(2022, 11, 5),
            'harga_pembelian' => 45000000,
            'detail_mesin' => [
                ['nama' => 'Dimensi', 'nilai' => '580 x 330 x 400', 'satuan' => 'mm'],
                ['nama' => 'Daya Listrik', 'nilai' => '220', 'satuan' => 'V'],
                ['nama' => 'Kapasitas', 'nilai' => '150', 'satuan' => 'lembar/jam'],
                ['nama' => 'Berat Mesin', 'nilai' => '28', 'satuan' => 'kg'],
                ['nama' => 'Lebar Maksimum', 'nilai' => '355', 'satuan' => 'mm']
            ],
            'deskripsi' => 'Mesin laminating otomatis untuk finishing dokumen dan foto.',
            'catatan_tambahan' => 'Gunakan suhu yang sesuai dengan jenis material.'
        ]);

        MasterMesin::create([
            'nama_mesin' => 'Mesin Cutting Jinka XL-1351',
            'tipe_mesin' => 'Mesin Finishing',
            'merek' => 'Jinka',
            'model' => 'XL-1351',
            'nomor_seri' => 'JK1351-2023012',
            'status' => 'Aktif',
            'tanggal_pembelian' => Carbon::create(2023, 2, 15),
            'harga_pembelian' => 28000000,
            'detail_mesin' => [
                ['nama' => 'Dimensi', 'nilai' => '1680 x 600 x 1050', 'satuan' => 'mm'],
                ['nama' => 'Daya Listrik', 'nilai' => '110-220', 'satuan' => 'V'],
                ['nama' => 'Kecepatan Potong', 'nilai' => '800', 'satuan' => 'mm/detik'],
                ['nama' => 'Lebar Maksimum', 'nilai' => '1350', 'satuan' => 'mm'],
                ['nama' => 'Tekanan Pisau', 'nilai' => '50-500', 'satuan' => 'gram']
            ],
            'deskripsi' => 'Cutting plotter untuk pembuatan stiker dan cutting vinyl.',
            'catatan_tambahan' => 'Ganti pisau setiap 3 bulan atau sesuai pemakaian.'
        ]);
    }
}
