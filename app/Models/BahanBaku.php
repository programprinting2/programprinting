<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;
    protected $table = 'BahanBaku';
    protected $fillable = [
        'id',
        'NamaBahan',
        'Keterangan',
        'IdSupplier',
        'KelolaStok',
        'IdSatuan',
        'Lebar',
        'Panjang',
        'Tinggi',
        'Berat',
        'Warna',
        'IdJenisBahanBaku',
        'FotoProduk',
        'StokMinimum',
        'Stok',
        'TglDibuat',
        'TglDiupdate',
        'Aktif',
        'IdUnit',
        'IdTipeBahan',
        'Volume',
        'IdSatuanKemasan',
        'PanjangKemasan',
        'LebarKemasan',
        'VolumeKemasan',
        'IsiKemasanTidakPasti',
        'IdUnitKemasan',
        'IsiKemasan',
        'IdSatuanIsi',
    ];
}
