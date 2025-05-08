<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKontak extends Model
{
    use HasFactory;

    protected $table = 'contacts'; // Specify the correct table name
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key
    protected $fillable = [
        'id',
        'tipe',       // ENUM('staff','customer','supplier')
        'nama',      // VARCHAR(100)
        'HP',         // VARCHAR(20)
        'alamat',     // TEXT
        'catatan',    // TEXT
        'created_at', // Timestamps
        'updated_at', // Timestamps
    ];
}
