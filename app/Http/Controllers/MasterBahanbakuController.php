<?php

namespace App\Http\Controllers;

use App\Exceptions\BahanBakuNotFoundException;
use App\Exceptions\InvalidBahanBakuDataException;
use App\Http\Requests\StoreBahanBakuRequest;
use App\Http\Requests\UpdateBahanBakuRequest;
use App\Models\Pemasok;
use App\Models\MasterParameter;
use App\Services\BahanBakuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MasterBahanbakuController extends Controller
{
    public function __construct(
        private BahanBakuService $bahanBakuService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'kategori' => $request->input('kategori'),
                'sub_kategori' => $request->input('sub_kategori'),
                'status' => $request->input('status'),
            ];

            $bahanbaku = $this->bahanBakuService->getPaginatedBahanBaku(10, $filters);

            // Preserve query parameters
            if ($request->has('search')) {
                $bahanbaku->appends(['search' => $request->search]);
            }
            if ($request->has('kategori')) {
                $bahanbaku->appends(['kategori' => $request->kategori]);
            }
            if ($request->has('status')) {
                $bahanbaku->appends(['status' => $request->status]);
            }
            if ($request->has('sub_kategori')) {
                $bahanbaku->appends(['sub_kategori' => $request->sub_kategori]);
            }

            // Ambil data untuk dropdown
            $pemasok = Pemasok::all();
            $kategoriMaster = MasterParameter::where('nama_parameter', 'KATEGORI BAHAN BAKU')->first();
            $kategoriList = [];
            if ($kategoriMaster) {
                $kategoriList = \App\Models\DetailParameter::where('master_parameter_id', $kategoriMaster->id)
                    ->where('aktif', 1)
                    ->orderBy('nama_detail_parameter')
                    ->get();
            }
            $subKategoriList = \App\Models\SubDetailParameter::with('detailParameter')
                ->where('aktif', 1)
                ->orderBy('nama_sub_detail_parameter')
                ->get();
            $satuanMaster = MasterParameter::where('nama_parameter', 'JENIS SATUAN')->first();
            $satuanList = [];
            if ($satuanMaster) {
                $satuanList = \App\Models\DetailParameter::where('master_parameter_id', $satuanMaster->id)
                    ->where('aktif', 1)
                    ->orderBy('nama_detail_parameter')
                    ->get();
            }
            $subSatuanList = \App\Models\SubDetailParameter::with('detailParameter')
                ->whereHas('detailParameter', function($query) use ($satuanMaster) {
                    $query->where('master_parameter_id', $satuanMaster->id);
                })
                ->where('aktif', 1)
                ->orderBy('nama_sub_detail_parameter')
                ->get();

            return view('backend.master-bahanbaku.index', compact('bahanbaku', 'pemasok', 'kategoriList', 'subKategoriList', 'satuanList', 'subSatuanList'));
        } catch (\Exception $e) {
            Log::error('Error loading Bahan Baku index', ['error' => $e->getMessage()]);
            return view('backend.master-bahanbaku.index', [
                'bahanbaku' => collect()->paginate(),
                'pemasok' => collect(),
                'kategoriList' => collect(),
                'subKategoriList' => collect(),
                'satuanList' => collect()
            ])->with('error', 'Gagal memuat data bahan baku');
        }
    }

    public function store(StoreBahanBakuRequest $request)
    {
        try {
            $data = $request->validated();
            
            $files = [
                'foto_pendukung_new' => $request->file('foto_pendukung_new', []),
                'video_pendukung_new' => $request->file('video_pendukung_new', []),
                'dokumen_pendukung_new' => $request->file('dokumen_pendukung_new', []),
            ];

            $bahanBaku = $this->bahanBakuService->createBahanBaku($data, $files);

            return response()->json([
                'success' => true,
                'message' => 'Data bahan baku berhasil ditambahkan'
            ]);
        } catch (InvalidBahanBakuDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Bahan Baku', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function update(UpdateBahanBakuRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            
            $files = [
                'foto_pendukung_new' => $request->file('foto_pendukung_new', []),
                'video_pendukung_new' => $request->file('video_pendukung_new', []),
                'dokumen_pendukung_new' => $request->file('dokumen_pendukung_new', []),
            ];

            $bahanBaku = $this->bahanBakuService->updateBahanBaku($id, $data, $files);

            return response()->json([
                'success' => true,
                'message' => 'Data bahan baku berhasil diperbarui'
            ]);
        } catch (BahanBakuNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidBahanBakuDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Bahan Baku', [
                'bahan_baku_id' => $id,
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
            $this->bahanBakuService->deleteBahanBaku($id);

            return response()->json([
                'success' => true,
                'message' => 'Data bahan baku berhasil dihapus'
            ]);
        } catch (BahanBakuNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Bahan Baku', [
                'bahan_baku_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data bahan baku'
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $bahanbaku = $this->bahanBakuService->getBahanBaku($id);
            $data = $bahanbaku->toArray();
            $data['pemasok_utama_nama'] = $bahanbaku->pemasokUtama ? $bahanbaku->pemasokUtama->nama : null;
            $data['pemasok_utama_kode'] = $bahanbaku->pemasokUtama ? $bahanbaku->pemasokUtama->kode_pemasok : null;
            return response()->json($data);
        } catch (BahanBakuNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing Bahan Baku', [
                'bahan_baku_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data bahan baku'
            ], 500);
        }
    }

    public function getProdukByBahanBaku(int $id)
    {
        try {
            $bahanBaku = $this->bahanBakuService->getBahanBaku($id);
            $produks = $bahanBaku->produks()
                ->select('produk.id', 'produk.nama_produk', 'produk.kode_produk', 'produk.sub_satuan_id', 'produk.sub_kategori_id','produk_bahan_baku.jumlah')
                ->with(['subSatuan', 'subKategori'])
                ->get();

            $produkList = $produks->map(function ($produk) {
                return [
                    'nama' => $produk->nama_produk,
                    'kode' => $produk->kode_produk,
                    'kategori' => $produk->subKategori ? $produk->subKategori->nama_sub_detail_parameter : '-',
                    'jumlah' => $produk->pivot->jumlah,
                    'satuan' => $produk->subSatuan ? $produk->subSatuan->nama_sub_detail_parameter : '-'
                ];
            });

            return response()->json([
                'success' => true,
                'produk' => $produkList,
                'total' => $produkList->count(),
                'nama_bahan_baku' => $bahanBaku->nama_bahan
            ]);
        } catch (BahanBakuNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting products by bahan baku', [
                'bahan_baku_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk'
            ], 500);
        }
    }

}


