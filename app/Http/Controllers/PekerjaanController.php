<?php

namespace App\Http\Controllers;

use App\Services\SpkService;
use App\Models\Pelanggan;
use App\Models\MasterMesin;
use App\Http\Requests\StoreSpkItemCetakProgressRequest;
use App\Http\Requests\BulkCompleteSpkItemCetakRequest;
use App\Models\SPKItem;
use App\Services\ActivityLogService;
use App\Models\SpkItemCetakLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; 

class PekerjaanController extends Controller
{
    public function __construct(
        private SpkService $spkService
    ) {}

    public function managerOrder(Request $request): View
    {
        // $filters = $request->only(['search', 'customer_id']);
        $filters = $request->only(['search']);
        $filters['exclude_status'] = 'selesai';
        $filters['sort_status'] = 'proses_bayar';

        // $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $spk = $this->spkService->getAllSpk($filters);
        $spk->load('items.produk.bahanBakus');
        // $customers = Pelanggan::where('status', true)->get();

        $bahanBakuGroups = [];
        $mesinGroups     = [];
        $pelangganGroups = [];
        $produkGroups = [];

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
                if (!$produk) {
                    continue;
                }
        
                $alur = $produk->alur_produksi_json ?? [];
                if (!is_array($alur)) {
                    continue;
                }
        
                foreach ($alur as $step) {
                    if (!is_array($step)) {
                        continue;
                    }
        
                    $divisiMesinId = $step['divisi_mesin_id'] ?? null;
                    $divisiMesin   = $step['divisi_mesin'] ?? null;
                    $ketDivisi     = $step['keterangan_divisi'] ?? '';
        
                    if (!$divisiMesinId && (!$divisiMesin || trim((string) $divisiMesin) === '')) {
                        continue;
                    }
                    $key = $divisiMesinId ?: trim((string) $divisiMesin);
        
                    if (!isset($mesinGroups[$key])) {
                        $mesinGroups[$key] = [
                            'id'   => $divisiMesinId,
                            'nama' => $divisiMesin ?? ('Mesin #'.$key),
                            'kode' => $ketDivisi ?? '',
                            'spk'  => [],
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

        foreach ($spk as $spkRow) {
            foreach ($spkRow->items as $spkItem) {
                $produk = $spkItem->produk;
        
                $produkId = $spkItem->produk_id ?? null;
                $key = $produkId ?: ($spkItem->nama_produk ?? 'tanpa_produk');
        
                if (!isset($produkGroups[$key])) {
                    $produkGroups[$key] = [
                        'id'   => $produkId,
                        'nama' => $produk?->nama_produk ?? ($spkItem->nama_produk ?? 'Tanpa Produk'),
                        'kode' => $produk?->kode_produk ?? '',
                        'spk'  => [],
                    ];
                }
        
                if (!isset($produkGroups[$key]['spk'][$spkRow->id])) {
                    $produkGroups[$key]['spk'][$spkRow->id] = $spkRow;
                }
            }
        }

        return view('pages.pekerjaan.manager-order', [
            'spk'             => $spk,
            // 'customers'       => $customers,
            'pelangganGroups' => $pelangganGroups,
            'bahanBakuGroups' => $bahanBakuGroups,
            'produkGroups' => $produkGroups,
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
        // $filters = $request->only(['search', 'customer_id']);
        $filters['status'] = 'manager_approval_order';

        $spk = $this->spkService->getAllSpk($filters);
        $spk->load('items.produk.bahanBakus', 'pelanggan', 'items.cetakLogs.user');

        // $customers = Pelanggan::where('status', true)->get();

        $allMesin = MasterMesin::all();
        $tipeMesinGroups = [];

        $findMesinForDivisi = function (?int $divisiId, ?string $divisiNama) use ($allMesin) {
            $divisiNama = $divisiNama ? trim($divisiNama) : null;
        
            return $allMesin->filter(function ($mesin) use ($divisiId, $divisiNama) {
                if ($divisiId && isset($mesin->tipe_mesin_id) && (int) $mesin->tipe_mesin_id === (int) $divisiId) {
                    return true;
                }
        
                if ($divisiNama && isset($mesin->tipe_mesin) && trim((string) $mesin->tipe_mesin) === $divisiNama) {
                    return true;
                }
        
                return false;
            });
        };

        foreach ($spk as $spkRow) {
            foreach ($spkRow->items as $spkItem) {
                $produk = $spkItem->produk;
                if (!$produk) {
                    continue;
                }

                $alur = $produk->alur_produksi_json ?? [];
                if (!is_array($alur) || empty($alur)) {
                    continue;
                }

                foreach ($alur as $step) {
                    if (!is_array($step)) {
                        continue;
                    }

                    $divisiId   = isset($step['divisi_mesin_id']) ? (int) $step['divisi_mesin_id'] : null;
                    $divisiNama = isset($step['divisi_mesin']) ? trim((string) $step['divisi_mesin']) : null;

                    if (!$divisiId && !$divisiNama) {
                        continue;
                    }

                    // $mesin = $findMesinForDivisi($divisiId, $divisiNama);

                    $tipeKey = $divisiId ?: ($divisiNama ?: 'tanpa_tipe');

                    $tipeLabelDasar = $divisiNama ?: 'Tanpa Tipe';

                    if (!isset($tipeMesinGroups[$tipeKey])) {
                        $tipeMesinGroups[$tipeKey] = [
                            'tipe_key'    => $tipeKey,
                            'tipe_label'  => $tipeLabelDasar,
                            'mesin_list'  => [],
                            'spk'         => [],
                            'bahanGroups' => [],
                        ];
                    }

                    $mesinMatches = $findMesinForDivisi($divisiId, $divisiNama);

                    if ($mesinMatches && $mesinMatches->isNotEmpty()) {
                        foreach ($mesinMatches as $mesin) {
                            $tipeMesinGroups[$tipeKey]['mesin_list'][$mesin->id] = $mesin;
                        }
                    }

                    if (!isset($tipeMesinGroups[$tipeKey]['spk'][$spkRow->id])) {
                        $tipeMesinGroups[$tipeKey]['spk'][$spkRow->id] = $spkRow;
                    }

                    foreach ($produk->bahanBakus as $bahan) {
                        $bahanId = $bahan->id;

                        if (!isset($tipeMesinGroups[$tipeKey]['bahanGroups'][$bahanId])) {
                            $tipeMesinGroups[$tipeKey]['bahanGroups'][$bahanId] = [
                                'id'    => $bahan->id,
                                'nama'  => $bahan->nama_bahan ?? ('Bahan #'.$bahan->id),
                                'kode'  => $bahan->kode_bahan ?? '',
                                'items' => [],
                            ];
                        }

                        $uniqKey = 'spk_'.$spkRow->id.'_item_'.$spkItem->id;

                        if (!isset($tipeMesinGroups[$tipeKey]['bahanGroups'][$bahanId]['items'][$uniqKey])) {
                            $tipeMesinGroups[$tipeKey]['bahanGroups'][$bahanId]['items'][$uniqKey] = [
                                'spk'  => $spkRow,
                                'item' => $spkItem,
                            ];
                        }
                    }
                }
            }
        }

        foreach ($tipeMesinGroups as $key => &$group) {
            $mesinList = $group['mesin_list'] ?? [];
            Log::debug('Mesin List:', ['tipeKey' => $key, 'mesinCount' => count($mesinList), 'mesinList' => $mesinList]);

            if (count($mesinList) === 1) {
                $group['label'] = reset($mesinList)->nama_mesin ?? $group['tipe_label'];
                Log::debug('Label for single machine:', ['label' => $group['label']]);
            } else {
                $group['label'] = $group['tipe_label'];
                Log::debug('Label for multiple machines:', ['label' => $group['label']]);
            }
        }
        unset($group);

        return view('pages.pekerjaan.operator-cetak', [
            'spk'             => $spk,
            // 'customers'       => $customers,
            'tipeMesinGroups' => $tipeMesinGroups,
        ]);
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

    public function getCetakProgress(SPKItem $spkItem): JsonResponse
    {
        $spkItem->load(['cetakLogs.user', 'spk.pelanggan']);

        $qty = (int) ($spkItem->jumlah ?? 0);
        $sudah = (int) $spkItem->cetakLogs->sum('jumlah');
        $sisa = max(0, $qty - $sudah);
        $pct = $qty > 0 ? min(100, round(($sudah / $qty) * 100, 1)) : 0;

        return response()->json([
            'item' => [
                'id' => $spkItem->id,
                'nama_produk' => $spkItem->nama_produk,
                'qty' => $qty,
                'sudah' => $sudah,
                'sisa' => $sisa,
                'progress_persen' => $pct,
            ],
            'spk' => [
                'nomor_spk' => $spkItem->spk->nomor_spk ?? '',
                'pelanggan' => $spkItem->spk->pelanggan->nama ?? '-',
            ],
            'logs' => $spkItem->cetakLogs()
                ->withTrashed()
                ->orderBy('created_at')
                ->get()
                ->values()
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'jumlah' => (int) $l->jumlah,
                    'operator' => $l->user->name ?? ('User #'.$l->user_id),
                    'waktu' => optional($l->created_at)->format('H:i') ?? '',
                    'tanggal' => optional($l->created_at)->format('d/m/Y') ?? '',
                    'is_batalkan' => $l->trashed(),
                ]),
        ]);
    }

    public function storeCetakProgress(StoreSpkItemCetakProgressRequest $request): RedirectResponse
    {
        $spkItemId = (int) $request->input('spk_item_id');
        $jumlah = (int) $request->input('jumlah');

        try {
            DB::transaction(function () use ($spkItemId, $jumlah) {
                $item = SPKItem::query()
                    ->lockForUpdate()
                    ->findOrFail($spkItemId);

                $qtyPesanan = (int) ($item->jumlah ?? 0);
                if ($qtyPesanan <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Quantity pesanan tidak valid.',
                    ]);
                }

                $lockedLogs = SpkItemCetakLog::query()
                    ->where('spk_item_id', $item->id)
                    ->lockForUpdate()
                    ->get(['jumlah']);

                $sudahCetak = (int) $lockedLogs->sum('jumlah');
                $akanTotal = $sudahCetak + $jumlah;

                if ($akanTotal > $qtyPesanan) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Jumlah melebihi qty pesanan (overprint).',
                    ]);
                }

                SpkItemCetakLog::query()->create([
                    'spk_item_id' => $item->id,
                    'user_id' => 1, //(int) auth()->id(),
                    'jumlah' => $jumlah,
                ]);

                if ($item->spk) {
                    $keterangan = sprintf(
                        'Cetak %d %s untuk item "%s".',
                        $jumlah,
                        $item->satuan,
                        $item->nama_produk
                    );
                    ActivityLogService::log(
                        $item->spk,
                        'spk_item_cetak_tambah',
                        $keterangan,
                        'info'
                    );
                }
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', 'Progress cetak berhasil ditambahkan.');
    }

    public function bulkCompleteCetak(BulkCompleteSpkItemCetakRequest $request): RedirectResponse
    {
        $ids = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->values();

        DB::transaction(function () use ($ids) {
            $items = SPKItem::query()
                ->whereIn('id', $ids->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($ids as $id) {
                $item = $items->get($id);
                if (!$item) {
                    continue;
                }

                $qtyPesanan = (int) ($item->jumlah ?? 0);
                if ($qtyPesanan <= 0) {
                    continue;
                }

                $lockedLogs = SpkItemCetakLog::query()
                    ->where('spk_item_id', $item->id)
                    ->lockForUpdate()
                    ->get(['jumlah']);

                $sudahCetak = (int) $lockedLogs->sum('jumlah');

                $sisa = $qtyPesanan - $sudahCetak;
                if ($sisa <= 0) {
                    continue;
                }

                SpkItemCetakLog::query()->create([
                    'spk_item_id' => $item->id,
                    'user_id' => 1, //(int) auth()->id(),
                    'jumlah' => $sisa,
                ]);

                if ($item->spk) {
                    $keterangan = sprintf(
                        'Bulk-complete cetak %d %s untuk item "%s".',
                        $sisa,
                        $item->satuan,
                        $item->nama_produk
                    );
                    ActivityLogService::log(
                        $item->spk,
                        'spk_item_cetak_bulk_complete',
                        $keterangan,
                        'info'
                    );
                }
            }
        });

        return back()->with('success', 'Bulk print berhasil: item ditandai 100% selesai.');
    }

    public function history(SPKItem $spkItem)
    {
        $logs = $spkItem->cetakLogs() 
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'tanggal_label' => optional($log->created_at)->format('d M Y H:i'),
                    'jumlah' => (int) $log->jumlah,
                    'jumlah_formatted' => number_format((int) $log->jumlah, 0, ',', '.'),
                    'operator' => optional($log->user)->name ?? null,
                    // 'keterangan' => $log->keterangan,
                ];
            });

        return response()->json(['logs' => $logs]);
    }

    public function destroyHistory(SpkItemCetakLog $log)
    {
        DB::transaction(function () use ($log, &$response) {
            $spkItem = $log->spkItem;
            $log->delete();

            $qtyPesanan   = (int) ($spkItem->jumlah ?? 0);
            $sudahCetak   = (int) $spkItem->cetakLogs()->sum('jumlah');
            $sisa         = max(0, $qtyPesanan - $sudahCetak);

            $progressPct  = 0;
            if ($qtyPesanan > 0) {
                $progressPct = min(100, round(($sudahCetak / $qtyPesanan) * 100));
            }

            if ($spkItem->spk) {
                $keterangan = sprintf(
                    'Batalkan cetak %d %s untuk item "%s".',
                    (int) $log->jumlah,
                    $spkItem->satuan,
                    $spkItem->nama_produk
                );
                ActivityLogService::log(
                    $spkItem->spk,
                    'spk_item_cetak_batalkan',
                    $keterangan,
                    'warning'
                );
            }
            
            $response = [
                'success'            => true,
                'spk_item_id'        => $spkItem->id,
                'jumlah_sudah_cetak' => $sudahCetak,
                'sisa_belum_cetak'   => $sisa,
                'progress_persen'    => $progressPct,
            ];
        });

        return response()->json($response ?? ['success' => false]);
    }

    public function historyLogs(Request $request): JsonResponse
    {
        $request->validate([
            'filter'    => ['nullable', 'in:hari_ini,kemarin,bulan_ini,rentang'],
            'date_from' => ['nullable', 'date', 'required_if:filter,rentang'],
            'date_to'   => ['nullable', 'date', 'required_if:filter,rentang', 'after_or_equal:date_from'],
            'page'      => ['nullable', 'integer', 'min:1'],
        ]);

        $filter = $request->input('filter', 'hari_ini');
        $query = SpkItemCetakLog::query()
            ->withTrashed()
            ->with(['spkItem.spk.pelanggan', 'user'])
            ->orderByDesc('created_at');

        $now = now();
        switch ($filter) {
            case 'hari_ini':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'kemarin':
                $query->whereDate('created_at', $now->subDay()->toDateString());
                break;
            case 'bulan_ini':
                $query->whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month);
                break;
            case 'rentang':
                $dateFrom = $request->date('date_from');
                $dateTo = $request->date('date_to');
                if ($dateFrom && $dateTo) {
                    $query->whereDate('created_at', '>=', $dateFrom)
                        ->whereDate('created_at', '<=', $dateTo);
                }
                break;
        }

        $logs = $query->paginate(10)->withQueryString();

        $data = $logs->getCollection()->map(function ($log) {
            $spkItem = $log->spkItem;
            $spk = $spkItem?->spk;
            return [
                'id'              => $log->id,
                'tanggal_label'   => optional($log->created_at)->format('d/m/Y H:i'),
                'nomor_spk'       => $spk?->nomor_spk ?? '-',
                'nama_produk'     => $spkItem?->nama_produk ?? '-',
                'jumlah_formatted' => number_format((int) $log->jumlah, 0, ',', '.'),
                'operator'        => optional($log->user)->name ?? ('User #' . $log->user_id),
                'is_batalkan'      => $log->trashed(),  
            ];
        })->values()->all();

        return response()->json([
            'data'  => $data,
            'links' => $logs->linkCollection()->toArray(),
            'meta'  => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
                'from'         => $logs->firstItem(),
                'to'           => $logs->lastItem(),
            ],
        ]);
    }
}