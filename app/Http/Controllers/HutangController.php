<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class HutangController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        try {
            $activeTab = $request->get('tab', 'pemasok');

            // Jika request AJAX untuk detail
            if ($request->ajax()) {
                if ($request->has('pemasok_id')) {
                    return $this->getPemasokDetailAjax($request->pemasok_id);
                }
                if ($request->has('umur_group')) {
                    return $this->getUmurHutangDetailAjax($request->umur_group);
                }
            }

            // Data untuk tab Per Pemasok
            $pemasokWithHutang = $this->getPemasokWithHutang();

            // Data untuk tab Per Umur Hutang
            $umurHutangGroups = $this->getUmurHutangGroups();

            return view('pages.hutang.index', compact(
                'pemasokWithHutang', 
                'umurHutangGroups', 
                'activeTab'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error loading Hutang page', ['error' => $e->getMessage()]);
            return view('pages.hutang.index', [
                'pemasokWithHutang' => collect([]),
                'umurHutangGroups' => collect([]),
                'activeTab' => 'pemasok'
            ])->with('error', 'Gagal memuat data hutang');
        }
    }

    private function getPemasokWithHutang()
    {
        return Pemasok::where('status', true)
            ->whereHas('pembelian', function ($query) {
                $query->where('is_lunas', false);
            })
            ->withCount(['pembelian as jumlah_hutang' => function ($query) {
                $query->where('is_lunas', false);
            }])
            ->with(['pembelian' => function ($query) {
                $query->where('is_lunas', false)
                    ->select('id', 'pemasok_id', 'total');
            }])
            ->orderBy('nama')
            ->get()
            ->map(function ($pemasok) {
                return [
                    'id' => $pemasok->id,
                    'nama' => $pemasok->nama,
                    'kode_pemasok' => $pemasok->kode_pemasok,
                    'jumlah_transaksi' => $pemasok->jumlah_hutang,
                    'total_hutang' => $pemasok->pembelian->sum('total'),
                ];
            });
    }

    private function getUmurHutangGroups()
    {
        $now = Carbon::now();
        
        // Ambil semua pembelian belum lunas
        $pembelianBelumLunas = Pembelian::with('pemasok')
            ->where('is_lunas', false)
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        // Kelompokkan berdasarkan umur hutang
        $groups = [
            '0-30' => ['label' => '0-30 Hari', 'data' => [], 'total' => 0],
            '30-60' => ['label' => '31-60 Hari', 'data' => [], 'total' => 0],
            '60-90' => ['label' => '61-90 Hari', 'data' => [], 'total' => 0],
            '90+' => ['label' => '>90 Hari', 'data' => [], 'total' => 0],
        ];

        foreach ($pembelianBelumLunas as $pembelian) {
            $tanggalPembelian = Carbon::parse($pembelian->tanggal_pembelian);
            $umurHari = $now->diffInDays($tanggalPembelian);
            
            if ($umurHari <= 30) {
                $group = '0-30';
            } elseif ($umurHari <= 60) {
                $group = '30-60';
            } elseif ($umurHari <= 90) {
                $group = '60-90';
            } else {
                $group = '90+';
            }
            
            $groups[$group]['data'][] = $pembelian;
            $groups[$group]['total'] += $pembelian->total;
        }

        // Filter hanya group yang ada datanya
        return collect($groups)->filter(function ($group) {
            return count($group['data']) > 0;
        });
    }

    private function getPemasokDetailAjax($pemasokId): JsonResponse
    {
        try {
            $pembelian = Pembelian::with(['pemasok:id,nama,kode_pemasok'])
                ->where('pemasok_id', $pemasokId)
                ->where('is_lunas', false)
                ->orderBy('tanggal_pembelian', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pembelian,
                'summary' => [
                    'total_transaksi' => $pembelian->count(),
                    'total_hutang' => $pembelian->sum('total')
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading pemasok detail', ['pemasok_id' => $pemasokId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail pembelian'
            ], 500);
        }
    }

    private function getUmurHutangDetailAjax($umurGroup): JsonResponse
    {
        try {
            $now = Carbon::now();
            
            $query = Pembelian::with('pemasok')
                ->where('is_lunas', false);
            
            // Filter berdasarkan umur group
            switch ($umurGroup) {
                case '0-30':
                    $query->whereRaw('(CURRENT_DATE - tanggal_pembelian) <= 30');
                    break;
            
                case '30-60':
                    $query->whereRaw('(CURRENT_DATE - tanggal_pembelian) BETWEEN 31 AND 60');
                    break;
            
                case '60-90':
                    $query->whereRaw('(CURRENT_DATE - tanggal_pembelian) BETWEEN 61 AND 90');
                    break;
            
                case '90+':
                    $query->whereRaw('(CURRENT_DATE - tanggal_pembelian) > 90');
                    break;
            }
            
            
            $pembelian = $query->orderBy('tanggal_pembelian', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $pembelian,
                'summary' => [
                    'total_transaksi' => $pembelian->count(),
                    'total_hutang' => $pembelian->sum('total'),
                    'group_label' => $this->getUmurGroupLabel($umurGroup)
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading umur hutang detail', ['umur_group' => $umurGroup, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail hutang'
            ], 500);
        }
    }

    private function getUmurGroupLabel($group): string
    {
        $labels = [
            '0-30' => '0-30 Hari',
            '30-60' => '31-60 Hari',
            '60-90' => '61-90 Hari',
            '90+' => '>90 Hari'
        ];
        
        return $labels[$group] ?? $group;
    }
}