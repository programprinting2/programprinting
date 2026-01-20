<?php

namespace App\Jobs;

use App\Models\MasterMesin;
use App\Models\Produk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateProdukModalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes
    public $backoff = [60, 300, 600];

    private int $mesinId;
    private int $batchSize;

    public function __construct(int $mesinId, int $batchSize = 20)
    {
        $this->mesinId = $mesinId;
        $this->batchSize = $batchSize;
    }

    public function handle(): void
    {
        $mesin = MasterMesin::find($this->mesinId);
        if (!$mesin) {
            Log::warning('Mesin not found for recalc job', ['mesin_id' => $this->mesinId]);
            return;
        }

        Log::info('Starting recalc job for mesin', [
            'mesin_id' => $this->mesinId,
            'mesin_name' => $mesin->nama_mesin
        ]);

        $totalProcessed = 0;

        // Process dalam batch
        Produk::where('needs_recalc', true)
            ->whereRaw('? = ANY(mesin_ids)', [$this->mesinId])
            ->chunk($this->batchSize, function ($produkBatch) use (&$totalProcessed) {
                foreach ($produkBatch as $produk) {
                    try {
                        $produk->load('bahanBakus');
                    
                        $produk->refreshParameterModalPrices();
                        $produk->updateTotalModal();
                        $produk->update(['needs_recalc' => false]);
                        $totalProcessed++;
                        
                        Log::debug('Processed produk recalc', [
                            'produk_id' => $produk->id,
                            'produk_name' => $produk->nama_produk,
                            'new_total' => $produk->total_modal_keseluruhan
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Failed to recalc produk', [
                            'produk_id' => $produk->id,
                            'error' => $e->getMessage()
                        ]);
                        $produk->update(['needs_recalc' => false]);
                    }
                }
            });

        Log::info('Completed recalc job for mesin', [
            'mesin_id' => $this->mesinId,
            'total_processed' => $totalProcessed
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Recalc job failed', [
            'mesin_id' => $this->mesinId,
            'error' => $exception->getMessage()
        ]);
    }
}