<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Pemasok;
use App\Models\MasterParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class MasterBahanbakuController extends Controller
{
    public function index(Request $request)
    {
        $query = BahanBaku::query();

        // Eager load relasi pemasokUtama
        $query->with('pemasokUtama');

        // Pencarian berdasarkan nama, kode, atau kategori
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_bahan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_bahan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kategori) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(sub_kategori) LIKE ?', ['%' . $search . '%']);
            });
    }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        // Pagination dengan 10 item per halaman
        $bahanbaku = $query->orderBy('created_at', 'desc')->paginate(10);

        // Tambahkan parameter pencarian ke pagination
        if ($request->has('search')) {
            $bahanbaku->appends(['search' => $request->search]);
        }
        if ($request->has('kategori')) {
            $bahanbaku->appends(['kategori' => $request->kategori]);
        }
        if ($request->has('status')) {
            $bahanbaku->appends(['status' => $request->status]);
        }

        // Ambil data pemasok
        $pemasok = Pemasok::all();

        // Ambil data sub-kategori dari MasterParameter dan DetailParameter
        $subKategoriParameters = MasterParameter::with('details')
                                                ->where('nama_parameter', 'like', 'SUB KATEGORI%')
                                                ->get()
                                                ->keyBy('nama_parameter');

        return view('backend.master-bahanbaku.index', compact('bahanbaku', 'pemasok', 'subKategoriParameters'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'status_aktif' => 'required|in:0,1',
            'satuan_utama' => 'required|string|max:50',
            'pilihan_warna' => 'nullable|string|max:50',
            'nama_warna_custom' => 'nullable|string|max:100',
            'berat' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'tebal' => 'nullable|numeric|min:0',
            'gramasi_densitas' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'konversi_satuan_json' => 'nullable|string',
            'pemasok_utama_id' => 'nullable|exists:pemasok,id',
            'harga_terakhir' => 'nullable|numeric|min:0',
            'histori_harga_json' => 'nullable|string',
            'stok_saat_ini' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'stok_maksimum' => 'nullable|integer|min:0',
            'foto_produk_url' => 'nullable|string|max:255',
            'dokumen_pendukung_json' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate kode bahan otomatis
            $kode_bahan = IdGenerator::generate([
                'table' => 'bahan_baku',
                'field' => 'kode_bahan',
                'length' => 8,
                'prefix' => 'MAT-'
            ]);

            $data = $request->all();
            $data['kode_bahan'] = $kode_bahan;
            
            // Konversi nilai numerik
            $data['harga_terakhir'] = $request->input('harga_terakhir') ? (int) $request->input('harga_terakhir') : null;
            $data['stok_saat_ini'] = $request->input('stok_saat_ini') ? (int) $request->input('stok_saat_ini') : 0;
            $data['stok_minimum'] = $request->input('stok_minimum') ? (int) $request->input('stok_minimum') : 0;
            $data['stok_maksimum'] = $request->input('stok_maksimum') ? (int) $request->input('stok_maksimum') : 0;
            
            // Konversi status_aktif ke boolean
            $data['status_aktif'] = (bool) $request->input('status_aktif');
            
            // Memastikan data JSON valid
            try {
                // Konversi satuan
                if ($request->has('konversi_satuan_json')) {
                    $konversiData = json_decode($request->input('konversi_satuan_json'), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Konversi format lama ke format baru jika diperlukan
                        $konversiData = array_map(function($item) {
                            if (isset($item['from_value'])) {
                                return [
                                    'dari' => $item['from_value'],
                                    'satuan_dari' => $item['from_unit'],
                                    'ke' => $item['to_value'],
                                    'satuan_ke' => $item['to_unit']
                                ];
                            }
                            return $item;
                        }, $konversiData);
                        $data['konversi_satuan_json'] = $konversiData;
                    } else {
                        throw new \Exception('Format konversi satuan tidak valid');
                    }
                } else {
                    $data['konversi_satuan_json'] = [];
                }

                // Histori harga
                if ($request->has('histori_harga_json')) {
                    $historiData = json_decode($request->input('histori_harga_json'), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data['histori_harga_json'] = $historiData;
                    } else {
                        throw new \Exception('Format histori harga tidak valid');
                    }
                } else {
                    $data['histori_harga_json'] = [];
                }

                // Dokumen pendukung
                if ($request->has('dokumen_pendukung_json')) {
                    $dokumenData = json_decode($request->input('dokumen_pendukung_json'), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data['dokumen_pendukung_json'] = $dokumenData;
                    } else {
                        throw new \Exception('Format dokumen pendukung tidak valid');
                    }
                } else {
                    $data['dokumen_pendukung_json'] = [];
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data JSON tidak valid: ' . $e->getMessage()
                ], 422);
            }

            $bahanBaku = BahanBaku::create($data);

            return response()->json([
                'success' => true, 
                'message' => 'Data bahan baku berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menambahkan data bahan baku: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $bahanbaku = BahanBaku::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'status_aktif' => 'required|boolean',
            'satuan_utama' => 'required|string|max:50',
            'pilihan_warna' => 'nullable|string|max:50',
            'nama_warna_custom' => 'nullable|string|max:100',
            'berat' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'tebal' => 'nullable|numeric|min:0',
            'gramasi_densitas' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'konversi_satuan_json' => 'nullable|json',
            'pemasok_utama_id' => 'nullable|exists:pemasok,id',
            'harga_terakhir' => 'nullable|numeric|min:0',
            'histori_harga_json' => 'nullable|json',
            'stok_saat_ini' => 'nullable|numeric|min:0',
            'stok_minimum' => 'nullable|numeric|min:0',
            'stok_maksimum' => 'nullable|numeric|min:0',
            'foto_produk_url' => 'nullable|string|max:255',
            'dokumen_pendukung_json' => 'nullable|json',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        // Memastikan data JSON valid
        try {
            // Konversi satuan
            if ($request->has('konversi_satuan_json')) {
                $konversiData = json_decode($request->input('konversi_satuan_json'), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Konversi format lama ke format baru jika diperlukan
                    $konversiData = array_map(function($item) {
                        if (isset($item['from_value'])) {
                            return [
                                'dari' => $item['from_value'],
                                'satuan_dari' => $item['from_unit'],
                                'ke' => $item['to_value'],
                                'satuan_ke' => $item['to_unit']
                            ];
                        }
                        return $item;
                    }, $konversiData);
                    $data['konversi_satuan_json'] = $konversiData;
                } else {
                    throw new \Exception('Format konversi satuan tidak valid');
                }
            }

            // Histori harga
            if ($request->has('histori_harga_json')) {
                $historiData = json_decode($request->input('histori_harga_json'), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['histori_harga_json'] = $historiData;
                } else {
                    throw new \Exception('Format histori harga tidak valid');
                }
            }

            // Dokumen pendukung
            if ($request->has('dokumen_pendukung_json')) {
                $dokumenData = json_decode($request->input('dokumen_pendukung_json'), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['dokumen_pendukung_json'] = $dokumenData;
                } else {
                    throw new \Exception('Format dokumen pendukung tidak valid');
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format data JSON tidak valid: ' . $e->getMessage()
            ], 422);
        }

        try {
            $bahanbaku->update($data);
            return response()->json(['success' => true, 'message' => 'Data bahan baku berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data bahan baku: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bahanbaku = BahanBaku::findOrFail($id);
            $bahanbaku->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data bahan baku berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data bahan baku: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $bahanbaku = BahanBaku::findOrFail($id);
        return response()->json($bahanbaku);
    }
}
