<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkPembayaran extends Model
{
    protected $table = 'spk_pembayaran';

    protected $fillable = [
        'spk_id',
        'jumlah',
        'metode',
        'tanggal',
        'referensi',
        'catatan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }

    public function spk(): BelongsTo
    {
        return $this->belongsTo(SPK::class, 'spk_id');
    }
}