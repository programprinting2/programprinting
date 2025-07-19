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
use App\Models\Produk;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

// use App\Models\MasterParameter;
// use Illuminate\Support\Facades\Validator;


class MasterProdukController extends Controller
{

     public function index(Request $request)
    {
        $query = Produk::with(['kategoriUtama', 'subKategori', 'satuan']);

        // Search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_produk) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_produk) LIKE ?', ['%' . $search . '%']);
            });
        }
        // Filter status
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }
        // Pagination
        $produk = $query->orderBy('created_at', 'desc')->paginate(10);
        // Ambil data kategori, subkategori, satuan
        $kategoriProduk = MasterParameter::where('nama_parameter', 'KATEGORI PRODUK')->first();
        $kategoriProdukList = [];
        if ($kategoriProduk) {
            $kategoriProdukList = DetailParameter::where('master_parameter_id', $kategoriProduk->id)
                ->where('aktif', 1)
                ->orderBy('nama_detail_parameter')
                ->get();
        }
        $subKategoriList = SubDetailParameter::with('detailParameter')
            ->where('aktif', 1)
            ->orderBy('nama_sub_detail_parameter')
            ->get();
        $satuanMaster = MasterParameter::where('nama_parameter', 'SATUAN')->first();
        $satuanList = [];
        if ($satuanMaster) {
            $satuanList = DetailParameter::where('master_parameter_id', $satuanMaster->id)
                ->where('aktif', 1)
                ->orderBy('nama_detail_parameter')
                ->get();
        }
        return view('backend.master-produk.index', compact('produk', 'kategoriProdukList', 'subKategoriList', 'satuanList'));
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'kategori_utama_id' => 'required|exists:detail_parameters,id',
            'sub_kategori_id' => 'nullable|exists:sub_detail_parameter,id',
            'satuan_id' => 'required|exists:detail_parameters,id',
            'metode_penjualan' => 'required|in:m2,meter_lari',
            'lebar' => 'nullable|integer|min:0',
            'panjang' => 'nullable|integer|min:0',
            'status_aktif' => 'required|boolean',
            'bahan_baku_json' => 'nullable|json',
            'harga_bertingkat_json' => 'nullable|json',
            'harga_reseller_json' => 'nullable|json',
            'foto_pendukung_json' => 'nullable|json',
            'video_pendukung_json' => 'nullable|json',
            'dokumen_pendukung_json' => 'nullable|json',
            'alur_produksi_json' => 'nullable|json',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();
        $data['kode_produk'] = IdGenerator::generate([
            'table' => 'produk',
            'field' => 'kode_produk',
            'length' => 8,
            'prefix' => 'PRD-'
        ]);
        // Proses upload file media & dokumen
        $fotoPaths = [];
        if ($request->hasFile('foto_pendukung_new')) {
            foreach ($request->file('foto_pendukung_new') as $file) {
                $path = $file->store('produk/foto', 'public');
                if ($path) $fotoPaths[] = '/storage/' . $path;
            }
        }
        $videoPaths = [];
        if ($request->hasFile('video_pendukung_new')) {
            foreach ($request->file('video_pendukung_new') as $file) {
                $path = $file->store('produk/video', 'public');
                if ($path) $videoPaths[] = '/storage/' . $path;
            }
        }
        $dokumenPaths = [];
        if ($request->hasFile('dokumen_pendukung_new')) {
            foreach ($request->file('dokumen_pendukung_new') as $file) {
                $path = $file->store('produk/dokumen', 'public');
                if ($path) $dokumenPaths[] = [
                    'nama' => $file->getClientOriginalName(),
                    'url' => '/storage/' . $path,
                    'jenis' => $file->getClientMimeType(),
                    'ukuran' => $file->getSize()
                ];
            }
        }
        $data['foto_pendukung_json'] = $fotoPaths;
        $data['video_pendukung_json'] = $videoPaths;
        $data['dokumen_pendukung_json'] = $dokumenPaths;
        // Konversi JSON string ke array jika perlu
        foreach ([
            'bahan_baku_json', 'harga_bertingkat_json', 'harga_reseller_json',
            'alur_produksi_json'
        ] as $jsonField) {
            if (isset($data[$jsonField]) && is_string($data[$jsonField])) {
                $data[$jsonField] = json_decode($data[$jsonField], true);
            }
        }
        $produk = Produk::create($data);
        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan', 'produk' => $produk]);
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return response()->json(['success' => true, 'produk' => $produk]);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'kategori_utama_id' => 'required|exists:detail_parameters,id',
            'sub_kategori_id' => 'nullable|exists:sub_detail_parameter,id',
            'satuan_id' => 'required|exists:detail_parameters,id',
            'metode_penjualan' => 'required|in:m2,meter_lari',
            'lebar' => 'nullable|integer|min:0',
            'panjang' => 'nullable|integer|min:0',
            'status_aktif' => 'required|boolean',
            'bahan_baku_json' => 'nullable|json',
            'harga_bertingkat_json' => 'nullable|json',
            'harga_reseller_json' => 'nullable|json',
            'alur_produksi_json' => 'nullable|json',
            // Media & dokumen
            'foto_pendukung_existing_json' => 'nullable|string',
            'video_pendukung_existing_json' => 'nullable|string',
            'dokumen_pendukung_json' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();
        // --- Handle Foto ---
        $fotoLama = $produk->foto_pendukung_json ?: [];
        $fotoDipertahankan = $request->has('foto_pendukung_existing_json') ? json_decode($request->input('foto_pendukung_existing_json'), true) : [];
        $fotoBaru = [];
        if ($request->hasFile('foto_pendukung_new')) {
            foreach ($request->file('foto_pendukung_new') as $file) {
                $path = $file->store('produk/foto', 'public');
                if ($path) $fotoBaru[] = '/storage/' . $path;
            }
        }
        // Hapus file fisik yang dihapus user
        $fotoTerhapus = array_diff($fotoLama, $fotoDipertahankan);
        foreach ($fotoTerhapus as $path) {
            $storagePath = str_replace('/storage/', '', $path);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
        }
        $data['foto_pendukung_json'] = array_values(array_merge($fotoDipertahankan, $fotoBaru));
        // --- Handle Video ---
        $videoLama = $produk->video_pendukung_json ?: [];
        $videoDipertahankan = $request->has('video_pendukung_existing_json') ? json_decode($request->input('video_pendukung_existing_json'), true) : [];
        $videoBaru = [];
        if ($request->hasFile('video_pendukung_new')) {
            foreach ($request->file('video_pendukung_new') as $file) {
                $path = $file->store('produk/video', 'public');
                if ($path) $videoBaru[] = '/storage/' . $path;
            }
        }
        $videoTerhapus = array_diff($videoLama, $videoDipertahankan);
        foreach ($videoTerhapus as $path) {
            $storagePath = str_replace('/storage/', '', $path);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
        }
        $data['video_pendukung_json'] = array_values(array_merge($videoDipertahankan, $videoBaru));
        // --- Handle Dokumen ---
        $dokumenLama = $produk->dokumen_pendukung_json ?: [];
        $dokumenDipertahankan = $request->has('dokumen_pendukung_json') ? json_decode($request->input('dokumen_pendukung_json'), true) : [];
        $dokumenBaru = [];
        if ($request->hasFile('dokumen_pendukung_new')) {
            foreach ($request->file('dokumen_pendukung_new') as $file) {
                $path = $file->store('produk/dokumen', 'public');
                if ($path) {
                    $dokumenBaru[] = [
                        'nama' => $file->getClientOriginalName(),
                        'url' => '/storage/' . $path,
                        'jenis' => $file->getClientMimeType(),
                        'ukuran' => $file->getSize()
                    ];
                }
            }
        }
        // Hapus file dokumen yang dihapus user
        $dokumenLamaPaths = array_map(function($doc) { return $doc['url'] ?? null; }, $dokumenLama);
        $dokumenDipertahankanPaths = array_map(function($doc) { return $doc['url'] ?? null; }, $dokumenDipertahankan);
        $dokumenTerhapus = array_diff($dokumenLamaPaths, $dokumenDipertahankanPaths);
        foreach ($dokumenTerhapus as $path) {
            if ($path) {
                $storagePath = str_replace('/storage/', '', $path);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
            }
        }
        $data['dokumen_pendukung_json'] = array_values(array_merge($dokumenDipertahankan, $dokumenBaru));
        // --- Handle JSON lain ---
        foreach ([
            'bahan_baku_json', 'harga_bertingkat_json', 'harga_reseller_json',
            'alur_produksi_json'
        ] as $jsonField) {
            if (isset($data[$jsonField]) && is_string($data[$jsonField])) {
                $data[$jsonField] = json_decode($data[$jsonField], true);
            }
        }
        unset($data['kode_produk']);
        $produk->update($data);
        return response()->json(['success' => true, 'message' => 'Produk berhasil diupdate', 'produk' => $produk]);
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();
        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
    }

    // (Opsional) Show detail produk
    public function show($id)
    {
        $produk = Produk::findOrFail($id);
        return response()->json(['success' => true, 'produk' => $produk]);
    }
}



