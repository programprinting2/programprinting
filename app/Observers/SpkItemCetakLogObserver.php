<?php

namespace App\Observers;

use App\Models\SpkItemCetakLog;
use App\Models\SPKItem;

class SpkItemCetakLogObserver
{
    /**
     * Dipanggil setiap kali ada progress cetak baru (log baru dibuat).
     */
    public function created(SpkItemCetakLog $log): void
    {
        $item = $log->spkItem()->with('spk')->first();
        if (! $item || ! $item->spk) {
            return;
        }

        $spk = $item->spk;

        if ($spk->status !== 'manager_approval_order') {
            return;
        }

        $spk->loadMissing('items.cetakLogs');

        $allDone = $spk->items->every(function (SPKItem $it): bool {
            $qty = (int) ($it->jumlah ?? 0);
            if ($qty <= 0) {
                return false;
            }

            // Jika Anda sudah menambahkan accessor is_cetak_selesai di SPKItem, bisa pakai:
            // return $it->is_cetak_selesai;

            $printed = (int) $it->cetakLogs->sum('jumlah');

            return $printed >= $qty;
        });

        if (! $allDone) {
            return;
        }

        $spk->update([
            'status' => 'operator_cetak',
        ]);
    }
}