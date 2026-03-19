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
        $item = $log->spkItem()->with(['spk', 'cetakLogs'])->first();

        if (! $item || ! $item->spk) {
            return;
        }

        $spk = $item->spk;

        // =========================
        // 1) Hitung progress item
        // =========================
        $qty = (int) ($item->jumlah ?? 0);

        $totalCetakItem = (int) SpkItemCetakLog::query()
            ->where('spk_item_id', $item->id)
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $remaining = max(0, $qty - $totalCetakItem);
        $progressPct = $qty > 0
            ? min(100, round(($totalCetakItem / $qty) * 100, 1))
            : 0.0;

        $progressColor = $progressPct >= 100
            ? 'bg-success'
            : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');

        event(new SpkItemUpdated(
            spkId: (int) $spk->id,
            spkItemId: (int) $item->id,
            progressPct: (float) $progressPct,
            progressColor: $progressColor,
            remaining: (int) $remaining,
            satuan: (string) ($item->satuan ?? ''),
            isDone: $remaining <= 0 && $qty > 0,
        ));

        // ==================================
        // 2) Hitung progress total level SPK
        // ==================================
        $spk->loadMissing('items.cetakLogs');

        $totalQty = 0.0;
        $weightedProgress = 0.0;

        foreach ($spk->items as $spkItem) {
            $itemQty = (float) ($spkItem->jumlah ?? 0);
            $printed = (float) $spkItem->cetakLogs
                ->whereNull('deleted_at')
                ->sum('jumlah');

            $itemProgress = $itemQty > 0
                ? min(100, round(($printed / $itemQty) * 100, 1))
                : 0.0;

            $totalQty += $itemQty;
            $weightedProgress += ($itemQty * $itemProgress);
        }

        $spkProgressPct = $totalQty > 0
            ? (int) round($weightedProgress / $totalQty)
            : 0;

        $spkProgressColor = $spkProgressPct >= 100
            ? 'bg-success'
            : ($spkProgressPct >= 50 ? 'bg-warning' : 'bg-primary');

        event(new SpkUpdated(
            spkId: (int) $spk->id,
            spkProgressPct: $spkProgressPct,
            spkProgressColor: $spkProgressColor,
        ));

        // =========================================================
        // 3) Auto advance status jika semua item selesai dicetak
        //    (pertahankan rule lama Anda)
        // =========================================================
        $allDone = $spk->items->every(function (SPKItem $it): bool {
            $itemQty = (int) ($it->jumlah ?? 0);

            if ($itemQty <= 0) {
                return false;
            }

            $printed = (int) SpkItemCetakLog::query()
                ->where('spk_item_id', $it->id)
                ->whereNull('deleted_at')
                ->sum('jumlah');

            return $printed >= $itemQty;
        });

        if ($allDone && $spk->status === 'manager_approval_order') {
            $spk->update([
                'status' => 'operator_cetak',
            ]);
        }
    }
}