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

    public function getSpkProsesBayar(Request $request): LengthAwarePaginator
    {
        $filters = ['status' => 'proses_bayar'] + $request->only('search', 'customer_id');
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

    //  public function processPayment(SPK $spk, array $data): Pembayaran
    // {
    // }
}