<?php

namespace App\Http\Controllers;

use App\Services\SpkService;
use App\Models\Pelanggan;
use App\Models\MasterMesin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PekerjaanController extends Controller
{
    public function __construct(
        private SpkService $spkService
    ) {}

    public function managerOrder(Request $request): View
    {
        // $filters = $request->only(['search', 'customer_id']);
        $filters = $request->only(['search', 'status']);
        $filters['exclude_status'] = 'selesai';
        $filters['sort_status'] = 'proses_bayar';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $spk->load('items.produk.bahanBakus');
        // $customers = Pelanggan::where('status', true)->get();

        $bahanBakuGroups = [];
        $mesinGroups     = [];
        $pelangganGroups = [];

        foreach ($spk as $spkRow) {
            foreach ($spkRow->items as $spkItem) {
                $produk = $spkItem->produk;
                if (!$produk) {
                    continue;
                }
        
                foreach ($produk->bahanBakus as $bahan) {
                    $key = $bahan->id;
        
                    if (!isset($bahanBakuGroups[$key])) {
                        $bahanBakuGroups[$key] = [
                            'id'    => $bahan->id,
                            'nama'  => $bahan->nama_bahan ?? ('Bahan #'.$bahan->id),
                            'kode'  => $bahan->kode_bahan ?? '',
                            'spk'   => [],
                        ];
                    }
        
                    if (!isset($bahanBakuGroups[$key]['spk'][$spkRow->id])) {
                        $bahanBakuGroups[$key]['spk'][$spkRow->id] = $spkRow;
                    }
                }
            }
        }
        
        foreach ($spk as $spkRow) {
            foreach ($spkRow->items as $spkItem) {
                $produk = $spkItem->produk;
                if (!$produk) continue;
        
                foreach ($produk->mesin_ids as $mesinId) {
                    $mesin = MasterMesin::find($mesinId);
                    if (!$mesin) continue;
        
                    $key = $mesin->id;
                    if (!isset($mesinGroups[$key])) {
                        $mesinGroups[$key] = [
                            'id'    => $mesin->id,
                            'nama'  => $mesin->nama_mesin ?? ('Mesin #'.$mesin->id),
                            'kode'  => $mesin->tipe_mesin ?? '',
                            'spk'   => [],
                        ];
                    }
        
                    if (!isset($mesinGroups[$key]['spk'][$spkRow->id])) {
                        $mesinGroups[$key]['spk'][$spkRow->id] = $spkRow;
                    }
                }
            }
        }

        foreach ($spk as $spkRow) {
            $pelanggan = $spkRow->pelanggan;
            $key = $pelanggan?->id ?? 'tanpa_pelanggan';
    
            if (!isset($pelangganGroups[$key])) {
                $pelangganGroups[$key] = [
                    'id'    => $pelanggan?->id,
                    'nama'  => $pelanggan?->nama ?? 'Tanpa Pelanggan',
                    'email' => $pelanggan?->email ?? null,
                    'spk'   => [],
                ];
            }
    
            if (!isset($pelangganGroups[$key]['spk'][$spkRow->id])) {
                $pelangganGroups[$key]['spk'][$spkRow->id] = $spkRow;
            }
        }

        return view('pages.pekerjaan.manager-order', [
            'spk'             => $spk,
            // 'customers'       => $customers,
            'pelangganGroups' => $pelangganGroups,
            'bahanBakuGroups' => $bahanBakuGroups,
            'mesinGroups'     => $mesinGroups,
        ]);
    }

    public function managerProduksi(Request $request): View
    {
        // $filters = $request->only(['search', 'customer_id']);
        $filters = $request->only(['search', 'status']);
        $filters['exclude_status'] = 'selesai';
        $filters['sort_status'] = 'manager_approval_order';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        // $customers = Pelanggan::where('status', true)->get();

        return view('pages.pekerjaan.manager-produksi', compact('spk'));
    }

    public function operatorCetak(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id']);
        $filters['status'] = 'manager_approval_produksi';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $customers = Pelanggan::where('status', true)->get();

        return view('pages.pekerjaan.operator-cetak', compact('spk', 'customers'));
    }

    public function finishingQc(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id']);
        $filters['status'] = 'operator_cetak';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $customers = Pelanggan::where('status', true)->get();

        return view('pages.pekerjaan.finishing-qc', compact('spk', 'customers'));
    }

    public function siapAmbil(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id']);
        $filters['status'] = 'finishing_qc';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $customers = Pelanggan::where('status', true)->get();

        return view('pages.pekerjaan.siap-ambil', compact('spk', 'customers'));
    }

    public function tandaiSelesai(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id']);
        $filters['status'] = 'siap_diambil';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $customers = Pelanggan::where('status', true)->get();

        return view('pages.pekerjaan.tandai-selesai', compact('spk', 'customers'));
    }
}