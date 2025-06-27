<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianItem extends Model
{
    use HasFactory;
    protected $table = 'pembelian_item';
    protected $fillable = [
        'pembelian_id', 'material_id', 'jumlah', 'harga', 'diskon_persen', 'subtotal'
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'material_id');
    }
} 