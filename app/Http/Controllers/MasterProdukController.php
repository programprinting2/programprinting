<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CariController;
use App\Models\MasterParameter;
use App\Models\SubDetailParameter;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;
use App\Models\DetailParameter; // Pastikan model ini sesuai dengan tabel kategori utama
use App\Models\BahanBaku;

// use App\Models\MasterParameter;
// use Illuminate\Support\Facades\Validator;


class MasterProdukController extends Controller
{

     public function index(Request $request)
    {
        // Ambil master parameter kategori produk
        $kategoriProduk = MasterParameter::where('nama_parameter', 'KATEGORI PRODUK')->first();
        $kategoriProdukList = [];
        if ($kategoriProduk) {
            $kategoriProdukList = DetailParameter::where('master_parameter_id', $kategoriProduk->id)
                ->where('aktif', 1)
                ->orderBy('nama_detail_parameter')
                ->get();
        }

        // Ambil semua sub kategori (sub detail parameter) yang aktif
        $subKategoriList = SubDetailParameter::with('detailParameter')
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

        // $bahanBaku = BahanBaku::all();
        //  dd($bahanBaku);

    //    dd($satuanList);

        // TODO: Ambil data produk dan pagination jika sudah ada modelnya
        // $produk = Produk::orderBy('created_at', 'desc')->paginate(10);

        return view('backend.master-produk.index', compact('kategoriProdukList', 'subKategoriList', 'satuanList'));
        // return view('backend.master-produk.index', compact('kategoriProdukList', 'subKategoriList', 'satuanList', 'bahanBaku'));
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

    
    // public function CariBahanBaku(Request $request)
    // {
    //     // tampilakan semua data bahan BahanBaku
    //     $bahanBaku = BahanBaku::all();
    //      dd($bahanBaku);
    //     // return response()->json($bahanBaku);
    //     // $searchTerm = $request->input('searchBahanBaku');

    //     // $bahanBaku = BahanBaku::query()
    //     //     ->when($searchTerm, function ($query, $searchTerm) {
    //     //         $query->where('kode_bahan', 'like', "%{$searchTerm}%")
    //     //               ->orWhere('nama_bahan', 'like', "%{$searchTerm}%");
    //     //     })
    //     //     ->get(); 

    // return view('backend.master-produk.index', compact('bahanBaku'));

    // }
}
