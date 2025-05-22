<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterMesin;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MesinController extends Controller
{
    protected $cloudinaryService;
    
    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mesin = MasterMesin::latest()->paginate(10);
        return view('backend.master-mesin.index', compact('mesin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.master-mesin-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_mesin' => 'required|string|max:255',
            'tipe_mesin' => 'required|in:Printer Large Format,Digital Printer A3+,Mesin Finishing',
            'merek' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'nomor_seri' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Maintenance,Rusak,Tidak Aktif',
            'tanggal_pembelian' => 'nullable|date',
            'harga_pembelian' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'lebar_media_maksimum' => 'nullable|numeric|min:0',
            'detail_mesin_json' => 'nullable',
            'catatan_tambahan' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'harga_tinta_per_liter' => 'nullable|numeric|min:0',
            'konsumsi_tinta_per_m2' => 'nullable|numeric|min:0',
            'biaya_tambahan_json' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $mesin = new MasterMesin();
            $mesin->nama_mesin = $request->nama_mesin;
            $mesin->tipe_mesin = $request->tipe_mesin;
            $mesin->merek = $request->merek;
            $mesin->model = $request->model;
            $mesin->nomor_seri = $request->nomor_seri;
            $mesin->status = $request->status;
            $mesin->tanggal_pembelian = $request->tanggal_pembelian;
            $mesin->harga_pembelian = $request->harga_pembelian;
            $mesin->deskripsi = $request->deskripsi;
            $mesin->lebar_media_maksimum = $request->lebar_media_maksimum;
            
            // Handling detail_mesin
            if ($request->has('detail_mesin_json')) {
                $mesin->detail_mesin = json_decode($request->detail_mesin_json, true);
            }
            
            $mesin->catatan_tambahan = $request->catatan_tambahan;
            
            // Upload gambar jika ada
            if ($request->hasFile('gambar')) {
                // Upload ke Cloudinary dan simpan public_id
                $publicId = $this->cloudinaryService->upload($request->file('gambar'), 'mesin');
                if ($publicId) {
                    $mesin->cloudinary_public_id = $publicId;
                }
            }
            
            $mesin->harga_tinta_per_liter = $request->harga_tinta_per_liter;
            $mesin->konsumsi_tinta_per_m2 = $request->konsumsi_tinta_per_m2;
            
            // Handling biaya_tambahan
            if ($request->has('biaya_tambahan_json')) {
                $mesin->biaya_tambahan = json_decode($request->biaya_tambahan_json, true);
            }
            
            $mesin->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesin berhasil ditambahkan'
                ]);
            }

            return redirect()
                ->route('backend.master-mesin.index')
                ->with('success', 'Mesin berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mesin = MasterMesin::findOrFail($id);
        return view('backend.master-mesin-detail', compact('mesin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mesin = MasterMesin::findOrFail($id);
        return view('backend.master-mesin-form', compact('mesin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_mesin' => 'required|string|max:255',
            'tipe_mesin' => 'required|in:Printer Large Format,Digital Printer A3+,Mesin Finishing',
            'merek' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'nomor_seri' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Maintenance,Rusak,Tidak Aktif',
            'tanggal_pembelian' => 'nullable|date',
            'harga_pembelian' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'lebar_media_maksimum' => 'nullable|numeric|min:0',
            'detail_mesin_json' => 'nullable',
            'catatan_tambahan' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'harga_tinta_per_liter' => 'nullable|numeric|min:0',
            'konsumsi_tinta_per_m2' => 'nullable|numeric|min:0',
            'biaya_tambahan_json' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $mesin = MasterMesin::findOrFail($id);
            $mesin->nama_mesin = $request->nama_mesin;
            $mesin->tipe_mesin = $request->tipe_mesin;
            $mesin->merek = $request->merek;
            $mesin->model = $request->model;
            $mesin->nomor_seri = $request->nomor_seri;
            $mesin->status = $request->status;
            $mesin->tanggal_pembelian = $request->tanggal_pembelian;
            $mesin->harga_pembelian = $request->harga_pembelian;
            $mesin->deskripsi = $request->deskripsi;
            $mesin->lebar_media_maksimum = $request->lebar_media_maksimum;
            
            // Handling detail_mesin
            if ($request->has('detail_mesin_json')) {
                $mesin->detail_mesin = json_decode($request->detail_mesin_json, true);
            }
            
            $mesin->catatan_tambahan = $request->catatan_tambahan;
            
            // Handling gambar
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama dari Cloudinary jika ada
                if ($mesin->cloudinary_public_id) {
                    $this->cloudinaryService->delete($mesin->cloudinary_public_id);
                }
                
                // Upload ke Cloudinary dan simpan public_id
                $publicId = $this->cloudinaryService->upload($request->file('gambar'), 'mesin');
                if ($publicId) {
                    $mesin->cloudinary_public_id = $publicId;
                }
            } elseif ($request->has('hapus_gambar') && $request->hapus_gambar == 1) {
                // Hapus gambar dari Cloudinary jika ada
                if ($mesin->cloudinary_public_id) {
                    $this->cloudinaryService->delete($mesin->cloudinary_public_id);
                    $mesin->cloudinary_public_id = null;
                }
            }
            
            $mesin->harga_tinta_per_liter = $request->harga_tinta_per_liter;
            $mesin->konsumsi_tinta_per_m2 = $request->konsumsi_tinta_per_m2;
            
            // Handling biaya_tambahan
            if ($request->has('biaya_tambahan_json')) {
                $mesin->biaya_tambahan = json_decode($request->biaya_tambahan_json, true);
            }
            
            $mesin->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesin berhasil diperbarui'
                ]);
            }

            return redirect()
                ->route('backend.master-mesin.index')
                ->with('success', 'Mesin berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $mesin = MasterMesin::findOrFail($id);
            
            // Hapus gambar dari Cloudinary jika ada
            if ($mesin->cloudinary_public_id) {
                $this->cloudinaryService->delete($mesin->cloudinary_public_id);
            }
            
            $mesin->delete();

            return redirect()->route('backend.master-mesin.index')
                ->with('success', 'Mesin berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
