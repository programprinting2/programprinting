<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian';
    protected $fillable = [
        'kode_pembelian', 'tanggal_pembelian', 'pemasok_id', 'nomor_form', 'jatuh_tempo', 'catatan',
        'diskon_persen', 'biaya_pengiriman', 'tarif_pajak', 'nota_kredit', 'biaya_lain', 'total', 'is_lunas'
    ];
    protected $casts = [
        'diskon_persen' => 'decimal:2',
        'biaya_pengiriman' => 'integer',
        'tarif_pajak' => 'decimal:2',
        'nota_kredit' => 'integer',
        'biaya_lain' => 'integer',
        'total' => 'decimal:2', 
        'is_lunas' => 'boolean',
    ];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_id');
    }

    public function items()
    {
        return $this->hasMany(PembelianItem::class, 'pembelian_id');
    }
} 