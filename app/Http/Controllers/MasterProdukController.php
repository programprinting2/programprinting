<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailParameter; // Pastikan model ini sesuai dengan tabel kategori utama

class MasterProdukController extends Controller
{

     public function index(Request $request)
    {
        return view('backend.master-produk.index');
    }

    public function create()
    {
        dd('agus');
        // // Ambil hanya field nama_detail_parameter dari tabel detail_parameters
        // $kategoriUtama = DetailParameter::where('nama_parameter', 'Kategori Utama')
        //     ->pluck('nama_detail_parameter');

        // // Kirim data ke view modal_form.blade.php
        // return view('backend.master-produk.modal_form', compact('kategoriUtama'));
    }

    // ...method lain jika diperlukan...
}
