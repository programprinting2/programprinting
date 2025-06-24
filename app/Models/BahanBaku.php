<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bahan_baku';
    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'keterangan',
        'detail_spesifikasi_json',
        'kategori_id',
        'sub_kategori_id',
        'satuan_utama_id',
        'status_aktif',
        'konversi_satuan_json',
        'pemasok_utama_id',
        'harga_terakhir',
        'stok_saat_ini',
        'stok_minimum',
        'stok_maksimum',
        'foto_pendukung_json',
        'video_pendukung_json',
        'dokumen_pendukung_json',
        'link_pendukung_json',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'konversi_satuan_json' => 'array',
        'dokumen_pendukung_json' => 'array',
        'foto_pendukung_json' => 'array',
        'video_pendukung_json' => 'array',
        'harga_terakhir' => 'integer',
        'stok_saat_ini' => 'integer',
        'stok_minimum' => 'integer',
        'stok_maksimum' => 'integer',
        'link_pendukung_json' => 'array',
        'detail_spesifikasi_json' => 'array',
    ];

    /**
     * Get the primary supplier associated with the raw material.
     */
    public function pemasokUtama()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_utama_id');
    }

    public function subKategoriDetail()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'sub_kategori_id');
    }

    public function kategoriDetail()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'kategori_id');
    }

    public function satuanUtamaDetail()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'satuan_utama_id');
    }
}
