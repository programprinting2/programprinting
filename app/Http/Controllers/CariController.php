<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Pemasok;
use Illuminate\Http\Request;

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
        $query->with(['kategoriDetail', 'subKategoriDetail', 'satuanUtamaDetail']);

        $bahanBakus = $query->orderBy('nama_bahan')->paginate(10);

        // Ubah response agar menampilkan nama kategori, sub-kategori, satuan utama
        $bahanBakus->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'kode_bahan' => $item->kode_bahan,
                'nama_bahan' => $item->nama_bahan,
                'kategori' => $item->kategoriDetail ? $item->kategoriDetail->nama_detail_parameter : '-',
                'sub_kategori' => $item->subKategoriDetail ? $item->subKategoriDetail->nama_sub_detail_parameter : '-',
                'satuan_utama' => $item->satuanUtamaDetail ? $item->satuanUtamaDetail->nama_detail_parameter : '-',
                'harga_terakhir' => $item->harga_terakhir ?? 0,
                'konversi_satuan' => $item->konversi_satuan_json ?? [],
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
}


