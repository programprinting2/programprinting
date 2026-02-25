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
                    $keterangan = 'SPK di-ACC ke Proses Pembayaran';
                    break;
                case 'manager_approval_order':
                    $activity = 'spk_manager_approval_order';
                    $keterangan = 'SPK di-ACC oleh Manager untuk Order';
                    break;
                case 'manager_approval_produksi':
                    $activity = 'spk_manager_approval_produksi';
                    $keterangan = 'SPK di-ACC oleh Manager untuk Produksi';
                    break;
                case 'operator_cetak':
                    $activity = 'spk_operator_cetak';
                    $keterangan = 'SPK sedang dicetak oleh Operator';
                    break;
                case 'finishing_qc':
                    $activity = 'spk_finishing_qc';
                    $keterangan = 'SPK sedang di Finishing / QC';
                    break;
                case 'siap_diambil':
                    $activity = 'spk_siap_diambil';
                    $keterangan = 'SPK siap untuk diambil';
                    break;
                case 'selesai':
                    $activity = 'spk_selesai';
                    $keterangan = 'SPK telah selesai';
                    break;
                default:
                    // Bisa log status lain jika diperlukan
                    break;
            }

            if ($activity) {
                ActivityLogService::log($spk, $activity, $keterangan, 'info');
            }
        }
    }
}
