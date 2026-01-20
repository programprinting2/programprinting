<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Pemasok;
use App\Models\DetailParameter;
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
        $query->with(['kategoriDetail', 'subKategoriDetail', 'satuanUtamaDetail', 'subSatuanDetail']);

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
}


