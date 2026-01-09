<?php

namespace App\Http\Controllers;

use App\Exceptions\ProdukNotFoundException;
use App\Exceptions\InvalidProdukDataException;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\MasterParameter;
use App\Models\SubDetailParameter;
use App\Models\MasterMesin;
use App\Services\ProdukService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

// use App\Models\MasterParameter;
// use Illuminate\Support\Facades\Validator;


class MasterProdukController extends Controller
{
    public function __construct(
        private ProdukService $produkService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ];

            $produk = $this->produkService->getPaginatedProduk(10, $filters);

            // Ambil data kategori, subkategori, satuan
            $kategoriProdukList = \App\Services\ParameterService::getParameterDetails('KATEGORI PRODUK');
            $subKategoriList = SubDetailParameter::with('detailParameter')
                ->where('aktif', 1)
                ->orderBy('nama_sub_detail_parameter')
                ->get();
            $satuanList = \App\Services\ParameterService::getParameterDetails('SATUAN');
            // Ambil data master mesin untuk window.masterMesinList
            $masterMesinList = MasterMesin::select('id', 'nama_mesin', 'tipe_mesin', 'biaya_perhitungan_profil')->get();
            
            return view('backend.master-produk.index', compact('produk', 'kategoriProdukList', 'subKategoriList', 'satuanList', 'masterMesinList'));
        } catch (\Exception $e) {
            Log::error('Error loading Produk index', ['error' => $e->getMessage()]);
            return view('backend.master-produk.index', [
                'produk' => collect()->paginate(),
                'kategoriProdukList' => collect(),
                'subKategoriList' => collect(),
                'satuanList' => collect()
            ])->with('error', 'Gagal memuat data produk');
        }
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

    public function store(StoreProdukRequest $request)
    {
        try {
            $data = $request->validated();
            
            $files = [
                'foto_pendukung_new' => $request->file('foto_pendukung_new', []),
                'video_pendukung_new' => $request->file('video_pendukung_new', []),
                'dokumen_pendukung_new' => $request->file('dokumen_pendukung_new', []),
            ];

            $produk = $this->produkService->createProduk($data, $files);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'produk' => $produk
            ]);
        } catch (InvalidProdukDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Produk', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function edit(int $id)
    {
        try {
            $produk = $this->produkService->getProduk($id);
            return response()->json(['success' => true, 'produk' => $produk]);
        } catch (ProdukNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error loading Produk edit', [
                'produk_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data produk'
            ], 500);
        }
    }

    public function update(UpdateProdukRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            
            $files = [
                'foto_pendukung_new' => $request->file('foto_pendukung_new', []),
                'video_pendukung_new' => $request->file('video_pendukung_new', []),
                'dokumen_pendukung_new' => $request->file('dokumen_pendukung_new', []),
            ];

            $produk = $this->produkService->updateProduk($id, $data, $files);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'produk' => $produk
            ]);
        } catch (ProdukNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidProdukDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Produk', [
                'produk_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->produkService->deleteProduk($id);
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (ProdukNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Produk', [
                'produk_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk'
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $produk = $this->produkService->getProduk($id);
            return response()->json([
                'success' => true,
                'produk' => $produk
            ]);
        } catch (ProdukNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing Produk', [
                'produk_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data produk'
            ], 500);
        }
    }
}



