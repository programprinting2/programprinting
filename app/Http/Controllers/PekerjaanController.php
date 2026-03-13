<?php

namespace App\Http\Controllers;

use App\Services\SpkService;
use App\Models\Pelanggan;
use App\Models\MasterMesin;
use App\Http\Requests\StoreSpkItemCetakProgressRequest;
use App\Http\Requests\BulkCompleteSpkItemCetakRequest;
use App\Http\Requests\AmbilPekerjaanRequest;
use App\Http\Requests\MultiAmbilPekerjaanRequest;
use App\Http\Requests\BatalAmbilPekerjaanRequest;
use App\Models\SPKItem;
use App\Models\SpkItemCetakQueue;
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
        // $spk->load('items.produk.bahanBakus');
        $spk->load([
            'items.produk.bahanBakus',
            'items.cetakLogs'
        ]);
        $allItems = $spk->pluck('items')->flatten();
        // $customers = Pelanggan::where('status', true)->get();

        $bahanBakuGroups = [];
        $mesinGroups     = [];
        $pelangganGroups = [];
        $produkGroups = [];

        // foreach ($spk as $spkRow) {
        //     foreach ($spkRow->items as $spkItem) {
        //         $produk = $spkItem->produk;
        //         if (!$produk) {
        //             continue;
        //         }
        
        //         foreach ($produk->bahanBakus as $bahan) {
        //             $key = $bahan->id;
        
        //             if (!isset($bahanBakuGroups[$key])) {
        //                 $bahanBakuGroups[$key] = [
        //                     'id'    => $bahan->id,
        //                     'nama'  => $bahan->nama_bahan ?? ('Bahan #'.$bahan->id),
        //                     'kode'  => $bahan->kode_bahan ?? '',
        //                     'spk'   => [],
        //                 ];
        //             }
        
        //             if (!isset($bahanBakuGroups[$key]['spk'][$spkRow->id])) {
        //                 $bahanBakuGroups[$key]['spk'][$spkRow->id] = $spkRow;
        //             }
        //         }
        //     }
        // }
        foreach ($allItems as $item) {

            $produk = $item->produk;
            if (!$produk) continue;
        
            foreach ($produk->bahanBakus as $bahan) {
        
                $key = $bahan->id;
        
                if (!isset($bahanBakuGroups[$key])) {
                    $bahanBakuGroups[$key] = [
                        'id' => $bahan->id,
                        'nama' => $bahan->nama_bahan,
                        'kode' => $bahan->kode_bahan,
                        'spk' => []
                    ];
                }
        
                $spkId = $item->spk_id;
        
                if (!isset($bahanBakuGroups[$key]['spk'][$spkId])) {
                    $bahanBakuGroups[$key]['spk'][$spkId] = $spk->firstWhere('id',$spkId);
                }
            }
        }
        
        // foreach ($spk as $spkRow) {
        //     foreach ($spkRow->items as $spkItem) {
        //         $produk = $spkItem->produk;
        //         if (!$produk) {
        //             continue;
        //         }
        
        //         $alur = $produk->alur_produksi_json ?? [];
        //         if (!is_array($alur)) {
        //             continue;
        //         }
        
        //         foreach ($alur as $step) {
        //             if (!is_array($step)) {
        //                 continue;
        //             }
        
        //             $divisiMesinId = $step['divisi_mesin_id'] ?? null;
        //             $divisiMesin   = $step['divisi_mesin'] ?? null;
        //             $ketDivisi     = $step['keterangan_divisi'] ?? '';
        
        //             if (!$divisiMesinId && (!$divisiMesin || trim((string) $divisiMesin) === '')) {
        //                 continue;
        //             }
        //             $key = $divisiMesinId ?: trim((string) $divisiMesin);
        
        //             if (!isset($mesinGroups[$key])) {
        //                 $mesinGroups[$key] = [
        //                     'id'   => $divisiMesinId,
        //                     'nama' => $divisiMesin ?? ('Mesin #'.$key),
        //                     'kode' => $ketDivisi ?? '',
        //                     'spk'  => [],
        //                 ];
        //             }
        
        //             if (!isset($mesinGroups[$key]['spk'][$spkRow->id])) {
        //                 $mesinGroups[$key]['spk'][$spkRow->id] = $spkRow;
        //             }
        //         }
        //     }
        // }

        foreach ($allItems as $item) {

            $produk = $item->produk;
            if (!$produk) continue;

            $alur = $produk->alur_produksi_json ?? [];

            foreach ($alur as $step) {

                $mesinId = $step['divisi_mesin_id'] ?? null;
                $mesinNama = $step['divisi_mesin'] ?? null;

                if (!$mesinId && !$mesinNama) continue;

                $key = $mesinId ?: $mesinNama;

                if (!isset($mesinGroups[$key])) {
                    $mesinGroups[$key] = [
                        'id' => $mesinId,
                        'nama' => $mesinNama,
                        'kode' => $step['keterangan_divisi'] ?? '',
                        'spk' => []
                    ];
                }

                $spkId = $item->spk_id;

                if (!isset($mesinGroups[$key]['spk'][$spkId])) {
                    $mesinGroups[$key]['spk'][$spkId] = $spk->firstWhere('id',$spkId);
                }
            }
        }

        // foreach ($spk as $spkRow) {
        //     $pelanggan = $spkRow->pelanggan;
        //     $key = $pelanggan?->id ?? 'tanpa_pelanggan';
    
        //     if (!isset($pelangganGroups[$key])) {
        //         $pelangganGroups[$key] = [
        //             'id'    => $pelanggan?->id,
        //             'nama'  => $pelanggan?->nama ?? 'Tanpa Pelanggan',
        //             'email' => $pelanggan?->email ?? null,
        //             'spk'   => [],
        //         ];
        //     }
    
        //     if (!isset($pelangganGroups[$key]['spk'][$spkRow->id])) {
        //         $pelangganGroups[$key]['spk'][$spkRow->id] = $spkRow;
        //     }
        // }
        foreach ($spk as $row) {

            $pel = $row->pelanggan;
        
            $key = $pel?->id ?? 'none';
        
            if (!isset($pelangganGroups[$key])) {
                $pelangganGroups[$key] = [
                    'id'=>$pel?->id,
                    'nama'=>$pel?->nama ?? 'Tanpa Pelanggan',
                    'email'=>$pel?->email,
                    'spk'=>[]
                ];
            }
        
            $pelangganGroups[$key]['spk'][$row->id] = $row;
        }

        
        foreach ($allItems as $item) {
        
            $key = $item->produk_id ?: $item->nama_produk;
        
            if (!isset($produkGroups[$key])) {
        
                $produk = $item->produk;
        
                $produkGroups[$key] = [
                    'id'=>$item->produk_id,
                    'nama'=>$produk->nama_produk ?? $item->nama_produk,
                    'kode'=>$produk->kode_produk ?? '',
                    'spk'=>[]
                ];
            }
        
            $produkGroups[$key]['spk'][$item->spk_id] = $spk->firstWhere('id',$item->spk_id);
        }

        // foreach ($spk as $spkRow) {
        //     foreach ($spkRow->items as $spkItem) {
        //         $produk = $spkItem->produk;
        
        //         $produkId = $spkItem->produk_id ?? null;
        //         $key = $produkId ?: ($spkItem->nama_produk ?? 'tanpa_produk');
        
        //         if (!isset($produkGroups[$key])) {
        //             $produkGroups[$key] = [
        //                 'id'   => $produkId,
        //                 'nama' => $produk?->nama_produk ?? ($spkItem->nama_produk ?? 'Tanpa Produk'),
        //                 'kode' => $produk?->kode_produk ?? '',
        //                 'spk'  => [],
        //             ];
        //         }
        
        //         if (!isset($produkGroups[$key]['spk'][$spkRow->id])) {
        //             $produkGroups[$key]['spk'][$spkRow->id] = $spkRow;
        //         }
        //     }
        // }


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
        $filters['status'] = 'manager_approval_order';

        $spk = $this->spkService->getAllSpk($filters);

        $spk->load([
            'items.produk.bahanBakus',
            'pelanggan',
            'items.cetakLogs.user'
        ]);

        $allItems = $spk->pluck('items')->flatten();
        $itemIds = $allItems->pluck('id')->unique();

        $queueTotalsByItemId = SpkItemCetakQueue::query()
            ->selectRaw('spk_item_id, SUM(jumlah) as total_diambil')
            ->whereIn('spk_item_id', $itemIds)
            ->groupBy('spk_item_id')
            ->pluck('total_diambil', 'spk_item_id');

        $printTotalsByItemId = SpkItemCetakLog::query()
            ->selectRaw('spk_item_id, SUM(jumlah) as total_cetak')
            ->whereIn('spk_item_id', $itemIds)
            ->groupBy('spk_item_id')
            ->pluck('total_cetak', 'spk_item_id');

        $allMesin = MasterMesin::all();

        $mesinByTipeId = $allMesin->groupBy('tipe_mesin_id');

        $mesinByTipeNama = $allMesin->groupBy(function ($m) {
            return trim((string) $m->tipe_mesin);
        });

        $tipeMesinGroups = [];

        foreach ($allItems as $spkItem) {
            $qtyPesanan = (int) ($spkItem->jumlah ?? 0);
            $totalDiambil = (int) ($queueTotalsByItemId[$spkItem->id] ?? 0);
            $totalCetak = (int) ($printTotalsByItemId[$spkItem->id] ?? 0);
            $ambilFull = $qtyPesanan > 0 && $totalDiambil >= $qtyPesanan;

            $cetakSelesai = $totalDiambil > 0 && $totalCetak >= $totalDiambil;
            if ($ambilFull && $cetakSelesai) {
                continue;
            }

            $produk = $spkItem->produk;
            if (!$produk) {
                continue;
            }

            $alur = $produk->alur_produksi_json ?? [];
            if (!is_array($alur) || empty($alur)) {
                continue;
            }

            $bahanBakus = $produk->bahanBakus;
            foreach ($alur as $step) {
                if (!is_array($step)) {
                    continue;
                }
                $divisiId   = $step['divisi_mesin_id'] ?? null;
                $divisiNama = isset($step['divisi_mesin'])
                    ? trim((string) $step['divisi_mesin'])
                    : null;
                if (!$divisiId && !$divisiNama) {
                    continue;
                }

                $tipeKey = $divisiId ?: $divisiNama;
                $tipeLabelDasar = $divisiNama ?: 'Tanpa Tipe';

                if (!isset($tipeMesinGroups[$tipeKey])) {
                    $mesinMatches = collect();
                    if ($divisiId && isset($mesinByTipeId[$divisiId])) {
                        $mesinMatches = $mesinByTipeId[$divisiId];
                    }
                    if ($mesinMatches->isEmpty() && $divisiNama && isset($mesinByTipeNama[$divisiNama])) {
                        $mesinMatches = $mesinByTipeNama[$divisiNama];
                    }

                    $tipeMesinGroups[$tipeKey] = [
                        'tipe_key'    => $tipeKey,
                        'tipe_label'  => $tipeLabelDasar,
                        'mesin_list'  => $mesinMatches->values()->all(),
                        'spk'         => [],
                        'bahanGroups' => [],
                    ];
                }

                $group = &$tipeMesinGroups[$tipeKey];
                $spkRow = $spkItem->spk;

                if ($spkRow && !isset($group['spk'][$spkRow->id])) {
                    $group['spk'][$spkRow->id] = $spkRow;
                }

                foreach ($bahanBakus as $bahan) {
                    $bahanId = $bahan->id;
                    if (!isset($group['bahanGroups'][$bahanId])) {
                        $group['bahanGroups'][$bahanId] = [
                            'id'    => $bahan->id,
                            'nama'  => $bahan->nama_bahan ?? ('Bahan #'.$bahan->id),
                            'kode'  => $bahan->kode_bahan ?? '',
                            'items' => [],
                        ];
                    }

                    $uniqKey = 'spk_'.$spkRow->id.'_item_'.$spkItem->id;

                    if (!isset($group['bahanGroups'][$bahanId]['items'][$uniqKey])) {
                        $group['bahanGroups'][$bahanId]['items'][$uniqKey] = [
                            'spk'  => $spkRow,
                            'item' => $spkItem,
                        ];
                    }
                }
            }
        }

        foreach ($tipeMesinGroups as &$group) {
            $mesinList = $group['mesin_list'] ?? [];
            if (count($mesinList) === 1) {
                $mesin = reset($mesinList);
                $group['label'] = $mesin->nama_mesin ?? $group['tipe_label'];

            } else {
                $group['label'] = $group['tipe_label'];
            }
        }

        unset($group);

        $queueRows = SpkItemCetakQueue::query()
            ->with([
                'spkItem.spk.pelanggan',
                'mesin',
            ])
            ->orderByDesc('created_at')
            ->where('user_id', auth()->id())
            ->get();

        $logsByKey = SpkItemCetakLog::query()
            ->selectRaw('spk_item_id, user_id, mesin_id, SUM(jumlah) as total')
            ->groupBy('spk_item_id', 'user_id', 'mesin_id')
            ->get()
            ->keyBy(function ($row) {
                return $row->spk_item_id.'_'.$row->user_id.'_'.$row->mesin_id;
            });

        $queueRowsFiltered = $queueRows->filter(function ($q) use ($logsByKey) {

            $item = $q->spkItem;

            if (!$item) {
                return false;
            }

            $key = $item->id.'_'.$q->user_id.'_'.$q->mesin_id;

            $printedForQueue = $logsByKey[$key]->total ?? 0;

            return (int)$printedForQueue < (int)$q->jumlah;

        })->values();

        $pekerjaanSayaItems = $queueRowsFiltered->map(function ($q) use ($logsByKey) {
            $item = $q->spkItem;
            $spk  = $item?->spk;
            $key = $q->spk_item_id.'_'.$q->user_id.'_'.$q->mesin_id;
            $printedForQueue = (int) ($logsByKey[$key]->total ?? 0);
            $qtyDiambil = (int) ($q->jumlah ?? 0);
            $progress = 0;

            if ($qtyDiambil > 0) {
                $progress = min(100, round(($printedForQueue / $qtyDiambil) * 100, 1));
            }

            return [
                'queue'       => $q,
                'spk'         => $spk,
                'item'        => $item,
                'mesin'       => $q->mesin,
                'mesin_nama'  => $q->mesin->nama_mesin ?? '-',
                'nomor_spk'   => $spk?->nomor_spk ?? '-',
                'pelanggan'   => $spk?->pelanggan?->nama ?? '-',
                'nama_item'   => $item?->nama_produk ?? '-',
                'printed'     => $printedForQueue,
                'qty_diambil' => $qtyDiambil,
                'progress'    => $progress,
            ];
        });

        $pekerjaanSayaCount = $pekerjaanSayaItems->count();
        return view('pages.pekerjaan.operator-cetak', [
            'spk'                => $spk,
            'tipeMesinGroups'    => $tipeMesinGroups,
            'pekerjaanSayaItems' => $pekerjaanSayaItems,
            'pekerjaanSayaCount' => $pekerjaanSayaCount,
            'queueTotalsByItemId'=> $queueTotalsByItemId,
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
        $jumlah    = (int) $request->input('jumlah');
        $mesinId   = $request->integer('mesin_id') ?: null;

        try {

            DB::transaction(function () use ($spkItemId, $jumlah, $mesinId) {

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
                    'user_id'     => 1, //(int) auth()->id(),
                    'mesin_id'    => $mesinId,
                    'jumlah'      => $jumlah,
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

        $mesinId = $request->integer('mesin_id') ?: null;
        $userId  = (int) (auth()->id() ?? 1);

        DB::transaction(function () use ($ids, $mesinId, $userId) {

            foreach ($ids as $itemId) {

                $item = SPKItem::lockForUpdate()->find($itemId);

                if (!$item) {
                    continue;
                }

                $queue = SpkItemCetakQueue::query()
                    ->where('spk_item_id', $itemId)
                    ->where('user_id', $userId)
                    ->where('mesin_id', $mesinId)
                    ->first();

                if (!$queue) {
                    continue;
                }

                $qtyDiambil = (int) $queue->jumlah;

                $sudahCetak = SpkItemCetakLog::query()
                    ->where('spk_item_id', $itemId)
                    ->where('user_id', $userId)
                    ->where('mesin_id', $mesinId)
                    ->sum('jumlah');

                $sisa = $qtyDiambil - $sudahCetak;

                if ($sisa <= 0) {
                    continue;
                }

                SpkItemCetakLog::create([
                    'spk_item_id' => $itemId,
                    'user_id'     => $userId,
                    'mesin_id'    => $mesinId,
                    'jumlah'      => $sisa,
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

        return back()->with('success', 'Bulk print berhasil: item ditandai selesai.');
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

    public function ambilQueue(AmbilPekerjaanRequest $request): RedirectResponse
    {
        $mesinId = (int) $request->input('mesin_id');
        $ids = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();
        $ambil = (int) $request->input('jumlah');
        $userId = (int) (auth()->id() ?? 1);

        DB::transaction(function () use ($ids, $mesinId, $ambil, $userId) {
            $items = SPKItem::query()
                ->whereIn('id', $ids->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($ids as $spkItemId) {
                $item = $items->get($spkItemId);
                if (!$item) {
                    continue;
                }

                $qtyPesanan = (int) ($item->jumlah ?? 0);
                if ($qtyPesanan <= 0) {
                    continue;
                }

                $totalClaimedAll = (int) SpkItemCetakQueue::query()
                    ->where('spk_item_id', $item->id)
                    ->sum('jumlah');

                if ($totalClaimedAll + $ambil > $qtyPesanan) {
                    $sisaBolehDiambil = max(0, $qtyPesanan - $totalClaimedAll);
                    throw ValidationException::withMessages([
                        'jumlah' => "Jumlah melebihi sisa yang bisa diambil. Sisa yang tersedia: {$sisaBolehDiambil}.",
                    ]);
                }

                SpkItemCetakQueue::query()->create([
                    'spk_item_id' => $item->id,
                    'mesin_id'    => $mesinId,
                    'user_id'     => $userId,
                    'jumlah'      => $ambil,
                ]);
            }
        });

        return back()->with('success', 'Pekerjaan berhasil diambil dan jumlah ditambahkan ke antrian mesin.');
    }

    public function batalAmbilQueue(BatalAmbilPekerjaanRequest $request): RedirectResponse
    {
        $mesinId = (int) $request->input('mesin_id');
        $ids = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        $userId = (int) (auth()->id() ?? 1);

        DB::transaction(function () use ($ids, $mesinId, $userId) {
            $queues = SpkItemCetakQueue::query()
                ->with('spkItem')
                ->where('mesin_id', $mesinId)
                ->where('user_id', $userId)
                ->whereIn('spk_item_id', $ids->all())
                ->lockForUpdate()
                ->get();

            foreach ($queues as $queue) {
                $item = $queue->spkItem;
                if (!$item) {
                    continue;
                }

                $queueQty   = (int) ($queue->jumlah ?? 0);
                if ($queueQty <= 0) {
                    $queue->delete();
                    continue;
                }

                $sudahCetak = (int) ($item->jumlah_sudah_cetak ?? 0);

                $sisaKlaim = max(0, $queueQty - $sudahCetak);

                if ($sisaKlaim <= 0) {
                    $queue->jumlah = min($queueQty, $sudahCetak);
                    if ($queue->jumlah <= 0) {
                        $queue->delete();
                    } else {
                        $queue->save();
                    }
                    continue;
                }

                $newQty = $queueQty - $sisaKlaim;
                if ($newQty <= 0) {
                    $queue->delete();
                } else {
                    $queue->jumlah = $newQty;
                    $queue->save();
                }
            }
        });

        return back()->with('success', 'Sisa pekerjaan yang belum dicetak berhasil dibatalkan dari antrian Anda.');
    }

    public function ambilQueueAll(MultiAmbilPekerjaanRequest $request): RedirectResponse
    {
        $mesinId = (int) $request->input('mesin_id');
        $ids = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        $userId = (int) (auth()->id() ?? 1);

        DB::transaction(function () use ($ids, $mesinId, $userId) {
            $items = SPKItem::query()
                ->whereIn('id', $ids->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($ids as $spkItemId) {
                $item = $items->get($spkItemId);
                if (!$item) {
                    continue;
                }

                $qtyPesanan = (int) ($item->jumlah ?? 0);
                if ($qtyPesanan <= 0) {
                    continue;
                }

                $totalClaimedAll = (int) SpkItemCetakQueue::query()
                    ->where('spk_item_id', $item->id)
                    ->sum('jumlah');

                $sisaBolehDiambil = max(0, $qtyPesanan - $totalClaimedAll);
                if ($sisaBolehDiambil <= 0) {
                    continue;
                }

                SpkItemCetakQueue::query()->create([
                    'spk_item_id' => $item->id,
                    'mesin_id'    => $mesinId,
                    'user_id'     => $userId,
                    'jumlah'      => $sisaBolehDiambil,
                ]);    
            }
        });

        return back()->with('success', 'Multi ambil berhasil: semua sisa qty yang bisa diambil sudah dimasukkan ke antrian.');
    }
}