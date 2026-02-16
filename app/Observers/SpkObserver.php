<?php

namespace App\Observers;

use App\Models\SPK;
use App\Services\ActivityLogService;

class SpkObserver
{
    /**
     * Handle the SPK "updated" event.
     */
    public function updated(SPK $spk): void
    {
        if ($spk->isDirty('status')) {
            $originalStatus = $spk->getOriginal('status');
            $newStatus = $spk->status;

            $activity = '';
            $keterangan = '';

            switch ($newStatus) {
                case 'proses_bayar':
                    $activity = 'spk_acc_proses_bayar';
                    $keterangan = 'SPK di-ACC ke proses pembayaran';
                    break;
                case 'proses_produksi':
                    $activity = 'spk_proses_produksi';
                    $keterangan = 'SPK memulai proses produksi';
                    break;
                case 'sudah_cetak':
                    $activity = 'spk_sudah_cetak';
                    $keterangan = 'SPK selesai dicetak';
                    break;
                case 'siap_antar':
                    $activity = 'spk_siap_antar';
                    $keterangan = 'SPK siap untuk diantar';
                    break;
                default:
                    // Log other status changes if needed, or ignore
                    break;
            }

            if ($activity) {
                ActivityLogService::log($spk, $activity, $keterangan, 'info');
            }
        }
    }
}
