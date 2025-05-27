<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggan';

    protected $fillable = [
        'kode_pelanggan',
        'nama',
        'no_telp',
        'handphone',
        'email',
        'website',
        'no_whatsapp',
        'alamat_utama',
        'alamat',
        'kategori_harga',
        'kategori_diskon',
        'syarat_pembayaran',
        'default_penjual',
        'default_diskon',
        'default_deskripsi',
        'kirim_barang',
        'nik',
        'npwp',
        'wajib_pajak',
        'nitku',
        'kode_negara',
        'tipe_transaksi',
        'detail_transaksi',
        'default_total_faktur_pajak',
        'kontak',
        'piutang_awal',
        'data_lain',
        'status'
    ];

    protected $casts = [
        'alamat' => 'array',
        'kontak' => 'array',
        'piutang_awal' => 'array',
        'data_lain' => 'array',
        'status' => 'boolean',
        'kirim_barang' => 'boolean',
        'default_total_faktur_pajak' => 'boolean',
        'default_diskon' => 'float',
        'batas_total_piutang' => 'float',
        'alamat_utama' => 'integer',
        'batas_umur_faktur' => 'integer',
        'wajib_pajak' => 'boolean'
    ];
}