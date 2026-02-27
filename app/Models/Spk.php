<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SPK extends Model
{
    use HasFactory;

    protected $table = 'spk';

    protected $fillable = [
        'nomor_spk',
        'tanggal_spk',
        'pelanggan_id',
        'status',
        'catatan',
        'total_biaya',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_spk' => 'date',
            'total_biaya' => 'decimal:2',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SPKItem::class, 'spk_id');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    // public static function statusList(): array
    // {
    //     return [
    //         'draft'       => 'Draft',
    //         'proses_bayar'      => 'Proses Pembayaran',
    //         'proses_produksi'  => 'Proses Produksi',
    //         'sudah_cetak'      => 'Sudah Cetak',
    //         'siap_antar'       => 'Siap Antar',
    //     ];
    // }

    public function updateTotalBiaya(): void
    {
        $total = $this->items()->sum('total_biaya');
        $this->update(['total_biaya' => $total]);
    }

    // public function scopeByStatus($query, string $status)
    // {
    //     return $query->where('status', $status);
    // }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(SpkPembayaran::class, 'spk_id');
    }

    public static function pembayaranStatusList(): array
    {
        return [
            'belum_bayar' => 'Belum Bayar',
            'kurang_bayar' => 'Kurang Bayar',
            'lunas' => 'Lunas',
        ];
    }

    public function refreshPembayaranSummary(): void
    {
        $totalDibayar = (float) $this->pembayaran()->sum('jumlah');
        $totalBiaya = (float) ($this->total_biaya ?? 0);

        $statusPembayaran = 'belum_bayar';
        if ($totalDibayar <= 0) {
            $statusPembayaran = 'belum_bayar';
        } elseif ($totalDibayar + 0.00001 < $totalBiaya) {
            $statusPembayaran = 'kurang_bayar';
        } else {
            $statusPembayaran = 'lunas';
        }

        $this->update([
            'total_dibayar' => $totalDibayar,
            'status_pembayaran' => $statusPembayaran,
        ]);
    }

    public function getSisaPembayaranAttribute(): float
    {
        $totalBiaya = (float) ($this->total_biaya ?? 0);
        $totalDibayar = (float) ($this->total_dibayar ?? 0);

        return max(0.0, $totalBiaya - $totalDibayar);
    }
}