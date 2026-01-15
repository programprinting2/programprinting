<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'kode_produk',
        'kategori_utama_id',
        'sub_kategori_id',
        'satuan_id',
        'sub_satuan_id',
        'lebar',
        'panjang',
        'status_aktif',
        'jenis_produk',
        'bahan_baku_json',
        'harga_bertingkat_json',
        'harga_reseller_json',
        'foto_pendukung_json',
        'video_pendukung_json',
        'dokumen_pendukung_json',
        'alur_produksi_json',
        'parameter_modal_json',
        'spesifikasi_teknis_json',
        'biaya_tambahan_json',           
        'total_modal_keseluruhan',
    ];

    protected $casts = [
        'bahan_baku_json' => 'array',
        'harga_bertingkat_json' => 'array',
        'harga_reseller_json' => 'array',
        'foto_pendukung_json' => 'array',
        'video_pendukung_json' => 'array',
        'dokumen_pendukung_json' => 'array',
        'alur_produksi_json' => 'array',
        'parameter_modal_json' => 'array',
        'spesifikasi_teknis_json' => 'array',
        'biaya_tambahan_json' => 'array',
        'status_aktif' => 'boolean',
        'total_modal_keseluruhan' => 'decimal:2'
    ];

    public function kategoriUtama()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'kategori_utama_id');
    }
    public function subKategori()
    {
        return $this->belongsTo(\App\Models\SubDetailParameter::class, 'sub_kategori_id');
    }
    public function satuan()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'satuan_id');
    }
    public function subSatuan()
    {
    return $this->belongsTo(SubDetailParameter::class, 'sub_satuan_id');
    }
}
