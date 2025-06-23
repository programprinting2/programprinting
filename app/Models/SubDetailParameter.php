<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDetailParameter extends Model
{
    use HasFactory;

    protected $table = 'sub_detail_parameter';
    
    protected $fillable = [
        'detail_parameter_id',
        'nama_sub_detail_parameter',
        'keterangan',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function detailParameter()
    {
        return $this->belongsTo(DetailParameter::class, 'detail_parameter_id');
    }
}
