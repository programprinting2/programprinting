<?php

namespace App\Services;

use App\Exceptions\SpkCreationException;
use App\Exceptions\SpkNotFoundException;
use App\Exceptions\InvalidSpkDataException;
use App\Models\SPK;
use App\Models\SPKItem;
use App\Repositories\Interfaces\SpkRepositoryInterface;
use App\Repositories\Interfaces\SpkItemRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class SpkService
{
    public function __construct(
        private SpkRepositoryInterface $spkRepository,
        private SpkItemRepositoryInterface $spkItemRepository
    ) {}

    /**
     * Create new SPK with items
     */
    public function createSpk(array $data): SPK
    {
        $this->validateSpkData($data);

        try {
            DB::beginTransaction();

            // Generate nomor SPK
            $nomorSpk = $this->generateNomorSpk();

            // Prepare SPK data
            $spkData = [
                'nomor_spk' => $nomorSpk,
                'tanggal_spk' => $data['tanggal_spk'],
                'pelanggan_id' => $data['pelanggan_id'],
                'status' => $data['status'] ?? 'verifikasi',
                'catatan' => $data['catatan'] ?? null,
                'created_by' => '1' //auth()->id(),
            ];

            // Create SPK
            $spk = $this->spkRepository->create($spkData);

            // Create SPK Items
            foreach ($data['items'] as $itemData) {
                $this->createSpkItem($spk->id, $itemData);
            }

            // Update total biaya
            $spk->updateTotalBiaya();

            DB::commit();

            Log::info('SPK created successfully', [
                'spk_id' => $spk->id,
                'nomor_spk' => $spk->nomor_spk,
                'pelanggan_id' => $spk->pelanggan_id
            ]);

            return $spk;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create SPK', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new SpkCreationException(['general' => 'Gagal membuat SPK: ' . $e->getMessage()]);
        }
    }

    /**
     * Update existing SPK
     */
    public function updateSpk(int $id, array $data): SPK
    {
        $spk = $this->spkRepository->find($id);
        if (!$spk) {
            throw new SpkNotFoundException();
        }

        $this->validateSpkUpdateData($data, $spk);

        try {
            DB::beginTransaction();

            $updateData = [
                'tanggal_spk' => $data['tanggal_spk'],
                'pelanggan_id' => $data['pelanggan_id'],
                'catatan' => $data['catatan'] ?? null,
                'updated_by' => auth()->id(),
            ];

            $this->spkRepository->update($id, $updateData);

            DB::commit();

            Log::info('SPK updated successfully', [
                'spk_id' => $id,
                'pelanggan_id' => $data['pelanggan_id']
            ]);

            return $this->spkRepository->findWithRelations($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update SPK', [
                'spk_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidSpkDataException('Gagal mengupdate SPK: ' . $e->getMessage());
        }
    }

    /**
     * Delete SPK
     */
    public function deleteSpk(int $id): bool
    {
        $spk = $this->spkRepository->find($id);
        if (!$spk) {
            throw new SpkNotFoundException();
        }

        try {
            $result = $this->spkRepository->delete($id);
            
            Log::info('SPK deleted successfully', ['spk_id' => $id]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete SPK', [
                'spk_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidSpkDataException('Gagal menghapus SPK');
        }
    }

    /**
     * Get SPK with full relations
     */
    public function getSpkWithRelations(int $id): SPK
    {
        $spk = $this->spkRepository->findWithRelations($id);
        if (!$spk) {
            throw new SpkNotFoundException();
        }
        return $spk;
    }

    /**
     * Get paginated SPK list
     */
    public function getPaginatedSpk(int $perPage = 10)
    {
        return $this->spkRepository->paginate($perPage);
    }

    /**
     * Create SPK Item
     */
    private function createSpkItem(int $spkId, array $itemData): SPKItem
    {
        $item = $this->spkItemRepository->createForSpk($spkId, $itemData);
        $item->updateTotalBiaya();
        return $item;
    }

    /**
     * Generate unique SPK number
     */
    private function generateNomorSpk(): string
    {
        return IdGenerator::generate([
            'table' => 'spk',
            'field' => 'nomor_spk',
            'length' => 13,
            'prefix' => 'SPK-' . date('ym') . '-',
            'reset_on_prefix_change' => true
        ]);
    }

    /**
     * Validate SPK creation data
     */
    private function validateSpkData(array $data): void
    {
        if (empty($data['pelanggan_id'])) {
            throw new InvalidSpkDataException('Pelanggan ID harus diisi');
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new InvalidSpkDataException('Minimal harus ada 1 item');
        }

        foreach ($data['items'] as $index => $item) {
            if (empty($item['nama_produk'])) {
                throw new InvalidSpkDataException("Nama produk item " . ($index + 1) . " harus diisi");
            }
            if (empty($item['jumlah']) || $item['jumlah'] < 1) {
                throw new InvalidSpkDataException("Jumlah item " . ($index + 1) . " harus lebih dari 0");
            }
        }
    }

    /**
     * Validate SPK update data
     */
    private function validateSpkUpdateData(array $data, SPK $spk): void
    {
        // Add specific update validations if needed
        // For example, check if SPK can still be updated based on status
        if ($spk->status === 'completed' || $spk->status === 'selesai') {
            throw new InvalidSpkDataException('SPK yang sudah selesai tidak dapat diubah');
        }
    }
}










