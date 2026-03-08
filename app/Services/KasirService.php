<?php

namespace App\Services;
use App\Repositories\Interfaces\SpkRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\SPK;
use App\Models\SpkPembayaran;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;


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

    // public function getSpkForPayment(int $spkId): SPK
    // {
    //     $spk = SPK::with(['pelanggan', 'pembayaran'])->findOrFail($spkId);
    //     if ($spk->status !== 'proses_bayar') {
    //         throw new \InvalidArgumentException('SPK bukan status proses bayar');
    //     }
    //     return $spk;
    // }

    public function storePayment(SPK $spk, array $data): SpkPembayaran
    {
        return DB::transaction(function () use ($spk, $data) {
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

    public function buildInvoiceFromSpk(SPK $spk): array
    {
        $spk->loadMissing(['pelanggan', 'items']);

        $groups = $spk->invoice_groups ?? [];
        if (!is_array($groups)) {
            $groups = [];
        }

        $items = [];
        $subtotal = 0;

        $itemsById = [];
        foreach ($spk->items as $item) {
            $itemsById[$item->id] = $item;
        }

        $groupedItemIds = [];
        foreach ($groups as $group) {
            $qty = (float) ($group['qty'] ?? 0);
            $price = (float) ($group['price'] ?? 0);
            $total = $qty * $price;

            $items[] = [
                'deskripsi' => '[Group] ' . ($group['name'] ?? 'Group'),
                'jumlah' => $qty,
                'harga' => $price,
                'pajak' => '0%',
                'diskon' => '0%',
                'total' => $total,
            ];

            $subtotal += $total;

            foreach ($group['itemIds'] ?? [] as $id) {
                $groupedItemIds[] = $id;
            }
        }

        $groupedItemIds = array_unique($groupedItemIds);
        foreach ($spk->items as $item) {
            if (in_array($item->id, $groupedItemIds, true)) {
                continue;
            }

            $qty = (float) $item->jumlah;
            $totalBiaya = (float) $item->total_biaya;
            $price = $qty > 0 ? $totalBiaya / $qty : $totalBiaya;

            $items[] = [
                'deskripsi' => $item->nama_produk,
                'jumlah' => $qty,
                'harga' => $price,
                'pajak' => '0%',
                'diskon' => '0%',
                'total' => $totalBiaya,
            ];

            $subtotal += $totalBiaya;
        }

        $pajak = 0;
        $diskon = 0;
        $total = $subtotal + $pajak - $diskon;

        return [
            'no' => 'INV-' . $spk->nomor_spk,
            'status' => 'pending',
            'tanggal' => now()->format('d M Y'),
            'jatuh_tempo' => now()->addDays(14)->format('d M Y'),
            'spk_no' => $spk->nomor_spk,
            'customer' => [
                'nama' => $spk->pelanggan->nama ?? '-',
                'email' => $spk->pelanggan->email ?? '-',
                'telp' => $spk->pelanggan->no_telp ?? '-',
                'catatan' => $spk->catatan ?? '',
            ],
            'ringkasan' => [
                'subtotal' => $subtotal,
                'pajak' => $pajak,
                'diskon' => $diskon,
                'total' => $total,
                'dibayar' => (float) $spk->total_dibayar,
                'sisa' => max(0.0, $total - (float) $spk->total_dibayar),
            ],
            'items' => $items,
            'pembayaran' => [], 
        ];
    }
}