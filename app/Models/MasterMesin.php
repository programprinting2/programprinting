<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMesin extends Model
{
    protected $table = 'master_mesin'; // Specify the table name
    protected $primaryKey = 'id'; // Specify the primary key if it's not 'id'
    protected $fillable = [
        'id',
        'kode_mesin',
        'aktif',
        'alias',
        'jenis_mesin',
        'keterangan',
        'created_at',
        'nama_mesin',
        'non_produksi', 
        'tanggal_beli', // Add the status column        
        'update_at',
        'gambar'
    ];

    

    use HasFactory;

}