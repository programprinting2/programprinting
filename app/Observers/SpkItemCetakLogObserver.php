<?php

namespace App\Observers;

use App\Events\SpkItemUpdated;
use App\Events\SpkUpdated;
use App\Models\SPKItem;
use App\Models\SpkItemCetakLog;

class SpkItemCetakLogObserver
{
    public function created(SpkItemCetakLog $log): void
    {
        $this->handleLogChange($log);
    }

    public function updated(SpkItemCetakLog $log): void
    {
        $this->handleLogChange($log);
    }

    public function deleted(SpkItemCetakLog $log): void
    {
        $this->handleLogChange($log);
    }

    public function restored(SpkItemCetakLog $log): void
    {
        $this->handleLogChange($log);
    }

    protected function handleLogChange(SpkItemCetakLog $log): void
    {
        $item = $log->spkItem()->with(['spk', 'cetakLogs', 'produk'])->first();

        if (!$item || !$item->spk) {
            return;
        }

        $spk = $item->spk;
        $spk->loadMissing(['items.produk', 'items.cetakLogs']);

        $allMesinIds = $spk->items
            ->flatMap(fn (SPKItem $spkItem) => $spkItem->cetakLogs->pluck('mesin_id'))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $mesinTypeByMesinId = $allMesinIds->isEmpty()
            ? collect()
            : \App\Models\MasterMesin::query()
                ->whereIn('id', $allMesinIds)
                ->get(['id', 'tipe_mesin'])
                ->mapWithKeys(fn ($m) => [
                    (int) $m->id => $this->normalizeMesinType($m->tipe_mesin ?? null),
                ]);

        $workflowByItemId = [];
        foreach ($spk->items as $spkItem) {
            $workflowByItemId[(int) $spkItem->id] = $this->buildItemWorkflowProgress($spkItem, $mesinTypeByMesinId);
        }

        $itemWorkflow = $workflowByItemId[(int) $item->id] ?? [
            'active_progress_pct' => 0.0,
            'active_remaining_qty' => 0,
            'is_done' => false,
            'steps' => [],
        ];

        $progressPct = (float) ($itemWorkflow['active_progress_pct'] ?? 0.0);
        $remaining = (int) ($itemWorkflow['active_remaining_qty'] ?? 0);
        $isDone = (bool) ($itemWorkflow['is_done'] ?? false);

        $progressColor = $progressPct >= 100
            ? 'bg-success'
            : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');

        event(new SpkItemUpdated(
            spkId: (int) $spk->id,
            spkItemId: (int) $item->id,
            progressPct: $progressPct,
            progressColor: $progressColor,
            remaining: $remaining,
            satuan: (string) ($item->satuan ?? ''),
            isDone: $isDone,
        ));

        $totalEligibleStepQty = 0.0;
        $weightedStepProgress = 0.0;

        foreach ($workflowByItemId as $wf) {
            foreach (($wf['steps'] ?? []) as $step) {
                $eligible = (float) ($step['eligible_qty'] ?? 0);
                if ($eligible <= 0) {
                    continue;
                }

                $pct = (float) ($step['progress_pct'] ?? 0);
                $totalEligibleStepQty += $eligible;
                $weightedStepProgress += ($eligible * $pct);
            }
        }

        $spkProgressPct = $totalEligibleStepQty > 0
            ? (int) round($weightedStepProgress / $totalEligibleStepQty)
            : 0;

        $spkProgressColor = $spkProgressPct >= 100
            ? 'bg-success'
            : ($spkProgressPct >= 50 ? 'bg-warning' : 'bg-primary');

        event(new SpkUpdated(
            spkId: (int) $spk->id,
            spkProgressPct: $spkProgressPct,
            spkProgressColor: $spkProgressColor,
            status: (string) $spk->status,
            statusPembayaran: $spk->status_pembayaran,
        ));

        $allDone = collect($workflowByItemId)
            ->every(fn (array $wf): bool => (bool) ($wf['is_done'] ?? false));

        if ($allDone && $spk->status === 'manager_approval_order') {
            $spk->update([
                'status' => 'operator_cetak',
            ]);

            event(new SpkUpdated(
                spkId: (int) $spk->id,
                status: (string) $spk->status,
                statusPembayaran: $spk->status_pembayaran,
            ));
        }
    }

    private function normalizeMesinType(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function buildItemWorkflowProgress(SPKItem $item, \Illuminate\Support\Collection $mesinTypeByMesinId): array
    {
        $item->loadMissing(['produk:id,alur_produksi_json', 'cetakLogs:spk_item_id,mesin_id,jumlah,deleted_at']);

        $qtyPesanan = (int) ($item->jumlah ?? 0);
        $alur = $item->produk?->alur_produksi_json ?? [];

        if (!is_array($alur) || empty($alur)) {
            return [
                'active_progress_pct' => 0.0,
                'active_remaining_qty' => $qtyPesanan,
                'active_step_index' => 0,
                'active_step_total' => 0,
                'is_done' => false,
                'steps' => [],
                'aggregate_pct' => 0.0,
            ];
        }

        $activeLogs = $item->cetakLogs->whereNull('deleted_at')->values();

        $printedByStepType = [];
        foreach ($activeLogs as $log) {
            $mesinId = (int) ($log->mesin_id ?? 0);
            $stepType = $mesinTypeByMesinId[$mesinId] ?? '';
            if ($stepType === '') {
                continue;
            }

            $printedByStepType[$stepType] = (int) ($printedByStepType[$stepType] ?? 0) + (int) ($log->jumlah ?? 0);
        }

        $steps = [];
        $eligibleQty = $qtyPesanan;
        $stepTotal = count($alur);

        foreach ($alur as $idx => $step) {
            if (!is_array($step)) {
                continue;
            }

            $stepType = $this->normalizeMesinType($step['divisi_mesin'] ?? '');
            if ($stepType === '') {
                continue;
            }

            $printedQty = (int) ($printedByStepType[$stepType] ?? 0);
            $remainingQty = max(0, $eligibleQty - $printedQty);
            $progressPct = $eligibleQty > 0
                ? min(100, round(($printedQty / $eligibleQty) * 100, 1))
                : 0.0;

            $steps[] = [
                'step_index' => $idx + 1,
                'step_total' => $stepTotal,
                'eligible_qty' => $eligibleQty,
                'printed_qty' => $printedQty,
                'remaining_qty' => $remainingQty,
                'progress_pct' => $progressPct,
            ];

            $eligibleQty = $printedQty;
        }

        $activeStep = collect($steps)->first(fn ($s) =>
            (int) ($s['eligible_qty'] ?? 0) > 0 && (int) ($s['remaining_qty'] ?? 0) > 0
        );

        if (!$activeStep) {
            $activeStep = !empty($steps) ? end($steps) : null;
        }

        $totalEligibleAllSteps = collect($steps)->sum(fn ($s) => (float) ($s['eligible_qty'] ?? 0));
        $weighted = collect($steps)->sum(fn ($s) =>
            ((float) ($s['eligible_qty'] ?? 0)) * ((float) ($s['progress_pct'] ?? 0))
        );

        $aggregatePct = $totalEligibleAllSteps > 0
            ? round($weighted / $totalEligibleAllSteps, 1)
            : 0.0;

        $activeRemaining = (int) ($activeStep['remaining_qty'] ?? $qtyPesanan);
        $activeEligible = (int) ($activeStep['eligible_qty'] ?? 0);
        $activeIndex = (int) ($activeStep['step_index'] ?? 0);
        $activeTotal = (int) ($activeStep['step_total'] ?? 0);

        $isDone = $activeTotal > 0
            && $activeIndex === $activeTotal
            && $activeEligible > 0
            && $activeRemaining <= 0;

        return [
            'active_progress_pct' => (float) ($activeStep['progress_pct'] ?? 0.0),
            'active_remaining_qty' => $activeRemaining,
            'active_step_index' => $activeIndex,
            'active_step_total' => $activeTotal,
            'is_done' => $isDone,
            'steps' => $steps,
            'aggregate_pct' => $aggregatePct,
        ];
    }
}