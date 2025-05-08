<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = ['id','nama_depan','nama_belakang','jenis_kelamin','agama','alamat'];

  
    use HasFactory;

}
