<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkItemCetakQueue extends Model
{
    protected $table = 'spk_item_cetak_queue';

    protected $fillable = ['spk_item_id', 'mesin_id', 'user_id', 'jumlah'];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function spkItem(): BelongsTo
    {
        return $this->belongsTo(SPKItem::class, 'spk_item_id');
    }

    public function mesin(): BelongsTo
    {
        return $this->belongsTo(MasterMesin::class, 'mesin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}