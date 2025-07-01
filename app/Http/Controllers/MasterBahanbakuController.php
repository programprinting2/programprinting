<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Pemasok;
use App\Models\MasterParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Storage;

class MasterBahanbakuController extends Controller
{
    public function index(Request $request)
    {
        $query = BahanBaku::query();

        // Eager load relasi pemasokUtama dan subKategoriDetail
        $query->with(['pemasokUtama', 'subKategoriDetail', 'kategoriDetail']);

        // Pencarian berdasarkan nama, kode, atau kategori
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_bahan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_bahan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('kategoriDetail', function($subQuery) use ($search) {
                      $subQuery->whereRaw('LOWER(nama_detail_parameter) LIKE ?', ['%' . $search . '%']);
                  })
                  ->orWhereHas('subKategoriDetail', function($subQuery) use ($search) {
                      $subQuery->whereRaw('LOWER(nama_sub_detail_parameter) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter berdasarkan sub-kategori (FK)
        if ($request->filled('sub_kategori')) {
            $query->where('sub_kategori_id', $request->sub_kategori);
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
        if ($request->has('sub_kategori')) {
            $bahanbaku->appends(['sub_kategori' => $request->sub_kategori]);
        }

        // Ambil data pemasok
        $pemasok = Pemasok::all();

        // Ambil master parameter kategori bahan baku
        $kategoriMaster = \App\Models\MasterParameter::where('nama_parameter', 'KATEGORI BAHAN BAKU')->first();
        $kategoriList = [];
        if ($kategoriMaster) {
            $kategoriList = \App\Models\DetailParameter::where('master_parameter_id', $kategoriMaster->id)
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
        $satuanMaster = \App\Models\MasterParameter::where('nama_parameter', 'SATUAN')->first();
        $satuanList = [];
        if ($satuanMaster) {
            $satuanList = \App\Models\DetailParameter::where('master_parameter_id', $satuanMaster->id)
                ->where('aktif', 1)
                ->orderBy('nama_detail_parameter')
                ->get();
        }

        return view('backend.master-bahanbaku.index', compact('bahanbaku', 'pemasok', 'kategoriList', 'subKategoriList', 'satuanList'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'kategori_id' => 'required|exists:detail_parameters,id',
            'sub_kategori_id' => 'required|exists:sub_detail_parameter,id',
            'satuan_utama_id' => 'required|exists:detail_parameters,id',
            'status_aktif' => 'required|in:0,1',
            'konversi_satuan_json' => 'nullable|string',
            'pemasok_utama_id' => 'nullable|exists:pemasok,id',
            'harga_terakhir' => 'nullable|numeric|min:0',
            'stok_saat_ini' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'stok_maksimum' => 'nullable|integer|min:0',
            'detail_spesifikasi_json' => 'nullable|json',
            'foto_pendukung_new.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:5048',
            'video_pendukung_new.*' => 'nullable|file|mimes:mp4,avi,mpeg,quicktime|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:20480',
            'dokumen_pendukung_new.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,csv,jpg,jpeg,png,gif|max:10240',
            'keterangan' => 'nullable|string',
            'link_pendukung_json' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi manual ekstra untuk keamanan file upload
        if ($request->hasFile('foto_pendukung_new')) {
            foreach ($request->file('foto_pendukung_new') as $file) {
                if (!in_array($file->extension(), ['jpeg','jpg','png','gif'])) {
                    return response()->json(['success'=>false,'message'=>'Format foto tidak diizinkan.'], 422);
                }
                if (strpos($file->getMimeType(), 'image/') !== 0) {
                    return response()->json(['success'=>false,'message'=>'File foto harus berupa gambar.'], 422);
                }
            }
        }
        if ($request->hasFile('video_pendukung_new')) {
            foreach ($request->file('video_pendukung_new') as $file) {
                if (!in_array($file->extension(), ['mp4','avi','mpeg','quicktime'])) {
                    return response()->json(['success'=>false,'message'=>'Format video tidak diizinkan.'], 422);
                }
                if (strpos($file->getMimeType(), 'video/') !== 0) {
                    return response()->json(['success'=>false,'message'=>'File video harus berupa video.'], 422);
                }
            }
        }
        if ($request->hasFile('dokumen_pendukung_new')) {
            foreach ($request->file('dokumen_pendukung_new') as $file) {
                if (!in_array($file->extension(), ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','txt','csv','jpg','jpeg','png','gif'])) {
                    return response()->json(['success'=>false,'message'=>'Format dokumen tidak diizinkan.'], 422);
                }
            }
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
                        // Validasi dan filter data
                        $konversiData = array_map(function($item) {
                            return [
                                'satuan_dari' => $item['satuan_dari'],
                                'jumlah' => $item['jumlah']
                            ];
                        }, $konversiData);
                        $data['konversi_satuan_json'] = $konversiData;
                    } else {
                        throw new \Exception('Format konversi satuan tidak valid');
                    }
                } else {
                    $data['konversi_satuan_json'] = [];
                }

                // Spesifikasi teknis (JSON)
                if ($request->has('detail_spesifikasi_json')) {
                    $spesifikasiData = json_decode($request->input('detail_spesifikasi_json'), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data['detail_spesifikasi_json'] = $spesifikasiData;
                    } else {
                        throw new \Exception('Format spesifikasi teknis tidak valid');
                    }
                } else {
                    $data['detail_spesifikasi_json'] = [];
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

                // Link pendukung
                if ($request->has('link_pendukung_json')) {
                    $linkData = json_decode($request->input('link_pendukung_json'), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Migrasi data lama (array string) ke array objek
                        $linkData = array_map(function($item) {
                            if (is_string($item)) return ['url' => $item, 'keterangan' => ''];
                            // Jika sudah objek, pastikan key url dan keterangan ada
                            return [
                                'url' => $item['url'] ?? '',
                                'keterangan' => $item['keterangan'] ?? ''
                            ];
                        }, $linkData);
                        // Validasi setiap item
                        foreach ($linkData as $link) {
                            if (!isset($link['url'])) {
                                throw new \Exception('Setiap link pendukung harus memiliki url.');
                            }
                        }
                        $data['link_pendukung_json'] = $linkData;
                    } else {
                        throw new \Exception('Format link pendukung tidak valid');
                    }
                } else {
                    $data['link_pendukung_json'] = [];
                }

                // --- Penanganan Unggah Foto Pendukung ke Storage --- 
                $fotoPendukungPaths = [];
                if ($request->hasFile('foto_pendukung_new')) {
                    foreach ($request->file('foto_pendukung_new') as $file) {
                        $path = $file->store('bahan_baku/foto', 'public');
                        if ($path) {
                            $fotoPendukungPaths[] = '/storage/' . $path;
                        }
                    }
                }
                $data['foto_pendukung_json'] = $fotoPendukungPaths;

                // --- Penanganan Unggah Video Pendukung ke Storage --- 
                $videoPendukungPaths = [];
                if ($request->hasFile('video_pendukung_new')) {
                    foreach ($request->file('video_pendukung_new') as $file) {
                        $path = $file->store('bahan_baku/video', 'public');
                        if ($path) {
                            $videoPendukungPaths[] = '/storage/' . $path;
                        }
                    }
                }
                $data['video_pendukung_json'] = $videoPendukungPaths;

                // --- Penanganan Unggah Dokumen Pendukung ke Storage --- 
                $dokumenPendukungPaths = [];
                if ($request->hasFile('dokumen_pendukung_new')) {
                    foreach ($request->file('dokumen_pendukung_new') as $file) {
                        $path = $file->store('bahan_baku/dokumen', 'public');
                        if ($path) {
                            $dokumenPendukungPaths[] = [
                                'nama' => $file->getClientOriginalName(),
                                'path' => '/storage/' . $path,
                                'ukuran' => $file->getSize(),
                                'tipe' => $file->getClientMimeType(),
                            ];
                        }
                    }
                }
                $data['dokumen_pendukung_json'] = $dokumenPendukungPaths;

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
            'kategori_id' => 'required|exists:detail_parameters,id',
            'sub_kategori_id' => 'required|exists:sub_detail_parameter,id',
            'satuan_utama_id' => 'required|exists:detail_parameters,id',
            'status_aktif' => 'required|in:0,1',
            'konversi_satuan_json' => 'nullable|string',
            'pemasok_utama_id' => 'nullable|exists:pemasok,id',
            'harga_terakhir' => 'nullable|numeric|min:0',
            'stok_saat_ini' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'stok_maksimum' => 'nullable|integer|min:0',
            'detail_spesifikasi_json' => 'nullable|json',
            'foto_pendukung_new.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:5048',
            'video_pendukung_new.*' => 'nullable|file|mimes:mp4,avi,mpeg,quicktime|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:20480',
            'foto_pendukung_existing_json' => 'nullable|string',
            'video_pendukung_existing_json' => 'nullable|string',
            'dokumen_pendukung_json' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'link_pendukung_json' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        // Konversi nilai numerik (sama seperti store method)
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
                    $konversiData = array_map(function($item) {
                        return [
                            'satuan_dari' => $item['satuan_dari'],
                            'jumlah' => $item['jumlah']
                        ];
                    }, $konversiData);
                    $data['konversi_satuan_json'] = $konversiData;
                } else {
                    throw new \Exception('Format konversi satuan tidak valid');
                }
            } else {
                $data['konversi_satuan_json'] = [];
            }

            // Spesifikasi teknis (JSON)
            if ($request->has('detail_spesifikasi_json')) {
                $spesifikasiData = json_decode($request->input('detail_spesifikasi_json'), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['detail_spesifikasi_json'] = $spesifikasiData;
                } else {
                    throw new \Exception('Format spesifikasi teknis tidak valid');
                }
            } else {
                $data['detail_spesifikasi_json'] = [];
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

            // Link pendukung
            if ($request->has('link_pendukung_json')) {
                $linkData = json_decode($request->input('link_pendukung_json'), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Migrasi data lama (array string) ke array objek
                    $linkData = array_map(function($item) {
                        if (is_string($item)) return ['url' => $item, 'keterangan' => ''];
                        // Jika sudah objek, pastikan key url dan keterangan ada
                        return [
                            'url' => $item['url'] ?? '',
                            'keterangan' => $item['keterangan'] ?? ''
                        ];
                    }, $linkData);
                    // Validasi setiap item
                    foreach ($linkData as $link) {
                        if (!isset($link['url'])) {
                            throw new \Exception('Setiap link pendukung harus memiliki url.');
                        }
                    }
                    $data['link_pendukung_json'] = $linkData;
                } else {
                    throw new \Exception('Format link pendukung tidak valid');
                }
            } else {
                $data['link_pendukung_json'] = [];
            }

            // --- Penanganan Foto & Video Pendukung (Update) ---
            // Foto
            $fotoLama = $bahanbaku->foto_pendukung_json ?: [];
            $fotoDipertahankan = $request->has('foto_pendukung_existing_json') ? json_decode($request->input('foto_pendukung_existing_json'), true) : [];
            $fotoBaru = [];
            if ($request->hasFile('foto_pendukung_new')) {
                foreach ($request->file('foto_pendukung_new') as $file) {
                    $path = $file->store('bahan_baku/foto', 'public');
                    if ($path) {
                        $fotoBaru[] = '/storage/' . $path;
                    }
                }
            }
            // Hapus file fisik yang dihapus user
            $fotoTerhapus = array_diff($fotoLama, $fotoDipertahankan);
            foreach ($fotoTerhapus as $path) {
                $storagePath = str_replace('/storage/', '', $path);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
            }
            $data['foto_pendukung_json'] = array_values(array_merge($fotoDipertahankan, $fotoBaru));

            // Video
            $videoLama = $bahanbaku->video_pendukung_json ?: [];
            $videoDipertahankan = $request->has('video_pendukung_existing_json') ? json_decode($request->input('video_pendukung_existing_json'), true) : [];
            $videoBaru = [];
            if ($request->hasFile('video_pendukung_new')) {
                foreach ($request->file('video_pendukung_new') as $file) {
                    $path = $file->store('bahan_baku/video', 'public');
                    if ($path) {
                        $videoBaru[] = '/storage/' . $path;
                    }
                }
            }
            // Hapus file fisik yang dihapus user
            $videoTerhapus = array_diff($videoLama, $videoDipertahankan);
            foreach ($videoTerhapus as $path) {
                $storagePath = str_replace('/storage/', '', $path);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
            }
            $data['video_pendukung_json'] = array_values(array_merge($videoDipertahankan, $videoBaru));

            // --- Penanganan Dokumen Pendukung (Update) ---
            $dokumenLama = $bahanbaku->dokumen_pendukung_json ?: [];
            $dokumenDipertahankan = $data['dokumen_pendukung_json']; // dari dokumen_pendukung_json (array lama yang dipertahankan)
            $dokumenDipertahankan = is_array($dokumenDipertahankan) ? $dokumenDipertahankan : [];
            $dokumenBaru = [];
            // Unggah dokumen baru
            if ($request->hasFile('dokumen_pendukung_new')) {
                foreach ($request->file('dokumen_pendukung_new') as $file) {
                    $path = $file->store('bahan_baku/dokumen', 'public');
                    if ($path) {
                        $dokumenBaru[] = [
                            'nama' => $file->getClientOriginalName(),
                            'path' => '/storage/' . $path,
                            'ukuran' => $file->getSize(),
                            'tipe' => $file->getClientMimeType(),
                        ];
                    }
                }
            }
            // Hapus file fisik dokumen yang dihapus user
            $dokumenLamaPaths = array_map(function($doc) { return $doc['path'] ?? null; }, $dokumenLama);
            $dokumenDipertahankanPaths = array_map(function($doc) { return $doc['path'] ?? null; }, $dokumenDipertahankan);
            $dokumenTerhapus = array_diff($dokumenLamaPaths, $dokumenDipertahankanPaths);
            foreach ($dokumenTerhapus as $path) {
                if ($path) {
                    $storagePath = str_replace('/storage/', '', $path);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
                }
            }
            $data['dokumen_pendukung_json'] = array_values(array_merge($dokumenDipertahankan, $dokumenBaru));

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

            // Hapus semua foto pendukung dari storage
            if ($bahanbaku->foto_pendukung_json && is_array($bahanbaku->foto_pendukung_json)) {
                foreach ($bahanbaku->foto_pendukung_json as $path) {
                    $storagePath = str_replace('/storage/', '', $path);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
                }
            }

            // Hapus semua video pendukung dari storage
            if ($bahanbaku->video_pendukung_json && is_array($bahanbaku->video_pendukung_json)) {
                foreach ($bahanbaku->video_pendukung_json as $path) {
                    $storagePath = str_replace('/storage/', '', $path);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
                }
            }

            // Hapus semua dokumen pendukung dari storage
            if ($bahanbaku->dokumen_pendukung_json && is_array($bahanbaku->dokumen_pendukung_json)) {
                foreach ($bahanbaku->dokumen_pendukung_json as $doc) {
                    if (isset($doc['path'])) {
                        $storagePath = str_replace('/storage/', '', $doc['path']);
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
                    }
                }
            }
            
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


