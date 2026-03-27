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
use App\Models\User;
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
            'items.cetakLogs:id,spk_item_id,mesin_id,jumlah,deleted_at,created_at'
        ]);

        $spkMap = $spk->keyBy('id');
        $tipeMesinNamaByMesinId = MasterMesin::query()
            ->get(['id', 'tipe_mesin'])
            ->mapWithKeys(function (MasterMesin $mesin) {
                $normalizedTipeMesin = strtolower(trim((string) ($mesin->tipe_mesin ?? '')));
                return [(int) $mesin->id => $normalizedTipeMesin];
            });

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
                $workflowStepsByMesin = [];
                if (is_array($alur) && !empty($alur)) {
                    $stepPrintedByKey = [];
                    foreach ($item->cetakLogs as $log) {
                        $mesinId = (int) ($log->mesin_id ?? 0);
                        if ($mesinId <= 0) {
                            continue;
                        }

                        $tipeMesinNama = (string) ($tipeMesinNamaByMesinId[$mesinId] ?? '');
                        if ($tipeMesinNama === '') {
                            continue;
                        }

                        $stepLogKey = 'name:'.$tipeMesinNama;
                        $stepPrintedByKey[$stepLogKey] = (int) ($stepPrintedByKey[$stepLogKey] ?? 0) + (int) ($log->jumlah ?? 0);
                    }

                    $eligibleQtyForStep = (int) ($item->jumlah ?? 0);
                    $totalStepCount = count($alur);

                    foreach ($alur as $index => $step) {
                        if (!is_array($step)) {
                            continue;
                        }

                        $mesinId   = $step['divisi_mesin_id'] ?? null;
                        $mesinNama = isset($step['divisi_mesin'])
                            ? trim((string) $step['divisi_mesin'])
                            : null;
                        if (!$mesinId && !$mesinNama) {
                            continue;
                        }

                        $key = $mesinId ?: $mesinNama;
                        $stepNameKey = 'name:'.strtolower(trim((string) $mesinNama));
                        $stepIdKey = $mesinId ? 'id:'.(int) $mesinId : null;
                        if ($stepIdKey && isset($stepPrintedByKey[$stepIdKey])) {
                            $printedQty = (int) $stepPrintedByKey[$stepIdKey];
                        } else {
                            $printedQty = (int) ($stepPrintedByKey[$stepNameKey] ?? 0);
                        }
                        
                        $remainingQty = max(0, $eligibleQtyForStep - $printedQty);

                        $workflowStepsByMesin[(string) $key] = [
                            'step_index' => $index + 1,
                            'step_total' => $totalStepCount,
                            'eligible_qty' => $eligibleQtyForStep,
                            'printed_qty' => $printedQty,
                            'remaining_qty' => $remainingQty,
                            'progress_pct' => $eligibleQtyForStep > 0
                                ? min(100, round(($printedQty / $eligibleQtyForStep) * 100, 1))
                                : 0,
                        ];

                        if ($eligibleQtyForStep <= 0) {
                            $eligibleQtyForStep = $printedQty;
                            continue;
                        }

                        if (!isset($mesinGroups[$key])) {
                            $mesinGroups[$key] = [
                                'id'   => $mesinId,
                                'nama' => $mesinNama,
                                'kode' => $step['keterangan_divisi'] ?? '',
                                'spk'  => [],
                                'total_eligible' => 0,
                                'total_printed' => 0,
                            ];
                        }

                        $mesinGroups[$key]['spk'][$row->id] = $row;
                        $mesinGroups[$key]['total_eligible'] += $eligibleQtyForStep;
                        $mesinGroups[$key]['total_printed'] += min($printedQty, $eligibleQtyForStep);
                        $eligibleQtyForStep = $printedQty;
                    }
                }

                $item->workflow_steps_by_mesin = $workflowStepsByMesin;

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

    public function managerProduksi(Request $request): View
    {
        // $filters = $request->only(['search', 'customer_id']);
        $filters = $request->only(['search', 'status']);
        $filters['exclude_status'] = 'selesai';
        $filters['sort_status'] = 'manager_approval_order';

        $spk = $this->spkService->getPaginatedSpk(10, $filters);
        $activeTab = $request->string('tab')->toString();
        if ($activeTab === '') {
            $activeTab = 'data-pekerjaan';
        }

        $users = User::query()
            ->with(['mesins:id,nama_mesin'])
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $allMesin = MasterMesin::query()
            ->orderBy('nama_mesin')
            ->get(['id', 'nama_mesin', 'tipe_mesin']);

        $selectedUserId = (int) ($request->integer('user_role_user_id') ?? 0);
        if ($selectedUserId <= 0 && $users->isNotEmpty()) {
            $selectedUserId = (int) ($users->first()->id ?? 0);
        }

        $selectedUser = $users->firstWhere('id', $selectedUserId);
        $selectedUserMesinIds = $selectedUser
            ? $selectedUser->mesins->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [];

        return view('pages.pekerjaan.manager-produksi', compact(
            'spk',
            'users',
            'allMesin',
            'selectedUserId',
            'selectedUserMesinIds',
            'activeTab'
        ));
    }

    public function saveUserMesinRoles(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'mesin_ids' => ['nullable', 'array'],
            'mesin_ids.*' => ['integer', 'exists:mesin,id'],
        ]);

        $user = User::query()->findOrFail((int) $validated['user_id']);
        $mesinIds = collect($validated['mesin_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $user->mesins()->sync($mesinIds);

        return back()
            ->with('success', 'Assignment role mesin berhasil disimpan.')
            ->withInput([
                'tab' => 'assignment-role-mesin',
                'user_role_user_id' => (int) $user->id,
            ]);
    }

    public function getUserAssignedMesinIds(int $userId)
    {
        $user = User::query()->findOrFail($userId);

        $mesinIds = $user->mesins()
            ->pluck('mesin.id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return response()->json([
            'success' => true,
            'mesin_ids' => $mesinIds,
        ]);
    }

    public function operatorCetak(Request $request): View
    {
        $filters['status'] = 'manager_approval_order';

        $spk = $this->spkService->getAllSpk($filters);

        $spk->load([
            // 'pelanggan:id,nama,email',
            'items.spk.pelanggan',
            'items:id,spk_id,produk_id,jumlah,nama_produk,satuan,panjang,lebar,file_pendukung',
            'items.spk:id,nomor_spk,pelanggan_id,tanggal_spk',
            'items.produk:id,nama_produk,kode_produk,alur_produksi_json,is_metric,metric_unit',
            'items.produk.bahanBakus:id,nama_bahan,kode_bahan',
            'items.cetakLogs:id,spk_item_id,user_id,mesin_id,jumlah,created_at',
        ]);

        $allItems = $spk->pluck('items')->flatten();
        $itemIds  = $allItems->pluck('id')->unique();
        $allMesinRaw = Cache::remember(
            'master_mesin_all',
            3600,
            fn () => MasterMesin::all(['id', 'nama_mesin', 'tipe_mesin'])
        );
        $assignedMesinIds = $this->getAssignedMesinIdsForCurrentUser();
        $allMesin = $allMesinRaw->whereIn('id', $assignedMesinIds)->values();
        $assignedMesins = $allMesin
        ->sortBy(fn ($m) => strtolower((string) ($m->nama_mesin ?? '')))
        ->values();
        $tipeNamaByMesinId = $allMesin->mapWithKeys(function ($mesin) {
            return [(int) $mesin->id => $this->normalizeMesinType($mesin->tipe_mesin ?? null)];
        });
        // $poolTipeNamaByMesinId = $allMesinRaw->mapWithKeys(function ($mesin) {
        //     return [(int) $mesin->id => $this->normalizeMesinType($mesin->tipe_mesin ?? null)];
        // });
        $poolTipeNamaByMesinId = $allMesin->mapWithKeys(function ($mesin) {
            return [(int) $mesin->id => $this->normalizeMesinType($mesin->tipe_mesin ?? null)];
        });

        $queueTotalsByItemId = SpkItemCetakQueue::query()
            ->selectRaw('spk_item_id, SUM(jumlah) as total_diambil')
            ->whereIn('spk_item_id', $itemIds)
            ->groupBy('spk_item_id')
            ->pluck('total_diambil', 'spk_item_id');
        $queueByItemAndMesin = SpkItemCetakQueue::query()
            ->selectRaw('spk_item_id, mesin_id, SUM(jumlah) as total_diambil')
            ->whereIn('spk_item_id', $itemIds)
            ->groupBy('spk_item_id', 'mesin_id')
            ->get();

        $printTotalsByItemId = SpkItemCetakLog::query()
            ->selectRaw('spk_item_id, SUM(jumlah) as total_cetak')
            ->whereIn('spk_item_id', $itemIds)
            ->whereNull('deleted_at')
            ->groupBy('spk_item_id')
            ->pluck('total_cetak', 'spk_item_id');
        $printByItemAndMesin = SpkItemCetakLog::query()
            ->selectRaw('spk_item_id, mesin_id, SUM(jumlah) as total_cetak')
            ->whereIn('spk_item_id', $itemIds)
            ->whereNull('deleted_at')
            ->groupBy('spk_item_id', 'mesin_id')
            ->get();

        $queueByItemAndStepType = [];
        foreach ($queueByItemAndMesin as $rowQueue) {
            $itemId = (int) ($rowQueue->spk_item_id ?? 0);
            $mesinId = (int) ($rowQueue->mesin_id ?? 0);
            $tipeNama = $tipeNamaByMesinId[$mesinId] ?? '';
            if ($itemId <= 0 || $tipeNama === '') {
                continue;
            }

            $aggKey = $itemId.'|'.$tipeNama;
            $queueByItemAndStepType[$aggKey] = (int) ($queueByItemAndStepType[$aggKey] ?? 0)
                + (int) ($rowQueue->total_diambil ?? 0);
        }

        $printByItemAndStepType = [];
        foreach ($printByItemAndMesin as $rowPrint) {
            $itemId = (int) ($rowPrint->spk_item_id ?? 0);
            $mesinId = (int) ($rowPrint->mesin_id ?? 0);
            $tipeNama = $tipeNamaByMesinId[$mesinId] ?? '';
            if ($itemId <= 0 || $tipeNama === '') {
                continue;
            }

            $aggKey = $itemId.'|'.$tipeNama;
            $printByItemAndStepType[$aggKey] = (int) ($printByItemAndStepType[$aggKey] ?? 0)
                + (int) ($rowPrint->total_cetak ?? 0);
        }


        $poolQueueByItemAndStepType = [];
        foreach ($queueByItemAndMesin as $rowQueue) {
            $itemId = (int) ($rowQueue->spk_item_id ?? 0);
            $mesinId = (int) ($rowQueue->mesin_id ?? 0);
            $tipeNama = $poolTipeNamaByMesinId[$mesinId] ?? '';
            if ($itemId <= 0 || $tipeNama === '') {
                continue;
            }

            $aggKey = $itemId.'|'.$tipeNama;
            $poolQueueByItemAndStepType[$aggKey] = (int) ($poolQueueByItemAndStepType[$aggKey] ?? 0)
                + (int) ($rowQueue->total_diambil ?? 0);
        }

        $poolPrintByItemAndStepType = [];
        foreach ($printByItemAndMesin as $rowPrint) {
            $itemId = (int) ($rowPrint->spk_item_id ?? 0);
            $mesinId = (int) ($rowPrint->mesin_id ?? 0);
            $tipeNama = $poolTipeNamaByMesinId[$mesinId] ?? '';
            if ($itemId <= 0 || $tipeNama === '') {
                continue;
            }

            $aggKey = $itemId.'|'.$tipeNama;
            $poolPrintByItemAndStepType[$aggKey] = (int) ($poolPrintByItemAndStepType[$aggKey] ?? 0)
                + (int) ($rowPrint->total_cetak ?? 0);
        }

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

        $tipeMesinGroups = $this->buildTipeMesinGroups(
            $allItems,
            $allMesin,
            $queueByItemAndStepType,
            $printByItemAndStepType
        );

        $poolTipeMesinGroups = $this->buildTipeMesinGroups(
            $allItems,
            $allMesin,
            $poolQueueByItemAndStepType,
            $poolPrintByItemAndStepType
        );

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

        $queueRowsFiltered = $queueRowsSorted->filter(function ($q) use ($tipeNamaByMesinId, $printByItemAndStepType, &$printedUsedByItem) {
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
            $stepType = $tipeNamaByMesinId[(int) ($q->mesin_id ?? 0)] ?? '';
            if ($stepType === '') {
                return false;
            }
            $stepAggKey = $itemId.'|'.$stepType;
            $totalCetakStep = (int) ($printByItemAndStepType[$stepAggKey] ?? 0);

            $allocKey = $stepAggKey;
            $alreadyAllocated = $printedUsedByItem[$allocKey] ?? 0;
            $remainingForItem = max(0, $totalCetakStep - $alreadyAllocated);
            $printedForQueue = min($qtyDiambil, $remainingForItem);

            $printedUsedByItem[$allocKey] = $alreadyAllocated + $printedForQueue;

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

        $pekerjaanSayaByMesin = collect($pekerjaanSayaItems)
            ->groupBy(function ($row) {
                return (int) ($row['queue']->mesin_id ?? 0);
            })
            ->map(function ($rows, $mesinId) {
                $first = $rows->first();

                return [
                    'mesin_id' => (int) $mesinId,
                    'mesin_nama' => $first['mesin_nama'] ?? ('Mesin #'.$mesinId),
                    'items' => $rows->values(),
                    'count' => $rows->count(),
                ];
            })
            ->sortBy(fn ($group) => strtolower((string) ($group['mesin_nama'] ?? '')))
            ->values();

        $pekerjaanSayaCount = $pekerjaanSayaItems->count();

        return view('pages.pekerjaan.operator-cetak', [
            'spk'                => $spk,
            'tipeMesinGroups'    => $tipeMesinGroups,
            'poolTipeMesinGroups'=> $poolTipeMesinGroups,
            'pekerjaanSayaItems' => $pekerjaanSayaItems,
            'pekerjaanSayaCount' => $pekerjaanSayaCount,
            'pekerjaanSayaByMesin' => $pekerjaanSayaByMesin,
            'queueTotalsByItemId'=> $queueTotalsByItemId,
            'assignedMesins' => $assignedMesins,
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
        $spkItem->load([
            'produk:id,alur_produksi_json',
            'cetakLogs.user',
            'cetakLogs.mesin:id,nama_mesin',
            'spk.pelanggan',
        ]);

        $qtyPesanan = (int) ($spkItem->jumlah ?? 0);
        $alur = $spkItem->produk?->alur_produksi_json ?? [];

        if (!is_array($alur) || empty($alur)) {
            return response()->json([
                'item' => [
                    'id' => $spkItem->id,
                    'nama_produk' => $spkItem->nama_produk,
                    'qty_pesanan' => $qtyPesanan,
                    'step_index' => 0,
                    'step_total' => 0,
                    'progress_persen' => 0,
                    'sisa' => $qtyPesanan,
                    'eligible_qty' => 0,
                    'printed_qty' => 0,
                    'remaining_qty' => $qtyPesanan,
                ],
                'spk' => [
                    'nomor_spk' => $spkItem->spk->nomor_spk ?? '',
                    'pelanggan' => $spkItem->spk->pelanggan->nama ?? '-',
                ],
                'steps' => [],
                'logs' => $spkItem->cetakLogs()
                    ->withTrashed()
                    ->orderBy('created_at')
                    ->get()
                    ->values()
                    ->map(function ($l) {

                        $time = $l->created_at
                            ? $l->created_at->copy()->timezone('Asia/Jakarta')
                            : null;
                
                        return [
                            'id' => $l->id,
                            'jumlah' => (int) $l->jumlah,
                            'operator' => $l->user->name ?? ('User #'.$l->user_id),
                            'mesin' => optional($l->mesin)->nama_mesin ?? ('Mesin #'.$l->mesin_id),
                            'waktu' => $time ? $time->format('H:i') : '',
                            'tanggal' => $time ? $time->format('d/m/Y') : '',
                            'datetime' => $time ? $time->toIso8601String() : null,
                            'is_batalkan' => $l->trashed(),
                        ];
                    }),
            ]);
        }

        // Ambil log cetak aktif (non-soft-delete), lalu agregasi by tipe mesin.
        $activePrintLogs = SpkItemCetakLog::query()
            ->where('spk_item_id', $spkItem->id)
            ->whereNull('deleted_at')
            ->get(['mesin_id', 'jumlah']);

        $mesinIds = $activePrintLogs
            ->pluck('mesin_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $mesinTypeByMesinId = MasterMesin::query()
            ->whereIn('id', $mesinIds)
            ->get(['id', 'tipe_mesin'])
            ->mapWithKeys(function ($mesin) {
                return [(int) $mesin->id => $this->normalizeMesinType($mesin->tipe_mesin ?? null)];
            });

        $printedByStepType = [];
        foreach ($activePrintLogs as $log) {
            $mesinId = (int) ($log->mesin_id ?? 0);
            $stepType = $mesinTypeByMesinId[$mesinId] ?? '';
            if ($stepType === '') {
                continue;
            }

            $printedByStepType[$stepType] = (int) ($printedByStepType[$stepType] ?? 0) + (int) ($log->jumlah ?? 0);
        }

        $steps = [];
        $eligibleQty = $qtyPesanan;
        $totalSteps = count($alur);

        foreach ($alur as $index => $step) {
            if (!is_array($step)) {
                continue;
            }

            $stepNama = trim((string) ($step['divisi_mesin'] ?? ''));
            $stepType = $this->normalizeMesinType($stepNama);
            if ($stepType === '') {
                continue;
            }

            $printedQty = (int) ($printedByStepType[$stepType] ?? 0);
            $remainingQty = max(0, $eligibleQty - $printedQty);
            $progressPct = $eligibleQty > 0
                ? min(100, round(($printedQty / $eligibleQty) * 100, 1))
                : 0;

            $steps[] = [
                'step_index' => $index + 1,
                'step_total' => $totalSteps,
                'divisi_mesin' => $stepNama,
                'divisi_mesin_id' => $step['divisi_mesin_id'] ?? null,
                'keterangan_divisi' => $step['keterangan_divisi'] ?? '',
                'eligible_qty' => $eligibleQty,
                'printed_qty' => $printedQty,
                'remaining_qty' => $remainingQty,
                'progress_persen' => $progressPct,
            ];

            // Workflow parsial: eligible step berikutnya = output printed step sekarang
            $eligibleQty = $printedQty;
        }

        // Step aktif = step pertama yang masih punya remaining > 0, fallback ke step terakhir.
        $activeStep = collect($steps)->first(function ($s) {
            return (int) ($s['eligible_qty'] ?? 0) > 0
                && (int) ($s['remaining_qty'] ?? 0) > 0;
        });

        if (!$activeStep) {
            $activeStep = !empty($steps) ? end($steps) : null;
        }

        // Progress akumulatif berbobot dari semua step (eligible sebagai bobot).
        $totalEligibleAllSteps = collect($steps)->sum(fn ($s) => (float) ($s['eligible_qty'] ?? 0));
        $weightedProgress = collect($steps)->sum(function ($s) {
            $eligible = (float) ($s['eligible_qty'] ?? 0);
            $pct = (float) ($s['progress_persen'] ?? 0);
            return $eligible * $pct;
        });

        $workflowProgressPct = $totalEligibleAllSteps > 0
            ? round($weightedProgress / $totalEligibleAllSteps, 1)
            : 0.0;

        return response()->json([
            'item' => [
                'id' => $spkItem->id,
                'nama_produk' => $spkItem->nama_produk,
                'qty_pesanan' => $qtyPesanan,

                // Fokus realtime UI: step aktif
                'step_index' => (int) ($activeStep['step_index'] ?? 0),
                'step_total' => (int) ($activeStep['step_total'] ?? 0),
                'progress_persen' => (float) ($activeStep['progress_persen'] ?? 0),
                'sisa' => (int) ($activeStep['remaining_qty'] ?? 0),
                'eligible_qty' => (int) ($activeStep['eligible_qty'] ?? 0),
                'printed_qty' => (int) ($activeStep['printed_qty'] ?? 0),
                'remaining_qty' => (int) ($activeStep['remaining_qty'] ?? 0),

                // Opsional untuk rekap SPK
                'workflow_progress_persen' => $workflowProgressPct,
            ],
            'spk' => [
                'nomor_spk' => $spkItem->spk->nomor_spk ?? '',
                'pelanggan' => $spkItem->spk->pelanggan->nama ?? '-',
            ],
            'steps' => array_values($steps),
            'logs' => $spkItem->cetakLogs()
                ->withTrashed()
                ->orderBy('created_at')
                ->get()
                ->values()
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'jumlah' => (int) $l->jumlah,
                    'operator' => $l->user->name ?? ('User #'.$l->user_id),
                    'mesin' => optional($l->mesin)->nama_mesin ?? ('Mesin #'.$l->mesin_id),
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
        $this->assertMesinAssignedToCurrentUser((int) $mesinId);

        try {
            DB::transaction(function () use ($spkItemId, $jumlah, $mesinId) {
                $userId = (int) auth()->id();

                $item = SPKItem::query()
                    ->lockForUpdate()
                    ->findOrFail($spkItemId);

                $qtyPesanan = (int) ($item->jumlah ?? 0);
                if ($qtyPesanan <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Quantity pesanan tidak valid.',
                    ]);
                }

                if (!$mesinId) {
                    throw ValidationException::withMessages([
                        'mesin_id' => 'Mesin wajib dipilih.',
                    ]);
                }

                $lockedQueueRows = SpkItemCetakQueue::query()
                    ->where('spk_item_id', $item->id)
                    ->where('user_id', $userId)
                    ->where('mesin_id', $mesinId)
                    ->lockForUpdate()
                    ->get(['jumlah']);

                $qtyDiambilDiMesin = (int) $lockedQueueRows->sum('jumlah');

                if ($qtyDiambilDiMesin <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Item ini belum diambil pada mesin yang dipilih.',
                    ]);
                }

                $lockedLogs = SpkItemCetakLog::query()
                    ->where('spk_item_id', $item->id)
                    ->where('user_id', $userId)
                    ->where('mesin_id', $mesinId)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->get(['jumlah']);

                $sudahCetakDiMesin = (int) $lockedLogs->sum('jumlah');
                $sisaBolehCetak = max(0, $qtyDiambilDiMesin - $sudahCetakDiMesin);

                if ($sisaBolehCetak <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Tidak ada sisa qty untuk dicetak pada mesin ini.',
                    ]);
                }

                if ($jumlah > $sisaBolehCetak) {
                    throw ValidationException::withMessages([
                        'jumlah' => "Jumlah melebihi sisa yang bisa dicetak pada mesin ini. Sisa: {$sisaBolehCetak}.",
                    ]);
                }

                SpkItemCetakLog::query()->create([
                    'spk_item_id' => $item->id,
                    'user_id'     => $userId,
                    'mesin_id'    => $mesinId,
                    'jumlah'      => $jumlah,
                ]);

                if ($item->spk) {
                    $keterangan = sprintf(
                        'Cetak %d %s untuk item "%s" pada mesin #%d.',
                        $jumlah,
                        $item->satuan,
                        $item->nama_produk,
                        $mesinId
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
        $this->assertMesinAssignedToCurrentUser((int) $mesinId);
        $userId  = (int) (auth()->id() ?? 1);

        $createdCount = 0;

        DB::transaction(function () use ($ids, $mesinId, $userId, &$createdCount) {
            foreach ($ids as $itemId) {
                $item = SPKItem::lockForUpdate()->find($itemId);

                if (!$item) {
                    continue;
                }

                $qtyDiambil = (int) SpkItemCetakQueue::query()
                    ->where('spk_item_id', $itemId)
                    ->where('user_id', $userId)
                    ->where('mesin_id', $mesinId)
                    ->sum('jumlah');

                if ($qtyDiambil <= 0) {
                    continue;
                }

                $sudahCetak = (int) SpkItemCetakLog::query()
                    ->where('spk_item_id', $itemId)
                    ->where('user_id', $userId)
                    ->where('mesin_id', $mesinId)
                    ->whereNull('deleted_at')
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

                $createdCount++;

                if ($item->spk) {
                    $keterangan = sprintf(
                        'Multi cetak %d %s untuk item "%s".',
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

        if ($createdCount <= 0) {
            return back()->with('warning', 'Tidak ada item yang bisa di cetak complete pada mesin ini.');
        }

        return back()->with('success', 'Multi cetak berhasil: item ditandai selesai.');
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

    public function destroyHistory(SpkItemCetakLog $log): JsonResponse
    {
        $authUserId = (int) auth()->id();

        if ((int) $log->user_id !== $authUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak membatalkan cetak milik operator lain.',
            ], 403);
        }

        DB::transaction(function () use ($log, &$response) {
            $spkItem = $log->spkItem;
            $log->delete();

            $qtyPesanan = (int) ($spkItem->jumlah ?? 0);
            $sudahCetak = (int) $spkItem->cetakLogs()
                ->whereNull('deleted_at')
                ->sum('jumlah');
            $sisa = max(0, $qtyPesanan - $sudahCetak);

            $progressPct = $qtyPesanan > 0
                ? min(100, round(($sudahCetak / $qtyPesanan) * 100))
                : 0;

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
                'success' => true,
                'spk_item_id' => $spkItem->id,
                'jumlah_sudah_cetak' => $sudahCetak,
                'sisa_belum_cetak' => $sisa,
                'progress_persen' => $progressPct,
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
            ->with(['spkItem.spk.pelanggan', 'user', 'mesin:id,nama_mesin'])
            ->orderByDesc('created_at');
            
        $query->whereHas('spkItem.spk', function ($q) {
            $q->where('status', '!=', 'selesai');
        });

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
        $authUserId = (int) auth()->id();

        $data = $logs->getCollection()->map(function ($log) use ($authUserId) {
            $spkItem = $log->spkItem;
            $spk = $spkItem?->spk;
            return [
                'id'              => $log->id,
                'tanggal_label'   => optional($log->created_at)->format('d/m/Y H:i'),
                'nomor_spk'       => $spk?->nomor_spk ?? '-',
                'nama_produk'     => $spkItem?->nama_produk ?? '-',
                'jumlah_formatted' => number_format((int) $log->jumlah, 0, ',', '.'),
                'operator'        => optional($log->user)->name ?? ('User #' . $log->user_id),
                'mesin' => optional($log->mesin)->nama_mesin ?? ('Mesin #'.$log->mesin_id),
                'is_batalkan'      => $log->trashed(),  
                'can_cancel'       => ((int) $log->user_id === $authUserId) && !$log->trashed(),
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
        $this->assertMesinAssignedToCurrentUser($mesinId);
        $ids = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();
        $ambil = (int) $request->input('jumlah');
        $userId = (int) (auth()->id() ?? 1);
        $targetMesinType = $this->normalizeMesinType(
            MasterMesin::query()->whereKey($mesinId)->value('tipe_mesin')
        );
        if ($targetMesinType === '') {
            throw ValidationException::withMessages([
                'mesin_id' => 'Mesin tidak valid atau tidak memiliki tipe mesin.',
            ]);
        }
        $mesinTypeByMesinId = MasterMesin::query()
            ->get(['id', 'tipe_mesin'])
            ->mapWithKeys(function ($mesin) {
                return [(int) $mesin->id => $this->normalizeMesinType($mesin->tipe_mesin ?? null)];
            });

        DB::transaction(function () use ($ids, $mesinId, $ambil, $userId, $targetMesinType, $mesinTypeByMesinId) {
            $items = SPKItem::query()
                ->with('produk:id,alur_produksi_json')
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

                $stepInfo = $this->calculateRemainingTakeForStep(
                    $item,
                    $mesinId,
                    $targetMesinType,
                    $mesinTypeByMesinId
                );
                $remainingTakeForTarget = (int) ($stepInfo['remaining'] ?? 0);

                \Log::info('ambilQueue guard', [
                    'spk_item_id' => $item->id,
                    'mesin_id' => $mesinId,
                    'target_mesin_type' => $targetMesinType,
                    'ambil' => $ambil,
                    'remaining_take_for_target' => $remainingTakeForTarget,
                    'step_info' => $stepInfo,
                ]);

                if ($remainingTakeForTarget <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Step ini belum eligible untuk diambil atau sisa ambil sudah habis.',
                    ]);
                }
                

                if ($ambil > $remainingTakeForTarget) {
                    throw ValidationException::withMessages([
                        'jumlah' => "Jumlah melebihi sisa yang bisa diambil. Sisa yang tersedia: {$remainingTakeForTarget}.",
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
        $this->assertMesinAssignedToCurrentUser($mesinId);

        $userId = (int) (auth()->id() ?? 1);

        $queueIds = collect($request->input('queue_ids', []))
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values();

        $itemIdsFallback = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values();

        DB::transaction(function () use ($queueIds, $itemIdsFallback, $mesinId, $userId) {
            $selectedQueues = SpkItemCetakQueue::query()
                ->where('mesin_id', $mesinId)
                ->where('user_id', $userId)
                ->when(
                    $queueIds->isNotEmpty(),
                    fn ($q) => $q->whereIn('id', $queueIds->all()),
                    fn ($q) => $q->whereIn('spk_item_id', $itemIdsFallback->all()) 
                )
                ->lockForUpdate()
                ->get();

            if ($selectedQueues->isEmpty()) {
                return;
            }

            $selectedQueueIds = $selectedQueues->pluck('id')->map(fn ($id) => (int) $id)->all();
            $targetItemIds = $selectedQueues->pluck('spk_item_id')->map(fn ($id) => (int) $id)->unique()->values();

            $allQueuesForItems = SpkItemCetakQueue::query()
                ->where('mesin_id', $mesinId)
                ->where('user_id', $userId)
                ->whereIn('spk_item_id', $targetItemIds->all())
                ->orderBy('created_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->groupBy('spk_item_id');

            $printedByItem = SpkItemCetakLog::query()
                ->selectRaw('spk_item_id, SUM(jumlah) as total_cetak')
                ->where('user_id', $userId)
                ->where('mesin_id', $mesinId)
                ->whereNull('deleted_at')
                ->whereIn('spk_item_id', $targetItemIds->all())
                ->groupBy('spk_item_id')
                ->pluck('total_cetak', 'spk_item_id');

            foreach ($allQueuesForItems as $itemId => $queues) {
                $remainingPrinted = (int) ($printedByItem[$itemId] ?? 0);

                foreach ($queues as $queue) {
                    $queueQty = (int) ($queue->jumlah ?? 0);

                    $printedForQueue = min($queueQty, max(0, $remainingPrinted));
                    $remainingPrinted -= $printedForQueue;

                    $isSelected = in_array((int) $queue->id, $selectedQueueIds, true);
                    if (!$isSelected) {
                        continue;
                    }

                    $newQty = $printedForQueue; 

                    if ($newQty <= 0) {
                        $queue->delete();
                    } else {
                        $queue->jumlah = $newQty;
                        $queue->save();
                    }
                }
            }
        });

        return back()->with('success', 'Sisa pekerjaan yang belum dicetak berhasil dibatalkan dari antrian Anda.');
    }

    public function ambilQueueAll(MultiAmbilPekerjaanRequest $request): RedirectResponse
    {
        $mesinId = (int) $request->input('mesin_id');
        $this->assertMesinAssignedToCurrentUser($mesinId);
        $ids = collect($request->input('spk_item_ids', []))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        $userId = (int) (auth()->id() ?? 1);

        $targetMesinType = $this->normalizeMesinType(
            MasterMesin::query()->whereKey($mesinId)->value('tipe_mesin')
        );

        if ($targetMesinType === '') {
            throw ValidationException::withMessages([
                'mesin_id' => 'Mesin tidak valid atau tidak memiliki tipe mesin.',
            ]);
        }

        $mesinTypeByMesinId = MasterMesin::query()
            ->get(['id', 'tipe_mesin'])
            ->mapWithKeys(function ($mesin) {
                return [(int) $mesin->id => $this->normalizeMesinType($mesin->tipe_mesin ?? null)];
            });

        $createdCount = 0;

        DB::transaction(function () use (
            $ids,
            $mesinId,
            $userId,
            $targetMesinType,
            $mesinTypeByMesinId,
            &$createdCount
        ) {
            $items = SPKItem::query()
                ->with('produk:id,alur_produksi_json')
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

                $stepInfo = $this->calculateRemainingTakeForStep(
                    $item,
                    $mesinId,
                    $targetMesinType,
                    $mesinTypeByMesinId
                );

                $matched = (bool) ($stepInfo['matched'] ?? false);
                $sisaBolehDiambil = (int) ($stepInfo['remaining'] ?? 0);

                if (!$matched || $sisaBolehDiambil <= 0) {
                    continue;
                }

                SpkItemCetakQueue::query()->create([
                    'spk_item_id' => $item->id,
                    'mesin_id'    => $mesinId,
                    'user_id'     => $userId,
                    'jumlah'      => $sisaBolehDiambil,
                ]);

                $createdCount++;
            }
        });

        if ($createdCount <= 0) {
            return back()->with('warning', 'Tidak ada item eligible untuk diambil pada step mesin ini.');
        }

        return back()->with('success', 'Multi ambil berhasil: semua sisa qty yang bisa diambil sudah dimasukkan ke antrian.');
    }

    private function normalizeMesinType(?string $mesinType): string
    {
        return strtolower(trim((string) $mesinType));
    }

    private function getAssignedMesinIdsForCurrentUser(): array
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return [];
        }

        return $authUser->mesins()
            ->pluck('mesin.id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function assertMesinAssignedToCurrentUser(int $mesinId): void
    {
        if ($mesinId <= 0) {
            throw ValidationException::withMessages([
                'mesin_id' => 'Mesin wajib dipilih.',
            ]);
        }

        $assignedMesinIds = $this->getAssignedMesinIdsForCurrentUser();
        if (!in_array($mesinId, $assignedMesinIds, true)) {
            throw ValidationException::withMessages([
                'mesin_id' => 'Anda tidak memiliki akses untuk mesin yang dipilih.',
            ]);
        }
    }

    private function resolveStepIdentity(array $step): array
    {
        $stepId = (int) ($step['divisi_mesin_id'] ?? 0);

        $rawName = trim((string) ($step['divisi_mesin'] ?? ''));
        $rawAlt  = trim((string) ($step['keterangan_divisi'] ?? ''));
        $rawType = trim((string) ($step['tipe_mesin'] ?? ''));

        $candidates = collect([$rawName, $rawAlt, $rawType])
            ->map(fn ($v) => $this->normalizeMesinType($v))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'step_id' => $stepId,
            'step_name' => $rawName !== '' ? $rawName : ($rawAlt !== '' ? $rawAlt : $rawType),
            'candidates' => $candidates,
        ];
    }

    private function pickActiveWorkflowStepFromBucket($workflowBucket): ?array
    {
        if (!$workflowBucket) {
            return null;
        }

        $list = (is_array($workflowBucket) && array_is_list($workflowBucket))
            ? $workflowBucket
            : [$workflowBucket];

        foreach ($list as $step) {
            if ((int) ($step['remaining_take_qty'] ?? 0) > 0) {
                return $step;
            }
        }

        return !empty($list) ? end($list) : null;
    }

    private function buildTipeMesinGroups(
        \Illuminate\Support\Collection $allItems,
        \Illuminate\Support\Collection $mesins,
        array $queueByItemAndStepType,
        array $printByItemAndStepType
    ): array {
        $mesinById = $mesins->keyBy(function ($m) {
            return (int) $m->id;
        });

        $mesinByTipeNama = $mesins->groupBy(function ($m) {
            return $this->normalizeMesinType($m->tipe_mesin ?? null);
        });

        $mesinByNamaMesin = $mesins->groupBy(function ($m) {
            return $this->normalizeMesinType($m->nama_mesin ?? null);
        });

        $tipeMesinGroups = [];

        foreach ($allItems as $spkItem) {
            $produk = $spkItem->produk;
            if (!$produk) {
                continue;
            }

            $alur = $produk->alur_produksi_json ?? [];
            if (!is_array($alur) || empty($alur)) {
                continue;
            }

            $eligibleQtyForStep = (int) ($spkItem->jumlah ?? 0);
            $workflowStepsByMesin = [];
            $totalStepCount = count($alur);

            foreach ($alur as $index => $step) {
                $identity = $this->resolveStepIdentity($step);
                $stepMesinId = (int) ($identity['step_id'] ?? 0);
                $stepCandidates = $identity['candidates'] ?? [];
                $stepNameLabel = (string) ($identity['step_name'] ?? '');

                if ($stepMesinId <= 0 && empty($stepCandidates)) {
                    continue;
                }

                $stepGroupKey = (string) ($stepMesinId ?: $stepNameLabel);

                $printedQtyStep = 0;
                $queuedQtyStep = 0;
                foreach ($stepCandidates as $candidateType) {
                    $stepAggKey = $spkItem->id.'|'.$candidateType;
                    $printedQtyStep += (int) ($printByItemAndStepType[$stepAggKey] ?? 0);
                    $queuedQtyStep  += (int) ($queueByItemAndStepType[$stepAggKey] ?? 0);
                }

                $remainingTakeQty = max(0, $eligibleQtyForStep - $queuedQtyStep);
                $remainingPrintQty = max(0, $queuedQtyStep - $printedQtyStep);

                $stepPayload = [
                    'step_index' => $index + 1,
                    'step_total' => $totalStepCount,
                    'eligible_qty' => $eligibleQtyForStep,
                    'printed_qty_step' => $printedQtyStep,
                    'queued_qty_step' => $queuedQtyStep,
                    'remaining_take_qty' => $remainingTakeQty,
                    'remaining_print_qty' => $remainingPrintQty,
                    'progress_step_pct' => $eligibleQtyForStep > 0
                        ? min(100, round(($printedQtyStep / $eligibleQtyForStep) * 100, 1))
                        : 0,
                    'step_name' => $stepNameLabel,
                ];

                if (!isset($workflowStepsByMesin[$stepGroupKey])) {
                    $workflowStepsByMesin[$stepGroupKey] = [];
                }
                $workflowStepsByMesin[$stepGroupKey][] = $stepPayload;

                $eligibleQtyForStep = $printedQtyStep;
            }

            $spkItem->workflow_steps_by_mesin = $workflowStepsByMesin;

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
                $workflowBucket = data_get($spkItem, 'workflow_steps_by_mesin.'.(string) $tipeKey);
                $workflowStep = $this->pickActiveWorkflowStepFromBucket($workflowBucket);

                if (!$workflowStep || (int) ($workflowStep['eligible_qty'] ?? 0) <= 0) {
                    continue;
                }

                $tipeLabelDasar = $divisiNama ?: $divisiKeterangan ?: 'Tanpa Tipe';

                if (!isset($tipeMesinGroups[$tipeKey])) {
                    $mesinMatches = collect();

                    $divisiNamaNorm = $this->normalizeMesinType($divisiNama ?? null);
                    $divisiKetNorm  = $this->normalizeMesinType($divisiKeterangan ?? null);

                    if ($divisiId && isset($mesinById[(int) $divisiId])) {
                        $mesinMatches = collect([$mesinById[(int) $divisiId]]);
                    }

                    if ($mesinMatches->isEmpty() && $divisiNamaNorm !== '' && isset($mesinByTipeNama[$divisiNamaNorm])) {
                        $mesinMatches = $mesinByTipeNama[$divisiNamaNorm];
                    }

                    if ($mesinMatches->isEmpty() && $divisiNamaNorm !== '' && isset($mesinByNamaMesin[$divisiNamaNorm])) {
                        $mesinMatches = $mesinByNamaMesin[$divisiNamaNorm];
                    }

                    if ($mesinMatches->isEmpty() && $divisiKetNorm !== '' && isset($mesinByTipeNama[$divisiKetNorm])) {
                        $mesinMatches = $mesinByTipeNama[$divisiKetNorm];
                    }

                    if ($mesinMatches->isEmpty() && $divisiKetNorm !== '' && isset($mesinByNamaMesin[$divisiKetNorm])) {
                        $mesinMatches = $mesinByNamaMesin[$divisiKetNorm];
                    }

                    if ($mesinMatches->isEmpty()) {
                        continue;
                    }

                    $tipeMesinGroups[$tipeKey] = [
                        'tipe_key' => $tipeKey,
                        'tipe_label' => $tipeLabelDasar,
                        'mesin_list' => $mesinMatches->values()->all(),
                        'spk' => [],
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
                            'id' => $bahan->id,
                            'nama' => $bahan->nama_bahan ?? ('Bahan #'.$bahan->id),
                            'kode' => $bahan->kode_bahan ?? '',
                            'items' => [],
                        ];
                    }

                    $uniqKey = 'spk_'.$spkRow->id.'_item_'.$spkItem->id;
                    if (!isset($group['bahanGroups'][$bahanId]['items'][$uniqKey])) {
                        $group['bahanGroups'][$bahanId]['items'][$uniqKey] = [
                            'spk' => $spkRow,
                            'item' => $spkItem,
                            'workflow_step' => $workflowStep,
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

        return $tipeMesinGroups;
    }

    private function calculateRemainingTakeForStep(
        SPKItem $item,
        int $targetMesinId,
        string $targetMesinType,
        $mesinTypeByMesinId
    ): array {
        $produk = $item->produk;
        $alur = $produk?->alur_produksi_json ?? [];
        if (!is_array($alur) || empty($alur)) {
            return [
                'remaining' => 0,
                'eligible' => 0,
                'queued' => 0,
                'printed' => 0,
                'matched' => false,
                'step_index' => 0,
                'step_total' => 0,
                'step_name' => '',
            ];
        }

        $printByStepType = [];
        $printRows = SpkItemCetakLog::query()
            ->selectRaw('mesin_id, SUM(jumlah) as total_cetak')
            ->where('spk_item_id', $item->id)
            ->whereNull('deleted_at')
            ->groupBy('mesin_id')
            ->get();

        foreach ($printRows as $row) {
            $tipe = $mesinTypeByMesinId[(int) ($row->mesin_id ?? 0)] ?? '';
            if ($tipe === '') {
                continue;
            }
            $printByStepType[$tipe] = (int) ($printByStepType[$tipe] ?? 0) + (int) ($row->total_cetak ?? 0);
        }

        $queueByStepType = [];
        $queueRows = SpkItemCetakQueue::query()
            ->selectRaw('mesin_id, SUM(jumlah) as total_diambil')
            ->where('spk_item_id', $item->id)
            ->groupBy('mesin_id')
            ->get();

        foreach ($queueRows as $row) {
            $tipe = $mesinTypeByMesinId[(int) ($row->mesin_id ?? 0)] ?? '';
            if ($tipe === '') {
                continue;
            }
            $queueByStepType[$tipe] = (int) ($queueByStepType[$tipe] ?? 0) + (int) ($row->total_diambil ?? 0);
        }

        $eligibleQty = (int) ($item->jumlah ?? 0);
        $stepTotal = count($alur);

        foreach ($alur as $index => $step) {
            if (!is_array($step)) {
                continue;
            }

            $identity = $this->resolveStepIdentity($step);
            $stepId = (int) ($identity['step_id'] ?? 0);
            $stepName = (string) ($identity['step_name'] ?? '');
            $candidates = $identity['candidates'] ?? [];

            if ($stepId <= 0 && empty($candidates)) {
                continue;
            }

            $printedQty = 0;
            $queuedQty = 0;
            foreach ($candidates as $candidateType) {
                $printedQty += (int) ($printByStepType[$candidateType] ?? 0);
                $queuedQty  += (int) ($queueByStepType[$candidateType] ?? 0);
            }

            $remainingTake = max(0, $eligibleQty - $queuedQty);

            $isTargetStep = ($stepId > 0 && $stepId === $targetMesinId)
                || in_array($targetMesinType, $candidates, true);

            if ($isTargetStep) {
                return [
                    'remaining' => $remainingTake,
                    'eligible' => $eligibleQty,
                    'queued' => $queuedQty,
                    'printed' => $printedQty,
                    'matched' => true,
                    'step_index' => $index + 1,
                    'step_total' => $stepTotal,
                    'step_name' => $stepName,
                ];
            }

            $eligibleQty = $printedQty;
        }

        return [
            'remaining' => 0,
            'eligible' => 0,
            'queued' => 0,
            'printed' => 0,
            'matched' => false,
            'step_index' => 0,
            'step_total' => $stepTotal,
            'step_name' => '',
        ];
    }
}