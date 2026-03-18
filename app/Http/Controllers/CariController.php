<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Pemasok;
use App\Models\Produk; 
use App\Models\SubDetailParameter;
use App\Models\MasterParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CariController extends Controller
{
    public function cariBahanBaku(Request $request)
    {
        $search = $request->input('searchBahanBaku');
        $query = BahanBaku::query();

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_bahan) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(nama_bahan) LIKE ?', ["%{$search}%"]);
            });
        }

        // Eager load relasi kategori, sub-kategori, satuan utama
        $query->with(['kategoriDetail', 'subKategoriDetail', 'satuanUtamaDetail', 'subSatuanDetail', 'warnaDetail']);

        $bahanBakus = $query->orderBy('nama_bahan')->paginate(10);

        // Ubah response agar menampilkan nama kategori, sub-kategori, satuan utama
        $bahanBakus->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode_bahan' => $item->kode_bahan,
                'nama_bahan' => $item->nama_bahan,
                'kategori' => $item->kategoriDetail ? $item->kategoriDetail->nama_detail_parameter : '-',
                'sub_kategori' => $item->subKategoriDetail ? $item->subKategoriDetail->nama_sub_detail_parameter : '-',
                'jenis_satuan' => $item->satuanUtamaDetail ? $item->satuanUtamaDetail->nama_detail_parameter : '-',
                'sub_satuan' => $item->subSatuanDetail ? $item->subSatuanDetail->nama_sub_detail_parameter : '-',
                'harga_terakhir' => $item->harga_terakhir ?? 0,
                'konversi_satuan' => $item->konversi_satuan_json ?? [],
                'warna_detail' => $item->warnaDetail ? [
                    'id' => $item->warnaDetail->id,
                    'nama_detail_parameter' => $item->warnaDetail->nama_detail_parameter,
                    'keterangan' => $item->warnaDetail->keterangan
                ] : null,
                'is_metric' => $item->is_metric,
                'panjang' => $item->panjang,
                'lebar' => $item->lebar,
            ];
        });

        return response()->json($bahanBakus);
    }

    public function cariPemasok(Request $request)
    {
        $search = $request->input('search');
        $query = Pemasok::query();
        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(kode_pemasok) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(handphone) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }
        $pemasok = $query->orderBy('nama')->paginate(10);
        $pemasok->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_pemasok,
                'nama' => $item->nama,
                'handphone' => $item->handphone,
                'email' => $item->email,
                'status' => $item->status,
            ];
        });
        return response()->json($pemasok);
    }
    

    public function cariMesin(Request $request)
    {
        $search = $request->input('search');
        $query = \App\Models\MasterMesin::query();

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(merek) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(nama_mesin) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(tipe_mesin) LIKE ?', ["%{$search}%"]);
            });
        }

        $mesins = $query->orderBy('nama_mesin')->paginate(10);

        $mesins->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'nama_mesin' => $item->nama_mesin ?? null,
                'tipe_mesin' => $item->tipe_mesin ?? null,
                'merek' => $item->merek ?? null,
                'status' => $item->status ?? null,
                'biaya_perhitungan_profil' => $item->biaya_perhitungan_profil ?? null,
            ];
        });

        return response()->json($mesins);
    }
    public function cariDivisiMesin(Request $request)
    {
        try {
            $masterTipeMesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
            $divisiMesin = $masterTipeMesin ? $masterTipeMesin->details()->where('aktif', 1)->get() : collect();

            $perPage = 10;
            $page = $request->get('page', 1);
            $search = $request->get('search', '');
            
            $query = $divisiMesin;
            
            if ($search) {
                $query = $query->filter(function($item) use ($search) {
                    return stripos($item->nama_detail_parameter, $search) !== false;
                });
            }
            
            $total = $query->count();
            $paginated = $query->forPage($page, $perPage);
            
            return response()->json([
                'data' => $paginated->values(),
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data divisi mesin'
            ], 500);
        }
    }

    public function cariPelanggan(Request $request)
    {
        $search = $request->input('search');
        $query = \App\Models\Pelanggan::query();

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(kode_pelanggan) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(handphone) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $pelanggan = $query->orderBy('nama')->paginate(10);

        $pelanggan->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_pelanggan,
                'nama' => $item->nama,
                'handphone' => $item->handphone,
                'email' => $item->email,
                'status' => $item->status,
                'kategori_harga' => $item->kategori_harga ?? 'Umum',
            ];
        });

        return response()->json($pelanggan);
    }

    public function cariKaryawan(Request $request)
    {
        $search = $request->input('search');
        $query = \App\Models\Karyawan::query();

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(id_karyawan) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(nomor_telepon) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $karyawan = $query->orderBy('nama_lengkap')->paginate(10);

        $karyawan->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->id_karyawan,
                'nama' => $item->nama_lengkap,
                'handphone' => $item->nomor_telepon,
                'email' => $item->email,
                'status' => $item->status,
            ];
        });

        return response()->json($karyawan);
    }

    public function cariParameter(Request $request)
    {
        $search = $request->input('searchParameter');
        $query = DetailParameter::query();

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_detail_parameter) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(keterangan) LIKE ?', ["%{$search}%"]);
            });
        }

        // Eager load relasi master parameter
        $query->with(['masterParameter']);

        $parameters = $query->orderBy('nama_detail_parameter')->paginate(10);

        // Transform response
        $parameters->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_detail_parameter,
                'keterangan' => $item->keterangan,
                'aktif' => $item->aktif,
                'master_parameter' => $item->masterParameter ? $item->masterParameter->nama_parameter : '-',
                'isi_parameter' => $item->isi_parameter,
            ];
        });

        return response()->json($parameters);
    }

    public function cariFinishing(Request $request)
    {
        $finishingCategory = MasterParameter::where('nama_parameter', 'KATEGORI PRODUK')->first();
        $finishingDetail = $finishingCategory ? 
            $finishingCategory->details()->where('nama_detail_parameter', 'FINISHING')->first() : null;
        
        if (!$finishingDetail) {
            return response()->json(['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 0]);
        }
        
        $query = SubDetailParameter::where('detail_parameter_id', $finishingDetail->id)
                                ->where('aktif', 1);
        
        if ($request->search) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_sub_detail_parameter) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(keterangan) LIKE ?', ["%{$search}%"]);
            });
        }
        
        $subDetails = $query->orderBy('nama_sub_detail_parameter')
                            ->paginate(10);
        
        $subDetails->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_sub_detail_parameter,
                'keterangan' => $item->keterangan,
                'detail_parameter' => $item->detailParameter->nama_detail_parameter ?? '-',
            ];
        });
        
        return response()->json($subDetails);
    }

    public function cariProdukKomponen(Request $request)
    {
        $search = $request->input('search');
        $query = Produk::where('jenis_produk', '!=', 'rakitan')
                    ->where('status_aktif', true);
        
                   

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_produk) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(nama_produk) LIKE ?', ["%{$search}%"]);
            });
        }

        // $query->with(['kategoriUtama', 'subKategori', 'satuan', 'subSatuan']);

        $produks = $query->orderBy('nama_produk')->paginate(10);

        $produks->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode_produk' => $item->kode_produk,
                'nama_produk' => $item->nama_produk,
                'jenis_produk' => $item->jenis_produk,
                'total_modal_keseluruhan' => $item->total_modal_keseluruhan ?? 0,
            ];
        });

        return response()->json($produks);
    }

    public function cariProdukFinishing(Request $request)
    {
        $produkId = $request->input('produk_id');
        $showAll = $request->boolean('show_all', false); 
        
        if (!$showAll) {
            if (!$produkId) {
                return response()->json(['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 0]);
            }
            
            $produkUtama = Produk::find($produkId);
            
            if (!$produkUtama || !is_array($produkUtama->finishing_json) || count($produkUtama->finishing_json) === 0) {
                return response()->json(['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 0]);
            }
            
            $subKategoriIds = collect($produkUtama->finishing_json)->pluck('id')->filter()->all();
            
            if (empty($subKategoriIds)) {
                return response()->json(['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 0]);
            }
            
            // Filter berdasarkan sub_kategori_id
            $query = Produk::whereIn('sub_kategori_id', $subKategoriIds)
                        ->where('status_aktif', true)
                        ->with(['kategoriUtama', 'subSatuan']);
        } else {
            $finishingCategory = MasterParameter::where('nama_parameter', 'KATEGORI PRODUK')->first();
            $finishingDetail = $finishingCategory ? 
                $finishingCategory->details()->where('nama_detail_parameter', 'FINISHING')->first() : null;
            
            if (!$finishingDetail) {
                return response()->json(['data' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 0]);
            }
            
            $query = Produk::where('kategori_utama_id', $finishingDetail->id)
                        ->where('status_aktif', true)
                        ->with(['kategoriUtama', 'subSatuan']);
        }
        
        if ($request->search) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_produk) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(nama_produk) LIKE ?', ["%{$search}%"]);
            });
        }
        
        $produks = $query->orderBy('nama_produk')->paginate(10);
        
        $produks->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode_produk' => $item->kode_produk,
                'nama_produk' => $item->nama_produk,
                'jenis_produk' => $item->jenis_produk,
                'total_modal_keseluruhan' => $item->total_modal_keseluruhan ?? 0,
                'harga_bertingkat_json' => $item->harga_bertingkat_json ?? [],
                'harga_reseller_json' => $item->harga_reseller_json ?? [],
                'kategori_nama' => $item->kategoriUtama?->nama_detail_parameter ?? '-',
                'satuan_nama' => $item->subSatuan?->nama_sub_detail_parameter ?? '-',
                'is_metric' => $item->is_metric ?? false,
                'panjang' => $item->panjang,  
                'lebar' => $item->lebar,  
                'panjang_locked' => $item->panjang_locked ?? false, 
                'lebar_locked' => $item->lebar_locked ?? false,     
                'metric_unit' => $item->metric_unit ?? 'cm'
            ];
        });
        
        return response()->json($produks);
    }

    public function cariSemuaProduk(Request $request)
    {
        $search = $request->input('search');
        $query = Produk::where('status_aktif', true);

        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_produk) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(nama_produk) LIKE ?', ["%{$search}%"]);
            });
        }

        $produks = $query->with(['kategoriUtama', 'subSatuan'])->orderBy('nama_produk')->paginate(10);

        $produks->getCollection()->transform(function ($item) {
            $modeWarna = null;
            $modeCetakan = null;
    
            $params = $item->parameter_modal_json ?? [];
            if (is_array($params)) {
                foreach ($params as $p) {
                    if ($modeWarna === null && !empty($p['mode_warna'])) {
                        $modeWarna = $p['mode_warna'];
                    }
                    if ($modeCetakan === null && !empty($p['mode_cetakan'])) {
                        $modeCetakan = $p['mode_cetakan'];
                    }
                    if ($modeWarna !== null && $modeCetakan !== null) {
                        break;
                    }
                }
            }
            return [
                'id' => $item->id,
                'kode_produk' => $item->kode_produk,
                'nama_produk' => $item->nama_produk,
                'jenis_produk' => $item->jenis_produk,
                'total_modal_keseluruhan' => $item->total_modal_keseluruhan ?? 0,
                'kategori_nama' => $item->kategoriUtama?->nama_detail_parameter ?? '-',
                'panjang' => $item->panjang,
                'lebar' => $item->lebar,
                'panjang_locked' => $item->panjang_locked ?? false,
                'lebar_locked' => $item->lebar_locked ?? false,
                'satuan_id' => $item->sub_satuan_id,
                'satuan_nama' => $item->subSatuan?->nama_sub_detail_parameter ?? '-',
                'is_metric' => $item->is_metric ?? false,
                'metric_unit' => $item->metric_unit ?? '-',
                'harga_bertingkat_json' => $item->harga_bertingkat_json ?? [],
                'harga_reseller_json' => $item->harga_reseller_json ?? [],
                'mode_warna' => $modeWarna,
                'mode_cetakan' => $modeCetakan,
            ];
        });

        return response()->json($produks);
    }

    public function cariRelasiProduk(Produk $produk)
    {
        $produk->load(['bahanBakus', 'produkKomponen']);

        return response()->json([
            'id' => $produk->id,
            'jenis_produk' => $produk->jenis_produk,
            'bahan_baku' => $produk->bahanBakus->map(function ($bahan) {
                return [
                    'id' => $bahan->id,
                    'nama' => $bahan->nama_bahan ?? $bahan->nama ?? '-',
                    'kode' => $bahan->kode_bahan ?? null,
                ];
            }),
            'komponen' => $produk->produkKomponen->map(function ($komp) {
                return [
                    'id' => $komp->id,
                    'nama' => $komp->nama_produk,
                    'kode' => $komp->kode_produk,
                ];
            }),
        ]);
    }
}


