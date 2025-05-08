<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterParameter extends Model
{
    use HasFactory;

    protected $table = 'parameter'; // Specify the correct table name
    protected $primaryKey = 'id'; // Specify the primary key if it's not 'id'
    protected $fillable = [
        'id',        
        'nama_parameter',
        'keterangan',
        'icon',        
        'aktif',
        'created_at',
        'updated_at',
    ];

    public function details()
    {
        return $this->hasMany(DetailParameter::class, 'master_parameter_id');
    }
}



