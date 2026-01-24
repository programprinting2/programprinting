<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class HutangController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $selectedPemasokId = $request->get('pemasok_id');
            
            // Ambil data pemasok dengan hutang (pembelian belum lunas)
            $pemasokWithHutang = Pemasok::where('status', true)
                ->whereHas('pembelian', function ($query) {
                    $query->where('is_lunas', false);
                })
                ->with(['pembelian' => function ($query) {
                    $query->where('is_lunas', false)
                        ->orderBy('tanggal_pembelian', 'desc');
                }])
                ->orderBy('nama')
                ->get()
                ->map(function ($pemasok) {
                    $pembelianBelumLunas = $pemasok->pembelian;
                    $totalHutang = $pembelianBelumLunas->sum('total');
                    $jumlahTransaksi = $pembelianBelumLunas->count();
                    
                    return [
                        'id' => $pemasok->id,
                        'nama' => $pemasok->nama,
                        'kode_pemasok' => $pemasok->kode_pemasok,
                        'jumlah_transaksi' => $jumlahTransaksi,
                        'total_hutang' => $totalHutang,
                        'pembelian' => $pembelianBelumLunas
                    ];
                });

            // Data pembelian untuk tabel (berdasarkan pemasok yang dipilih)
            $pembelianData = collect([]);
            if ($selectedPemasokId) {
                $pembelianData = Pembelian::with(['pemasok'])
                    ->where('pemasok_id', $selectedPemasokId)
                    ->where('is_lunas', false)
                    ->orderBy('tanggal_pembelian', 'desc')
                    ->paginate(15);
            }

            return view('pages.hutang.index', compact(
                'pemasokWithHutang', 
                'pembelianData', 
                'selectedPemasokId'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error loading Hutang page', ['error' => $e->getMessage()]);
            return view('pages.hutang.index', [
                'pemasokWithHutang' => collect([]),
                'pembelianData' => collect([])->paginate(),
                'selectedPemasokId' => null
            ])->with('error', 'Gagal memuat data hutang');
        }
    }
}