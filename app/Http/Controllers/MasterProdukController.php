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
        // Ambil master parameter kategori produk
        $kategoriProduk = \App\Models\MasterParameter::where('nama_parameter', 'KATEGORI PRODUK')->first();
        $kategoriProdukList = [];
        if ($kategoriProduk) {
            $kategoriProdukList = \App\Models\DetailParameter::where('master_parameter_id', $kategoriProduk->id)
                ->where('aktif', 1)
                ->orderBy('nama_detail_parameter')
                ->get();
        }

        // Ambil semua sub kategori (sub detail parameter) yang aktif
        $subKategoriList = \App\Models\SubDetailParameter::with('detailParameter')
            ->where('aktif', 1)
            ->orderBy('nama_sub_detail_parameter')
            ->get();

          // Ambil master parameter satuan
        $satuanMaster = MasterParameter::where('nama_parameter', 'SATUAN')->first();
        $satuanList = [];
        if ($satuanMaster) {
            $satuanList = DetailParameter::where('master_parameter_id', $satuanMaster->id)
                ->where('aktif', 1)
                ->orderBy('nama_detail_parameter')
                ->get();
        }
    //    dd($satuanList);

        // TODO: Ambil data produk dan pagination jika sudah ada modelnya
        // $produk = Produk::orderBy('created_at', 'desc')->paginate(10);

        return view('backend.master-produk.index', compact('kategoriProdukList', 'subKategoriList', 'satuanList'));
    }

    public function create()
    {
        // // Ambil master parameter kategori produk
        // $kategoriProduk = \App\Models\MasterParameter::where('nama_parameter', 'KATEGORI PRODUK')->first();
        // $kategoriProdukList = [];
        // if ($kategoriProduk) {
        //     $kategoriProdukList = \App\Models\DetailParameter::where('master_parameter_id', $kategoriProduk->id)
        //         ->where('aktif', 1)
        //         ->orderBy('nama_detail_parameter')
        //         ->get();
        // }

        // // Ambil semua sub kategori (sub detail parameter) yang aktif
        // $subKategoriList = \App\Models\SubDetailParameter::with('detailParameter')
        //     ->where('aktif', 1)
        //     ->orderBy('nama_sub_detail_parameter')
        //     ->get();

        return view('backend.master-produk.modal_form', compact('kategoriProdukList', 'subKategoriList'));
    }

    // ...method lain jika diperlukan...
}
