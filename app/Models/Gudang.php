<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'gudang';

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'manager',
        'kapasitas',
        'status',
        'deskripsi',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'no_telepon',
        'email',
    ];

    public function rak()
    {
        return $this->hasMany(Rak::class, 'gudang_id');
    }
}
