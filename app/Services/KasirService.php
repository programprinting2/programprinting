<?php

namespace App\Services;
use App\Repositories\Interfaces\SpkRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\SPK;


class KasirService
{
    public function __construct(
        private SpkRepositoryInterface $spkRepository
    ) {}

    public function getSpkBelumLunas(Request $request): LengthAwarePaginator
    {
        $filters = [
            'exclude_status_pembayaran' => 'lunas',
        ] + $request->only('search', 'customer_id');

        return $this->spkRepository->paginate(10, $filters);
    }

    public function getSpkForPayment(int $spkId): SPK
    {
        $spk = SPK::with(['pelanggan', 'pembayaran'])->findOrFail($spkId);
        if ($spk->status !== 'proses_bayar') {
            throw new \InvalidArgumentException('SPK bukan status proses bayar');
        }
        return $spk;
    }

    public function storePayment(SPK $spk, array $data): SpkPembayaran
    {
        return DB::transaction(function () use ($spk, $data) {
            /** @var SPK $locked */
            $locked = SPK::query()->whereKey($spk->id)->lockForUpdate()->firstOrFail();

            $payment = SpkPembayaran::create([
                'spk_id' => $locked->id,
                'jumlah' => $data['jumlah'],
                'metode' => $data['metode'],
                'tanggal' => $data['tanggal'],
                'referensi' => $data['referensi'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $locked->refreshPembayaranSummary();

            // if ($locked->status_pembayaran === 'lunas' && $locked->status === 'proses_bayar') {
            //     $locked->update(['status' => 'proses_produksi']);
            // }

            ActivityLogService::log($locked, 'spk_pembayaran', 'Pembayaran SPK disimpan', 'info');

            return $payment;
        });
    }
}