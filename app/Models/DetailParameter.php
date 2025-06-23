<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailParameter extends Model
{
    use HasFactory;
    protected $table = 'detail_parameters';
    protected $fillable = [
        'id',
        'aktif',
        'created_at',
        'updated_at',
        // 'icon',
        // 'isi_parameter',
        'keterangan',
        'master_parameter_id',
        'nama_detail_parameter',
        'warna',
    ];

    public function subDetailParameters()
    {
        return $this->hasMany(SubDetailParameter::class, 'detail_parameter_id');
    }
}
