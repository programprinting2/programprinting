<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterMesin;
use App\Services\CloudinaryService;
use App\Models\MasterParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Cloudinary\Cloudinary;

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
    public function index(Request $request)
    {
        $query = MasterMesin::query();

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                // Bersihkan karakter khusus dan konversi ke lowercase
                $search = strtolower($search);
                $search = preg_replace('/[^a-z0-9\s]/', '', $search);
                
                $query->where(function($q) use ($search) {
                    $q->whereRaw('LOWER(nama_mesin) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(merek) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(model) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(nomor_seri) LIKE ?', ['%' . $search . '%']);
                });
            }
        }

        // Filter berdasarkan tipe mesin
        if ($request->has('type') && $request->type !== 'semua') {
            $type = trim($request->type);
            if (!empty($type)) {
                $query->where('tipe_mesin', $type);
            }
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== 'semua') {
            $status = trim($request->status);
            if (!empty($status) && in_array($status, ['Aktif', 'Maintenance', 'Rusak', 'Tidak Aktif'])) {
                $query->where('status', $status);
            }
        }

        // Ambil data dengan pagination
        $mesin = $query->latest()->paginate(10);

        // Jika request AJAX, kembalikan view partial
        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.master-mesin.partials.card-view', compact('mesin'))->render(),
                'table_html' => view('backend.master-mesin.partials.table-view', compact('mesin'))->render(),
                'pagination' => [
                    'total' => $mesin->total(),
                    'per_page' => $mesin->perPage(),
                    'current_page' => $mesin->currentPage(),
                    'last_page' => $mesin->lastPage(),
                ],
                'total_count' => $mesin->total()
            ]);
        }

        // Ambil data untuk dropdown filter
        $master_tipe_mesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
        $tipe_mesin = $master_tipe_mesin ? $master_tipe_mesin->details()->where('aktif', 1)->get() : collect();
        $param_mode_warna = MasterParameter::where('nama_parameter', 'MODE WARNA')->first();
        $mode_warna_options = $param_mode_warna ? $param_mode_warna->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];

        return view('backend.master-mesin.index', compact('mesin', 'tipe_mesin', 'mode_warna_options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $master_tipe_mesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
        $tipe_mesin = $master_tipe_mesin ? $master_tipe_mesin->details()->where('aktif', 1)->get() : collect();
        $param_mode_warna = MasterParameter::where('nama_parameter', 'MODE WARNA')->first();
        $mode_warna_options = $param_mode_warna ? $param_mode_warna->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];
        return view('backend.master-mesin-form', compact('tipe_mesin', 'mode_warna_options'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Ambil daftar tipe mesin yang valid dari database
        $master_tipe_mesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
        $valid_tipe_mesin = $master_tipe_mesin ? $master_tipe_mesin->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];
        
        $validator = Validator::make($request->all(), [
            'nama_mesin' => 'required|string|max:255',
            'tipe_mesin' => ['required', 'string', 'max:255', function($attribute, $value, $fail) use ($valid_tipe_mesin) {
                if (!in_array($value, $valid_tipe_mesin)) {
                    $fail('Tipe mesin yang dipilih tidak valid.');
                }
            }],
            'merek' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'nomor_seri' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Maintenance,Rusak,Tidak Aktif',
            'tanggal_pembelian' => 'nullable|date',
            'harga_pembelian' => ['nullable', function($attribute, $value, $fail) {
                if ($value !== null) {
                    // Hapus semua titik dan koma dari nilai
                    $cleanValue = str_replace(['.', ','], '', $value);
                    if (!is_numeric($cleanValue)) {
                        $fail('Format harga pembelian tidak valid.');
                    }
                }
            }],
            'deskripsi' => 'nullable|string|max:1000',
            'lebar_media_maksimum' => 'nullable|numeric|min:0',
            'detail_mesin_json' => 'nullable|json',
            'catatan_tambahan' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'harga_tinta_per_liter' => 'nullable|numeric|min:0',
            'konsumsi_tinta_per_m2' => 'nullable|numeric|min:0',
            'biaya_perhitungan_profil_json' => 'nullable|json',
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
            // Bersihkan format harga pembelian sebelum disimpan
            if ($request->harga_pembelian !== null) {
                $mesin->harga_pembelian = str_replace(['.', ','], '', $request->harga_pembelian);
            } else {
                $mesin->harga_pembelian = null;
            }
            // $mesin->lebar_media_maksimum = $request->lebar_media_maksimum;
            $mesin->deskripsi = $request->deskripsi;
            
            // Handling detail_mesin
            if ($request->has('detail_mesin_json')) {
                $detailMesin = json_decode($request->detail_mesin_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $mesin->detail_mesin = $detailMesin;
                }
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
            
            // Handling biaya_perhitungan_profil
            if ($request->has('biaya_perhitungan_profil_json')) {
                $biayaProfil = json_decode($request->biaya_perhitungan_profil_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $mesin->biaya_perhitungan_profil = $biayaProfil;
                }
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
            \Log::error('Error in MesinController@store: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
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
        $master_tipe_mesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
        $tipe_mesin = $master_tipe_mesin ? $master_tipe_mesin->details()->where('aktif', 1)->get() : collect();
        $param_mode_warna = MasterParameter::where('nama_parameter', 'MODE WARNA')->first();
        $mode_warna_options = $param_mode_warna ? $param_mode_warna->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];
        return view('backend.master-mesin-form', compact('mesin', 'tipe_mesin', 'mode_warna_options'));
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
        try {
            // Ambil daftar tipe mesin yang valid dari database
            $master_tipe_mesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
            $valid_tipe_mesin = $master_tipe_mesin ? $master_tipe_mesin->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama_mesin' => 'required|string|max:255',
                'tipe_mesin' => ['required', 'string', 'max:255', function($attribute, $value, $fail) use ($valid_tipe_mesin) {
                    if (!in_array($value, $valid_tipe_mesin)) {
                        $fail('Tipe mesin yang dipilih tidak valid.');
                    }
                }],
                'merek' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'nomor_seri' => 'nullable|string|max:255',
                'status' => 'required|string|in:Aktif,Maintenance,Rusak,Tidak Aktif',
                'tanggal_pembelian' => 'nullable|date',
                'harga_pembelian' => ['nullable', function($attribute, $value, $fail) {
                    if ($value !== null) {
                        // Hapus semua titik dan koma dari nilai
                        $cleanValue = str_replace(['.', ','], '', $value);
                        if (!is_numeric($cleanValue)) {
                            $fail('Format harga pembelian tidak valid.');
                        }
                    }
                }],
                'lebar_media_maksimum' => 'nullable|numeric|min:0',
                'deskripsi' => 'nullable|string',
                'catatan_tambahan' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'detail_mesin_json' => 'required|json',
                'biaya_perhitungan_profil_json' => 'required|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Decode JSON data
            $detailMesin = json_decode($request->detail_mesin_json, true);
            $biayaPerhitunganProfil = json_decode($request->biaya_perhitungan_profil_json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format JSON tidak valid'
                ], 422);
            }

            // Validasi struktur data JSON
            if (!is_array($detailMesin) || !is_array($biayaPerhitunganProfil)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data tidak valid'
                ], 422);
            }

            // Validasi tambahan untuk tipe perhitungan per_waktu
            foreach ($biayaPerhitunganProfil as $profil) {
                if ($profil['tipe'] === 'per_waktu') {
                    if (!isset($profil['settings']['biaya_per_menit']) || $profil['settings']['biaya_per_menit'] <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Biaya per menit harus lebih besar dari 0 untuk tipe perhitungan per waktu'
                        ], 422);
                    }
                }
            }

            // Cari mesin yang akan diupdate
            $mesin = MasterMesin::findOrFail($id);

            // Update data dasar
            $mesin->nama_mesin = $request->nama_mesin;
            $mesin->tipe_mesin = $request->tipe_mesin;
            $mesin->merek = $request->merek;
            $mesin->model = $request->model;
            $mesin->nomor_seri = $request->nomor_seri;
            $mesin->status = $request->status;
            $mesin->tanggal_pembelian = $request->tanggal_pembelian;
            // Bersihkan format harga pembelian sebelum disimpan
            if ($request->harga_pembelian !== null) {
                $mesin->harga_pembelian = str_replace(['.', ','], '', $request->harga_pembelian);
            } else {
                $mesin->harga_pembelian = null;
            }
            $mesin->lebar_media_maksimum = $request->lebar_media_maksimum;
            $mesin->deskripsi = $request->deskripsi;
            $mesin->catatan_tambahan = $request->catatan_tambahan;
            
            // Update detail_mesin dengan data baru
            $mesin->detail_mesin = $detailMesin;
            $mesin->biaya_perhitungan_profil = $biayaPerhitunganProfil;
            
            // Handle gambar jika ada
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($mesin->cloudinary_public_id) {
                    $this->cloudinaryService->delete($mesin->cloudinary_public_id);
                }
                
                // Upload gambar baru
                $publicId = $this->cloudinaryService->upload($request->file('gambar'), 'mesin');
                if ($publicId) {
                    $mesin->cloudinary_public_id = $publicId;
                }
            } elseif ($request->has('hapus_gambar') && $request->hapus_gambar == '1') {
                // Hapus gambar jika checkbox dicentang
                if ($mesin->cloudinary_public_id) {
                    $this->cloudinaryService->delete($mesin->cloudinary_public_id);
                    $mesin->cloudinary_public_id = null;
                }
            }
            
            // Simpan perubahan
            $mesin->save();

            return response()->json([
                'success' => true,
                'message' => 'Data mesin berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating mesin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data mesin: ' . $e->getMessage()
            ], 500);
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
