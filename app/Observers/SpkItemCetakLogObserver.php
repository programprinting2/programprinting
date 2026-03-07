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
        // Pastikan relasi item + SPK ada
        $item = $log->spkItem()->with('spk')->first();
        if (! $item || ! $item->spk) {
            return;
        }

        $spk = $item->spk;

        // Hanya auto-shift kalau SPK memang sedang di tahap operator cetak (dari Manager Order)
        // (status awal yang muncul di halaman operator-cetak Anda)
        if ($spk->status !== 'manager_approval_order') {
            return;
        }

        // Muat semua item + log cetaknya
        $spk->loadMissing('items.cetakLogs');

        // Cek apakah semua item sudah full tercetak
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

        // Semua item sudah full tercetak → geser status ke tahap berikutnya
        // Tahap berikutnya dari Manager Order untuk alur Anda adalah 'operator_cetak'
        $spk->update([
            'status' => 'operator_cetak',
        ]);
        // Activity log status akan otomatis dicatat oleh SpkObserver::updated()
    }
}