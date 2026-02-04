<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'keterangan',
        'tags',
        'warna_id',
        'harga_bertingkat_json',
        'harga_reseller_json',
        'foto_pendukung_json',
        'video_pendukung_json',
        'dokumen_pendukung_json',
        'alur_produksi_json',
        'parameter_modal_json',
        'spesifikasi_teknis_json',
        'biaya_tambahan_json',
        'finishing_json',           
        'total_modal_keseluruhan',
        'needs_recalc',
        'is_metric',
        'metric_unit',
        'lebar_locked',
        'panjang_locked',
    ];

    protected $casts = [
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
        'total_modal_keseluruhan' => 'decimal:2',
        'lebar' => 'decimal:2',       
        'panjang' => 'decimal:2',
        'mesin_ids' => 'array',
        'needs_recalc' => 'boolean',
        'warna_id' => 'integer',
        'tags' => 'array',
        'finishing_json' => 'array',
        'is_metric' => 'boolean',
        'lebar_locked' => 'boolean',
        'panjang_locked' => 'boolean',
    ];

    // Relationship dengan bahan baku
    public function bahanBakus(): BelongsToMany
    {
        return $this->belongsToMany(BahanBaku::class, 'produk_bahan_baku')
            ->withPivot(['jumlah', 'harga_snapshot', 'harga_updated_at'])
            ->withTimestamps();
    }

    public function warnaDetail()
    {
        return $this->belongsTo(\App\Models\DetailParameter::class, 'warna_id');
    }

    public function produkKomponen(): BelongsToMany
    {
        return $this->belongsToMany(
            Produk::class, 
            'produk_rakitan', 
            'produk_rakitan_id', 
            'produk_komponen_id'
        )->withPivot(['jumlah', 'harga_snapshot', 'harga_updated_at'])
        ->withTimestamps();
    }

    // Sync bahan baku dari array
    public function syncBahanBakus(array $bahanBakuData): void
    {
        $syncData = [];
        
        foreach ($bahanBakuData as $item) {
            $syncData[$item['id']] = [
                'jumlah' => $item['jumlah'] ?? 1,
                'harga_snapshot' => $item['harga'] ?? 0,
                'harga_updated_at' => now(),
            ];
        }
        
        $this->bahanBakus()->sync($syncData);
        $this->updateTotalModal();
    }

    public function syncProdukKomponen(array $produkKomponenData): void
    {
        $syncData = [];
        
        foreach ($produkKomponenData as $item) {
            $produkKomponen = Produk::find($item['id']);
            if ($produkKomponen) {
                $syncData[$item['id']] = [
                    'jumlah' => $item['jumlah'] ?? 1,
                    'harga_snapshot' => $produkKomponen->total_modal_keseluruhan,
                    'harga_updated_at' => now(),
                ];
            }
        }
        
        $this->produkKomponen()->sync($syncData);
        $this->updateTotalModal();
    }

     // Calculate total modal
     public function calculateTotalModal(): float
     {
        // if ($this->jenis_produk === 'rakitan') {
        //     $totalKomponen = $this->produkKomponen->sum(function ($produkKomponen) {
        //         return $produkKomponen->pivot->harga_snapshot * $produkKomponen->pivot->jumlah;
        //     });
            
        //     // Biaya tambahan tetap berlaku
        //     $totalBiayaTambahan = 0;
        //     if (!empty($this->biaya_tambahan_json) && is_array($this->biaya_tambahan_json)) {
        //         foreach ($this->biaya_tambahan_json as $biaya) {
        //             $totalBiayaTambahan += $biaya['nilai'] ?? 0;
        //         }
        //     }
            
        //     return $totalKomponen + $totalBiayaTambahan;
        // }

        $totalKomponen = $this->produkKomponen->sum(function ($produkKomponen) {
            return $produkKomponen->pivot->harga_snapshot * $produkKomponen->pivot->jumlah;
        });
    

        $totalBahan = $this->bahanBakus->sum(function ($bahanBaku) {
            return $bahanBaku->pivot->harga_snapshot * $bahanBaku->pivot->jumlah;
        });

        $totalParam = 0;
        if (!empty($this->parameter_modal_json) && is_array($this->parameter_modal_json)) {
            foreach ($this->parameter_modal_json as $param) {
                $totalParam += ($param['harga'] ?? 0) * ($param['jumlah'] ?? 1);
            }
        }
    
        $totalBiayaTambahan = 0;
        if (!empty($this->biaya_tambahan_json) && is_array($this->biaya_tambahan_json)) {
            foreach ($this->biaya_tambahan_json as $biaya) {
                $totalBiayaTambahan += $biaya['nilai'] ?? 0;
            }
        }
    
        return $totalBahan + $totalParam + $totalBiayaTambahan + $totalKomponen;
     }

    public function updateTotalModal(): void
    {
        $this->refreshParameterModalPrices();
        $this->load('bahanBakus');
        
        $total = $this->calculateTotalModal();
        $this->update(['total_modal_keseluruhan' => $total]);
    }

    // Check if needs price refresh
    // public function needsPriceRefresh(): bool
    // {
    //     return $this->bahanBakus()
    //         ->whereColumn('produk_bahan_baku.harga_updated_at', '<', 'bahan_baku.updated_at')
    //         ->exists();
    // }

    // // Refresh prices from master
    // public function refreshBahanBakuPrices(): array
    // {
    //     $updatedCount = 0;
    //     $totalDifference = 0;
        
    //     foreach ($this->bahanBakus as $bahanBaku) {
    //         $currentPrice = $bahanBaku->harga_terakhir;
    //         $snapshotPrice = $bahanBaku->pivot->harga_snapshot;
            
    //         if ($currentPrice != $snapshotPrice) {
    //             $quantity = $bahanBaku->pivot->jumlah;
    //             $totalDifference += ($currentPrice - $snapshotPrice) * $quantity;
                
    //             $this->bahanBakus()->updateExistingPivot($bahanBaku->id, [
    //                 'harga_snapshot' => $currentPrice,
    //                 'harga_updated_at' => now(),
    //             ]);
                
    //             $updatedCount++;
    //         }
    //     }
        
    //     return [
    //         'updated_count' => $updatedCount,
    //         'total_difference' => $totalDifference,
    //     ];
    // }

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

    /**
     * Get mesin IDs dari parameter_modal_json
     */
    public function getMesinIdsAttribute()
    {
        if (!$this->parameter_modal_json) {
            return [];
        }

        $mesinIds = [];
        foreach ($this->parameter_modal_json as $param) {
            if (isset($param['mesin_id'])) {
                $mesinIds[] = (int) $param['mesin_id'];
            }
        }

        return array_unique($mesinIds);
    }

    /**
     * Refresh harga snapshot di parameter_modal_json dari mesin terkini
     */
    public function refreshParameterModalPrices(): void
    {
        if (empty($this->parameter_modal_json)) {
            return;
        }

        $updatedParams = [];
        
        foreach ($this->parameter_modal_json as $param) {
            $mesinId = $param['mesin_id'] ?? null;
            $namaParameter = $param['nama_parameter'] ?? '';
            
            if (!$mesinId || !$namaParameter) {
                $updatedParams[] = $param;
                continue;
            }

            // Cari mesin dan dapatkan harga terkini
            $mesin = \App\Models\MasterMesin::find($mesinId);
            if (!$mesin || empty($mesin->biaya_perhitungan_profil)) {
                $updatedParams[] = $param;
                continue;
            }

            // Cari profile yang sesuai dengan nama_parameter
            $matchingProfile = null;
            foreach ($mesin->biaya_perhitungan_profil as $profile) {
                if (($profile['nama'] ?? '') === $namaParameter) {
                    $matchingProfile = $profile;
                    break;
                }
            }

            if ($matchingProfile) {
                // Update harga dan total dengan nilai terkini
                $param['harga'] = $matchingProfile['total'] ?? $param['harga'];
                $param['total'] = ($param['harga'] ?? 0) * ($param['jumlah'] ?? 1);
            }

            $updatedParams[] = $param;
        }

        // Update parameter_modal_json dengan harga baru
        $this->update(['parameter_modal_json' => $updatedParams]);
    }

    /**
     * Override total_modal_keseluruhan dengan lazy recalc
     */
    public function getTotalModalKeseluruhanAttribute()
    {
        // Auto recalc jika true
        if ($this->needs_recalc) {
            $this->updateTotalModal();
            $this->update(['needs_recalc' => false]);
        }
        
        return $this->attributes['total_modal_keseluruhan'] ?? 0;
    }

    public function getTagNamesAttribute()
    {
        if (empty($this->tags)) return [];
        
        return DetailParameter::whereIn('id', $this->tags)
            ->pluck('nama_detail_parameter')
            ->toArray();
    }

    public function getTags()
    {
        return DetailParameter::whereIn('id', $this->tags)->get();
    }

    protected static function booted()
    {
        static::updated(function ($produk) {
            if ($produk->wasChanged('total_modal_keseluruhan')) {
                $oldPrice = $produk->getOriginal('total_modal_keseluruhan');
                $newPrice = $produk->total_modal_keseluruhan;
                
                if ($oldPrice === null || $oldPrice === 0) {
                    \Log::info('Skipping cascade update', [
                        'reason' => $produk->wasRecentlyCreated ? 'new_product' : 'null_or_zero_price',
                        'produk_id' => $produk->id,
                    ]);
                    return;
                }
                
                if ($oldPrice != $newPrice) {
                    $affectedRows = DB::table('produk_rakitan')
                        ->where('produk_komponen_id', $produk->id)
                        ->update([
                            'harga_snapshot' => $newPrice,
                            'harga_updated_at' => now(),
                        ]);
                    
                    $affectedProdukRakitanIds = collect();
                    
                    if ($affectedRows > 0) {
                        $affectedProdukRakitanIds = DB::table('produk_rakitan')
                            ->where('produk_komponen_id', $produk->id)
                            ->pluck('produk_rakitan_id');
                        
                        foreach ($affectedProdukRakitanIds as $produkRakitanId) {
                            $produkRakitan = Produk::find($produkRakitanId);
                            if ($produkRakitan && $produkRakitan->jenis_produk === 'rakitan') {
                                $produkRakitan->updateTotalModal();
                            }
                        }
                    }
                    
                    \Log::info("Cascade product price update completed", [
                        'produk' => $produk->nama_produk,
                        'produk_id' => $produk->id,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice,
                        'affected_junction_records' => $affectedRows,
                        'affected_rakitan_products' => $affectedProdukRakitanIds->count(),
                    ]);
                }
            }
        });
    }
}
