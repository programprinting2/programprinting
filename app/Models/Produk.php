<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    // Relationship dengan bahan baku
    public function bahanBakus(): BelongsToMany
    {
        return $this->belongsToMany(BahanBaku::class, 'produk_bahan_baku')
            ->withPivot(['jumlah', 'harga_snapshot', 'harga_updated_at'])
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

     // Calculate total modal
     public function calculateTotalModal(): float
     {
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
    
        return $totalBahan + $totalParam + $totalBiayaTambahan;
     }

    public function updateTotalModal(): void
    {
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
}
