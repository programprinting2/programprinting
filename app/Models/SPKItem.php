<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SPKItem extends Model
{
    use HasFactory;

    protected $table = 'spk_items';

    protected $fillable = [
        'spk_id',
        'produk_id',
        'nama_produk',
        'jumlah',
        'satuan',
        'keterangan',
        'lebar',
        'panjang',
        'deadline',
        'is_urgent',
        'biaya_produk',
        'biaya_finishing',
        'tipe_finishing',
        'tugas_produksi',
        'file_pendukung',
        'total_biaya',
    ];

    protected function casts(): array
    {
        return [
            'lebar' => 'decimal:2',
            'panjang' => 'decimal:2',
            'deadline' => 'datetime',
            'is_urgent' => 'boolean',
            'biaya_produk' => 'decimal:2',
            'biaya_finishing' => 'decimal:2',
            'total_biaya' => 'decimal:2',
            'tipe_finishing' => 'array',
            'tugas_produksi' => 'array',
            'file_pendukung' => 'array',
        ];
    }

    public function spk(): BelongsTo
    {
        return $this->belongsTo(SPK::class, 'spk_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // public function bahan(): BelongsTo
    // {
    //     return $this->belongsTo(BahanBaku::class, 'bahan_id');
    // }

    public function updateTotalBiaya(): void
    {
        $biayaProduk = (float) ($this->biaya_produk ?? 0);

        $biayaFinishing = (float) ($this->biaya_finishing ?? 0);
        if (!empty($this->tipe_finishing) && is_array($this->tipe_finishing)) {
            $dariTipeFinishing = collect($this->tipe_finishing)->sum(fn ($f) => (float) ($f['total'] ?? 0));
            if ($dariTipeFinishing > 0) {
                $biayaFinishing = $dariTipeFinishing;
            }
        }

        $biayaTugas = collect($this->tugas_produksi ?? [])->sum(fn ($t) => (int) ($t['biaya'] ?? $t['harga'] ?? 0));

        $totalItem = $biayaProduk + $biayaFinishing + $biayaTugas;

        $this->update([
            'biaya_finishing' => round($biayaFinishing, 2),
            'total_biaya' => round($totalItem, 2),
        ]);

        $this->spk->refresh()->updateTotalBiaya();
    }
}