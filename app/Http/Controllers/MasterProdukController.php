<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterParameter;
use App\Models\SubDetailParameter;
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
        $masterKategoriUtama = MasterParameter::with('details')
                                                ->where('nama_parameter', 'KATEGORI PRODUK')->first();
        
        $kategoriUtama = $masterKategoriUtama ? 
            $masterKategoriUtama->details()->where('aktif', 1)->get()->pluck('nama_detail_parameter') : [];


        $masterSubKategori = SubDetailParameter::with('detailParameter')
                                                ->where('detail_parameter_id', '12')->first();
        // dd($masterSubKategori);                                                

        // $subKategori = $masterSubKategori ? 
        //  $masterSubKategori->detailParameter()->where('aktif', 1)->get()->pluck('nama_sub_detail_parameter') : [];


      //   dd($subKategori);            

        // Ambil data sub-kategori dari MasterParameter dan DetailParameter
        // $subKategoriParameters = MasterParameter::with('details')
        //                                         ->where('nama_parameter', 'like', 'SUB KATEGORI%')
        //                                         ->get()
        //                                         ->keyBy('nama_parameter');
        // dd($subKategoriParameters);
    


                                                // $masterSubKategori = SubDetailParameter::with('details')
                                                // ->where('detail_parameter_id', $masterKategoriUtama->id)->first();

        return view('backend.master-produk.index',compact('kategoriUtama'));
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
