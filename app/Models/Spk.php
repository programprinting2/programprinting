<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SPK extends Model
{
    use HasFactory;

    protected $table = 'spk';
    
    protected $fillable = [
        'nomor_spk',
        'tanggal_spk',
        'customer_id',
        'status',
        'prioritas',
        'catatan',
        'total_biaya',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_spk' => 'date',
        'total_biaya' => 'integer'
    ];

    // Relasi ke Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'customer_id');
    }

    // Relasi ke User (created_by)
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke User (updated_by)
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi ke SPK Items
    public function items(): HasMany
    {
        return $this->hasMany(SPKItem::class);
    }

    // Method untuk generate nomor SPK otomatis
    public static function generateNomorSPK(): string
    {
        $today = now();
        $prefix = 'SPK' . $today->format('my');
        
        $lastSPK = self::where('nomor_spk', 'like', $prefix . '-%')
            ->orderBy('nomor_spk', 'desc')
            ->first();
        
        if ($lastSPK) {
            $lastNumber = (int) substr($lastSPK->nomor_spk, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Method untuk update total biaya
    public function updateTotalBiaya(): void
    {
        $total = $this->items()->sum('total_biaya');
        $this->update(['total_biaya' => $total]);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan prioritas
    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }
}
