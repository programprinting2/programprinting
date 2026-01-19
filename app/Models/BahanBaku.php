<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'sub_satuan_id',
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
        return $this->belongsTo(\App\Models\SubDetailParameter::class, 'sub_kategori_id');
    }

    public function kategoriDetail()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'kategori_id');
    }

    public function satuanUtamaDetail()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'satuan_utama_id');
    }

    public function subSatuanDetail()
    {
        return $this->belongsTo(\App\Models\SubDetailParameter::class, 'sub_satuan_id');
    }

    // Relationship dengan produk
    public function produks(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'produk_bahan_baku')
            ->withPivot(['jumlah', 'harga_snapshot', 'harga_updated_at']);
    }

    // Observer untuk cascade update
    protected static function booted()
    {
        static::updated(function ($bahanBaku) {
            if ($bahanBaku->wasChanged('harga_terakhir')) {
                $oldPrice = $bahanBaku->getOriginal('harga_terakhir');
                $newPrice = $bahanBaku->harga_terakhir;
                
                $affectedRows = DB::table('produk_bahan_baku')
                    ->where('bahan_baku_id', $bahanBaku->id)
                    ->update([
                        'harga_snapshot' => $newPrice,
                        'harga_updated_at' => now(),
                    ]);
                
                if ($affectedRows > 0) {
                $affectedProdukIds = DB::table('produk_bahan_baku')
                    ->where('bahan_baku_id', $bahanBaku->id)
                    ->pluck('produk_id');
                
                foreach ($affectedProdukIds as $produkId) {
                    $produk = Produk::find($produkId);
                    if ($produk) {
                        $produk->updateTotalModal();
                    }
                }
            }
                \Log::info("Cascade price update", [
                    'bahan_baku' => $bahanBaku->nama_bahan,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'affected_junction_records' => $affectedRows,
                    'affected_products' => $bahanBaku->produks()->count(),
                ]);
            }
        });
    }
}
