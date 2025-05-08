<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupHarga extends Model
{
    protected $table = 'group_harga'; // Specify the table name
    protected $primaryKey = 'id'; // Specify the primary key if it's not 'id'
    protected $fillable = ['id','created_at','updated_at','kodegroupharga','jenis','keterangan','aktif','default_group','gambar']; // Add relevant columns

  
    use HasFactory;

}