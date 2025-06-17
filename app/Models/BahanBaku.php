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
        'kategori',
        'sub_kategori',
        'status_aktif',
        'satuan_utama',
        'pilihan_warna',
        'nama_warna_custom',
        'berat',
        'tinggi',
        'tebal',
        'gramasi_densitas',
        'volume',
        'konversi_satuan_json',
        'pemasok_utama_id',
        'harga_terakhir',
        'histori_harga_json',
        'stok_saat_ini',
        'stok_minimum',
        'stok_maksimum',
        'foto_produk_url',
        'dokumen_pendukung_json',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'konversi_satuan_json' => 'array',
        'histori_harga_json' => 'array',
        'dokumen_pendukung_json' => 'array',
        'berat' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'tebal' => 'decimal:2',
        'gramasi_densitas' => 'decimal:2',
        'volume' => 'decimal:2',
        'harga_terakhir' => 'integer',
        'stok_saat_ini' => 'integer',
        'stok_minimum' => 'integer',
        'stok_maksimum' => 'integer'
    ];

    /**
     * Get the primary supplier associated with the raw material.
     */
    public function pemasokUtama()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_utama_id');
    }
}
