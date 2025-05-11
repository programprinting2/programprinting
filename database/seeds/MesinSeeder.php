<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MasterMesin;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MesinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenisMesin = ['Offset', 'Digital', 'Flexo', 'Screen', 'Letterpress'];
        $pabrikan = ['Heidelberg', 'Komori', 'KBA', 'Manroland', 'Ryobi'];
        $lokasi = ['Gudang A', 'Gudang B', 'Area Produksi 1', 'Area Produksi 2', 'Area Finishing'];
        
        for ($i = 1; $i <= 20; $i++) {
            $tanggalBeli = Carbon::now()->subMonths(rand(1, 36));
            $tanggalPemeliharaanTerakhir = Carbon::now()->subMonths(rand(1, 6));
            $tanggalPemeliharaanSelanjutnya = Carbon::now()->addMonths(rand(1, 6));
            
            MasterMesin::create([
                'kode_mesin' => 'M' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_mesin' => 'Mesin ' . $jenisMesin[array_rand($jenisMesin)] . ' ' . $i,
                'model_mesin' => 'Model-' . rand(1000, 9999),
                'jenis_mesin' => $jenisMesin[array_rand($jenisMesin)],
                'keterangan' => 'Mesin ' . $jenisMesin[array_rand($jenisMesin)] . ' dengan kapasitas produksi tinggi',
                'non_produksi' => rand(0, 1),
                'tanggal_beli' => $tanggalBeli,
                'aktif' => rand(0, 1),
                'nomor_seri' => 'SN-' . strtoupper(substr(md5(rand()), 0, 8)),
                'pabrikan' => $pabrikan[array_rand($pabrikan)],
                'lokasi_pemeliharaan' => $lokasi[array_rand($lokasi)],
                'tanggal_pemeliharaan_terakhir' => $tanggalPemeliharaanTerakhir,
                'tanggal_pemeliharaan_selanjutnya' => $tanggalPemeliharaanSelanjutnya,
                'catatan' => 'Catatan pemeliharaan untuk mesin ' . $i,
            ]);
        }
    }
}
