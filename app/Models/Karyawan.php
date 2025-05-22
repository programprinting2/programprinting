<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_karyawan',
        'nama_lengkap',
        'posisi',
        'departemen',
        'tanggal_masuk',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_pernikahan',
        'nomor_telepon',
        'email',
        'gaji_pokok',
        'status',
        'alamat',
        'rekening',
        'alamat_utama',
        'rekening_utama',
        'npwp',
        'status_pajak',
        'tarif_pajak',
        'estimasi_hari_kerja',
        'jam_kerja_per_hari',
        'komponen_gaji'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_lahir' => 'date',
        'alamat' => 'array',
        'rekening' => 'array',
        'komponen_gaji' => 'array',
        'gaji_pokok' => 'integer',
        'tarif_pajak' => 'integer',
        'estimasi_hari_kerja' => 'integer',
        'jam_kerja_per_hari' => 'integer',
    ];

    // Scope untuk karyawan aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    // Scope untuk karyawan tidak aktif
    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'Tidak Aktif');
    }
} 