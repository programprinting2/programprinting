<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterParameter;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;
use App\Models\DetailParameter; // Pastikan model ini sesuai dengan tabel kategori utama
// use App\Models\MasterParameter;
// use Illuminate\Support\Facades\Validator;


class MasterProdukController extends Controller
{

     public function index(Request $request)
    {
        //$kategoriUtama='agus';
        $kategoriUtama = MasterParameter::with('details')
                                                ->where('nama_parameter', 'like', 'SUB KATEGORI%')
                                                ->get()
                                                ->keyBy('nama_parameter');
        
        return view('backend.master-produk.index');
    }

    public function create()
    {
        // $kategoriUtama='agus';
        // dd($kategoriUtama);
        // // Ambil hanya field nama_detail_parameter dari tabel detail_parameters
        // $kategoriUtama = DetailParameter::where('nama_parameter', 'Kategori Utama')
        //     ->pluck('nama_detail_parameter');

        // // Kirim data ke view modal_form.blade.php
        // return view('backend.master-produk.modal_form', compact('kategoriUtama'));
        return view('backend.master-produk.modal_form');
    }

    // ...method lain jika diperlukan...
}
