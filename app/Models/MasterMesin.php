<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMesin extends Model
{
    protected $table = 'master_mesin'; // Specify the table name
    protected $primaryKey = 'id'; // Specify the primary key if it's not 'id'
    protected $fillable = [
        'id',
        'kode_mesin',
        'aktif',
        'nama_mesin',
        'model_mesin',
        'jenis_mesin',
        'keterangan',
        'non_produksi', 
        'tanggal_beli',
        'gambar',
        'nomor_seri',
        'pabrikan',
        'lokasi_pemeliharaan',
        'tanggal_pemeliharaan_terakhir',
        'tanggal_pemeliharaan_selanjutnya',
        'catatan'
    ];

    

    use HasFactory;

}