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

    public static function statusList(): array
    {
        return [
            'verifikasi'       => 'Verifikasi',
            'sudah_bayar'      => 'Sudah Bayar',
            'proses_produksi'  => 'Proses Produksi',
            'sudah_cetak'      => 'Sudah Cetak',
            'siap_antar'       => 'Siap Antar',
        ];
    }

    public function updateTotalBiaya(): void
    {
        $total = $this->items()->sum('total_biaya');
        $this->update(['total_biaya' => $total]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}