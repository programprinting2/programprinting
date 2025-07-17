<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $table = 'rak';

    protected $fillable = [
        'gudang_id',
        'kode_rak',
        'nama_rak',
        'kapasitas',
        'jumlah_level',
        'status',
        'lebar',
        'tinggi',
        'kedalaman',
        'deskripsi',
    ];

    // Relasi ke Gudang
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }
} 