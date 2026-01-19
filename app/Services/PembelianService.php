<?php

namespace App\Services;

use App\Exceptions\PembelianNotFoundException;
use App\Exceptions\InvalidPembelianDataException;
use App\Models\Pembelian;
use App\Models\BahanBaku;
use App\Repositories\Interfaces\PembelianRepositoryInterface;
use App\Repositories\Interfaces\PembelianItemRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PembelianService
{
    public function __construct(
        private PembelianRepositoryInterface $pembelianRepository,
        private PembelianItemRepositoryInterface $pembelianItemRepository
    ) {}

    public function createPembelian(array $data): Pembelian
    {
        $this->validatePembelianData($data);

        try {
            DB::beginTransaction();

            $kodePembelian = $this->generateKodePembelian();

            // Load semua bahan baku dalam satu query
            $bahanBakuIds = collect($data['items'])->pluck('bahanbaku_id')->unique()->filter()->values()->all();
            $bahanBakus = BahanBaku::whereIn('id', $bahanBakuIds)->get()->keyBy('id');

            // Hitung subtotal dan items
            $subtotal = 0;
            $items = [];
            foreach ($data['items'] as $item) {
                $diskon = isset($item['diskon_persen']) ? floatval($item['diskon_persen']) : 0;
                $bahanBaku = $bahanBakus->get($item['bahanbaku_id']);
                $jumlah_input = $item['jumlah'];
                $jumlah_utama = $jumlah_input;

                // Konversi satuan jika perlu
                if ($bahanBaku && isset($item['satuan']) && $item['satuan'] && is_array($bahanBaku->konversi_satuan_json)) {
                    foreach ($bahanBaku->konversi_satuan_json as $konv) {
                        if (isset($konv['satuan_dari']) && (string)$konv['satuan_dari'] === (string)$item['satuan']) {
                            $faktor_konversi = isset($konv['jumlah']) ? floatval($konv['jumlah']) : 1;
                            $jumlah_utama = $jumlah_input * $faktor_konversi;
                            break;
                        }
                    }
                }

                $item_subtotal = $item['harga'] * $jumlah_input * (1 - $diskon/100);
                $items[] = [
                    'bahanbaku_id' => $item['bahanbaku_id'],
                    'jumlah' => $jumlah_utama,
                    'satuan' => $item['satuan'] ?? null,
                    'harga' => $item['harga'],
                    'diskon_persen' => $diskon,
                    'subtotal' => $item_subtotal,
                ];
                $subtotal += $item_subtotal;
            }

            // Hitung total
            $diskon_persen = floatval($data['diskon_persen'] ?? 0);
            $jumlah_diskon = intval($data['jumlah_diskon'] ?? 0);
            $biaya_pengiriman = intval($data['biaya_pengiriman'] ?? 0);
            $tarif_pajak = floatval($data['tarif_pajak'] ?? 0);
            $nota_kredit = intval($data['nota_kredit'] ?? 0);
            $biaya_lain = intval($data['biaya_lain'] ?? 0);

            $diskon_total = $jumlah_diskon;
            $dpp = $subtotal - $diskon_total;
            $pajak = round($dpp * ($tarif_pajak/100));
            $total = $dpp + $pajak + $biaya_pengiriman + $biaya_lain - $nota_kredit;

            // Create pembelian
            $pembelian = $this->pembelianRepository->create([
                'kode_pembelian' => $kodePembelian,
                'tanggal_pembelian' => $data['tanggal_pembelian'],
                'pemasok_id' => $data['pemasok_id'],
                'nomor_form' => $data['nomor_form'] ?? null,
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'diskon_persen' => $diskon_persen,
                'biaya_pengiriman' => $biaya_pengiriman,
                'tarif_pajak' => $tarif_pajak,
                'nota_kredit' => $nota_kredit,
                'biaya_lain' => $biaya_lain,
                'total' => $total,
            ]);

            // Create items
            foreach ($items as $item) {
                $this->pembelianItemRepository->createForPembelian($pembelian->id, $item);
            }

            DB::commit();

            Log::info('Pembelian created successfully', [
                'pembelian_id' => $pembelian->id,
                'kode_pembelian' => $pembelian->kode_pembelian
            ]);

            return $pembelian;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Pembelian', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidPembelianDataException('Gagal membuat pembelian: ' . $e->getMessage());
        }
    }

    public function updatePembelian(string $kodePembelian, array $data): Pembelian
    {
        $pembelian = $this->pembelianRepository->findByKode($kodePembelian);
        if (!$pembelian) {
            throw new PembelianNotFoundException();
        }

        try {
            DB::beginTransaction();

            // Load semua bahan baku
            $bahanBakuIds = collect($data['items'])->pluck('bahanbaku_id')->unique()->filter()->values()->all();
            $bahanBakus = BahanBaku::whereIn('id', $bahanBakuIds)->get()->keyBy('id');

            // Hitung subtotal dan items
            $subtotal = 0;
            $items = [];
            foreach ($data['items'] as $item) {
                $diskon = isset($item['diskon_persen']) ? floatval($item['diskon_persen']) : 0;
                $bahanBaku = $bahanBakus->get($item['bahanbaku_id']);
                $jumlah_input = $item['jumlah'];
                $jumlah_utama = $jumlah_input;

                if ($bahanBaku && isset($item['satuan']) && $item['satuan'] && is_array($bahanBaku->konversi_satuan_json)) {
                    foreach ($bahanBaku->konversi_satuan_json as $konv) {
                        if (isset($konv['satuan_dari']) && (string)$konv['satuan_dari'] === (string)$item['satuan']) {
                            $faktor_konversi = isset($konv['jumlah']) ? floatval($konv['jumlah']) : 1;
                            $jumlah_utama = $jumlah_input * $faktor_konversi;
                            break;
                        }
                    }
                }

                $item_subtotal = $item['harga'] * $jumlah_input * (1 - $diskon/100);
                $items[] = [
                    'bahanbaku_id' => $item['bahanbaku_id'],
                    'jumlah' => $jumlah_utama,
                    'satuan' => $item['satuan'] ?? null,
                    'harga' => $item['harga'],
                    'diskon_persen' => $diskon,
                    'subtotal' => $item_subtotal,
                ];
                $subtotal += $item_subtotal;
            }

            // Hitung total
            $diskon_persen = floatval($data['diskon_persen'] ?? 0);
            $jumlah_diskon = intval($data['jumlah_diskon'] ?? 0);
            $biaya_pengiriman = intval($data['biaya_pengiriman'] ?? 0);
            $tarif_pajak = floatval($data['tarif_pajak'] ?? 0);
            $nota_kredit = intval($data['nota_kredit'] ?? 0);
            $biaya_lain = intval($data['biaya_lain'] ?? 0);

            $diskon_total = $jumlah_diskon;
            $dpp = $subtotal - $diskon_total;
            $pajak = round($dpp * ($tarif_pajak/100));
            $total = $dpp + $pajak + $biaya_pengiriman + $biaya_lain - $nota_kredit;

            // Update pembelian
            $this->pembelianRepository->update($pembelian->id, [
                'tanggal_pembelian' => $data['tanggal_pembelian'],
                'pemasok_id' => $data['pemasok_id'],
                'nomor_form' => $data['nomor_form'] ?? null,
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'diskon_persen' => $diskon_persen,
                'biaya_pengiriman' => $biaya_pengiriman,
                'tarif_pajak' => $tarif_pajak,
                'nota_kredit' => $nota_kredit,
                'biaya_lain' => $biaya_lain,
                'total' => $total,
            ]);

            // Delete old items and create new ones
            $this->pembelianItemRepository->deleteByPembelian($pembelian->id);
            foreach ($items as $item) {
                $this->pembelianItemRepository->createForPembelian($pembelian->id, $item);
            }

            DB::commit();

            Log::info('Pembelian updated successfully', ['kode_pembelian' => $kodePembelian]);

            return $this->pembelianRepository->findWithRelations($pembelian->id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Pembelian', [
                'kode_pembelian' => $kodePembelian,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidPembelianDataException('Gagal mengupdate pembelian: ' . $e->getMessage());
        }
    }

    public function deletePembelian(string $kodePembelian): bool
    {
        $pembelian = $this->pembelianRepository->findByKode($kodePembelian);
        if (!$pembelian) {
            throw new PembelianNotFoundException();
        }

        try {
            DB::beginTransaction();

            $this->pembelianItemRepository->deleteByPembelian($pembelian->id);
            $result = $this->pembelianRepository->delete($pembelian->id);

            DB::commit();

            Log::info('Pembelian deleted successfully', ['kode_pembelian' => $kodePembelian]);

            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete Pembelian', [
                'kode_pembelian' => $kodePembelian,
                'error' => $e->getMessage()
            ]);
            throw new InvalidPembelianDataException('Gagal menghapus pembelian');
        }
    }

    public function getPembelianByKode(string $kode): Pembelian
    {
        $pembelian = $this->pembelianRepository->findWithRelations(
            $this->pembelianRepository->findByKode($kode)?->id ?? 0
        );
        if (!$pembelian) {
            throw new PembelianNotFoundException();
        }
        return $pembelian;
    }

    public function getPaginatedPembelian(int $perPage = 10, array $filters = [])
    {
        return $this->pembelianRepository->paginate($perPage, $filters);
    }

    private function generateKodePembelian(): string
    {
        return IdGenerator::generate([
            'table' => 'pembelian',
            'field' => 'kode_pembelian',
            'length' => 12,
            'prefix' => 'PB-' . date('ym') . '-',
            'reset_on_prefix_change' => true
        ]);
    }

    private function validatePembelianData(array $data): void
    {
        if (empty($data['pemasok_id'])) {
            throw new InvalidPembelianDataException('Pemasok harus dipilih');
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new InvalidPembelianDataException('Minimal harus ada 1 item pembelian');
        }

        foreach ($data['items'] as $index => $item) {
            if (empty($item['bahanbaku_id'])) {
                throw new InvalidPembelianDataException("Bahan baku item " . ($index + 1) . " harus dipilih");
            }
            if (empty($item['jumlah']) || $item['jumlah'] < 1) {
                throw new InvalidPembelianDataException("Jumlah item " . ($index + 1) . " harus lebih dari 0");
            }
        }
    }
}










