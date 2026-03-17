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
use App\Models\SPK;
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
use Illuminate\Support\Facades\Cache;

class PekerjaanController extends Controller
{
    public function __construct(
        private SpkService $spkService
    ) {}

    public function managerOrder(Request $request): View
    {
        $filters = $request->only(['search']);
        $filters['exclude_status'] = 'selesai';
        $filters['sort_status']    = 'proses_bayar';

        $spk = $this->spkService->getAllSpk($filters, [
            'pelanggan:id,nama,email',
            'items:id,spk_id,produk_id,jumlah,nama_produk,panjang,lebar,satuan,file_pendukung',
            'items.produk:id,nama_produk,kode_produk,alur_produksi_json,is_metric,metric_unit',
            'items.produk.bahanBakus:id,nama_bahan,kode_bahan',
            'items.cetakLogs:id,spk_item_id,jumlah,deleted_at,created_at'
        ]);

        $spkMap = $spk->keyBy('id');

        $bahanBakuGroups = [];
        $mesinGroups     = [];
        $pelangganGroups = [];
        $produkGroups    = [];

        foreach ($spk as $row) {
            $pelanggan = $row->pelanggan;
            $pelKey    = $pelanggan?->id ?? 'none';

            // Group pelanggan
            if (!isset($pelangganGroups[$pelKey])) {
                $pelangganGroups[$pelKey] = [
                    'id'    => $pelanggan?->id,
                    'nama'  => $pelanggan?->nama ?? 'Tanpa Pelanggan',
                    'email' => $pelanggan?->email,
                    'spk'   => []
                ];
            }
            $pelangganGroups[$pelKey]['spk'][$row->id] = $row;

            foreach ($row->items as $item) {
                $produk = $item->produk;
                if (!$produk) continue;

                // Group bahan baku
                foreach ($produk->bahanBakus as $bahan) {
                    $key = $bahan->id;
                    if (!isset($bahanBakuGroups[$key])) {
                        $bahanBakuGroups[$key] = [
                            'id'   => $bahan->id,
                            'nama' => $bahan->nama_bahan,
                            'kode' => $bahan->kode_bahan,
                            'spk'  => []
                        ];
                    }
                    $bahanBakuGroups[$key]['spk'][$row->id] = $row;
                }

                // Group mesin
                $alur = $produk->alur_produksi_json ?? [];
                foreach ($alur as $step) {
                    $mesinId   = $step['divisi_mesin_id'] ?? null;
                    $mesinNama = $step['divisi_mesin'] ?? null;
                    if (!$mesinId && !$mesinNama) continue;

                    $key = $mesinId ?: $mesinNama;
                    if (!isset($mesinGroups[$key])) {
                        $mesinGroups[$key] = [
                            'id'   => $mesinId,
                            'nama' => $mesinNama,
                            'kode' => $step['keterangan_divisi'] ?? '',
                            'spk'  => []
                        ];
                    }
                    $mesinGroups[$key]['spk'][$row->id] = $row;
                }

                // Group produk
                $prodKey = $item->produk_id ?: $item->nama_produk;
                if (!isset($produkGroups[$prodKey])) {
                    $produkGroups[$prodKey] = [
                        'id'   => $item->produk_id,
                        'nama' => $produk->nama_produk ?? $item->nama_produk,
                        'kode' => $produk->kode_produk ?? '',
                        'spk'  => []
                    ];
                }
                $produkGroups[$prodKey]['spk'][$row->id] = $row;
            }
        }

        return view('pages.pekerjaan.manager-order', [
            'spk'             => $spk,
            'pelangganGroups' => $pelangganGroups,
            'bahanBakuGroups' => $bahanBakuGroups,
            'produkGroups'    => $produkGroups,
            'mesinGroups'     => $mesinGroups,
        ]);
    }

    public function managerOrderRow(SPK $spk)
    {
        // load relasi minimum yang dipakai row
        $spk->load([
            'pelanggan',
            'items', // kalau progress dihitung dari items
            // idealnya progress sudah agregat server-side (lebih cepat)
        ]);

        return view('pages.pekerjaan.partials.manager-order-spk-row', [
            'item' => $spk,
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
            'pelanggan:id,nama,email',
            'items.spk.pelanggan',
            'items:id,spk_id,produk_id,jumlah,nama_produk,satuan,panjang,lebar,file_pendukung',
            'items.spk:id,nomor_spk,pelanggan_id,tanggal_spk',
            'items.produk:id,nama_produk,kode_produk,alur_produksi_json,is_metric,metric_unit',
            'items.produk.bahanBakus:id,nama_bahan,kode_bahan',
            'items.cetakLogs:id,spk_item_id,user_id,mesin_id,jumlah,created_at',
        ]);

        $allItems = $spk->pluck('items')->flatten();
        $itemIds  = $allItems->pluck('id')->unique();

        $queueTotalsByItemId = SpkItemCetakQueue::query()
            ->selectRaw('spk_item_id, SUM(jumlah) as total_diambil')
            ->whereIn('spk_item_id', $itemIds)
            ->groupBy('spk_item_id')
            ->pluck('total_diambil', 'spk_item_id');

        $printTotalsByItemId = SpkItemCetakLog::query()
            ->selectRaw('spk_item_id, SUM(jumlah) as total_cetak')
            ->whereIn('spk_item_id', $itemIds)
            ->whereNull('deleted_at')
            ->groupBy('spk_item_id')
            ->pluck('total_cetak', 'spk_item_id');

        foreach ($allItems as $item) {
            $qty = (int) ($item->jumlah ?? 0);
            $totalCetak = $item->cetakLogs
                ->whereNull('deleted_at')
                ->sum('jumlah');

            $item->jumlah_sudah_cetak = $totalCetak;
            $item->sisa_belum_cetak = max(0, $qty - $totalCetak);
            $item->progress_cetak_persen = $qty > 0
                ? min(100, round(($totalCetak / $qty) * 100, 1))
                : 0;
        }

        $allMesin = Cache::remember(
            'master_mesin_all',
            3600,
            fn () => MasterMesin::all()
        );

        $mesinByTipeId = $allMesin->groupBy('tipe_mesin_id');
        $mesinByTipeNama = $allMesin->groupBy(function ($m) {
            return trim((string) $m->tipe_mesin);
        });

        $tipeMesinGroups = [];

        foreach ($allItems as $spkItem) {
            $produk = $spkItem->produk;
            if (!$produk) {
                continue;
            }
            $qtyTotal   = (int) ($spkItem->jumlah ?? 0);
            $totalAmbil = (int) ($queueTotalsByItemId[$spkItem->id] ?? 0);
            $totalCetak = (int) ($printTotalsByItemId[$spkItem->id] ?? 0);

            $ambilFull  = $qtyTotal > 0 && $totalAmbil >= $qtyTotal;
            $cetakFull  = $totalAmbil > 0 && $totalCetak >= $totalAmbil;

            if ($ambilFull && $cetakFull) {
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

                $divisiId = $step['divisi_mesin_id'] ?? null;
                $divisiNama = isset($step['divisi_mesin'])
                    ? trim((string) $step['divisi_mesin'])
                    : null;
                $divisiKeterangan = isset($step['keterangan_divisi'])
                    ? trim((string) $step['keterangan_divisi'])
                    : null;

                if (!$divisiId && !$divisiNama) {
                    continue;
                }

                $tipeKey = $divisiId ?: $divisiNama;
                $tipeLabelDasar = $divisiNama ?: $divisiKeterangan ?: 'Tanpa Tipe';

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

        $userId = (int) (auth()->id() ?? 1);

        $queueRows = SpkItemCetakQueue::query()
            ->with([
                'spkItem.spk.pelanggan:id,nama,email',
                'mesin:id,nama_mesin',
            ])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $queueRowsSorted = $queueRows->sortBy('created_at')->values();
        $printedUsedByItem = [];

        $queueRowsFiltered = $queueRowsSorted->filter(function ($q) use ($printTotalsByItemId, &$printedUsedByItem) {
            $item = $q->spkItem;
            $spk  = $item?->spk;

            if (!$item || !$spk) {
                return false;
            }

            if ($spk->status !== 'manager_approval_order') {
                return false;
            }

            $itemId       = $item->id;
            $qtyDiambil   = (int) ($q->jumlah ?? 0);
            $totalCetakAll = (int) ($printTotalsByItemId[$itemId] ?? 0);

            $alreadyAllocated = $printedUsedByItem[$itemId] ?? 0;
            $remainingForItem = max(0, $totalCetakAll - $alreadyAllocated);
            $printedForQueue = min($qtyDiambil, $remainingForItem);

            $printedUsedByItem[$itemId] = $alreadyAllocated + $printedForQueue;

            $q->computed_printed = $printedForQueue;

            return $printedForQueue < $qtyDiambil;
        })->values();

        $pekerjaanSayaItems = $queueRowsFiltered->map(function ($q) {
            $item            = $q->spkItem;
            $spk             = $item?->spk;
            $qtyDiambil      = (int) ($q->jumlah ?? 0);
            $printedForQueue = (int) ($q->computed_printed ?? 0);
            $progress        = 0;

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