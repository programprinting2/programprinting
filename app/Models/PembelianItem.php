<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianItem extends Model
{
    use HasFactory;
    protected $table = 'item_pembelian';
    protected $fillable = [
        'pembelian_id', 'bahanbaku_id', 'jumlah', 'satuan', 'harga', 'diskon_persen', 'subtotal'
    ];
    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'subtotal' => 'decimal:2', 
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahanbaku_id');
    }
} 