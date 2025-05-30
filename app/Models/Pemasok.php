<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasok extends Model
{
    use HasFactory;

    protected $table = 'pemasok';

    protected $fillable = [
        'kode_pemasok',
        'nama',
        'no_telp',
        'handphone',
        'email',
        'website',
        'kategori',
        'alamat_utama',
        'alamat',
        'syarat_pembayaran',
        'deskripsi_pembelian',
        'default_diskon',
        'akun_utang',
        'akun_uang_muka',
        'nik',
        'npwp',
        'wajib_pajak',
        'rekening_utama',
        'rekening',
        'utang_awal',
        'status'
    ];

    protected $casts = [
        'alamat' => 'array',
        'utang_awal' => 'array',
        'rekening' => 'array',
        'default_diskon' => 'float',
        'alamat_utama' => 'integer',
        'rekening_utama' => 'integer',
        'wajib_pajak' => 'boolean',
        'status' => 'boolean',
    ];

    // Mutator untuk wajib_pajak
    public function setWajibPajakAttribute($value)
    {
        $this->attributes['wajib_pajak'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
