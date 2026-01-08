<?php

namespace App\Services;

use App\Exceptions\RakNotFoundException;
use App\Exceptions\InvalidRakDataException;
use App\Models\Rak;
use App\Repositories\Interfaces\RakRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class RakService
{
    public function __construct(
        private RakRepositoryInterface $rakRepository
    ) {}

    public function createRak(array $data): Rak
    {
        $this->validateRakData($data);

        try {
            DB::beginTransaction();

            $data['kode_rak'] = $this->generateKodeRak();

            $rak = $this->rakRepository->create($data);

            DB::commit();

            Log::info('Rak created successfully', [
                'rak_id' => $rak->id,
                'kode_rak' => $rak->kode_rak
            ]);

            return $rak;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Rak', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidRakDataException('Gagal membuat rak: ' . $e->getMessage());
        }
    }

    public function updateRak(int $id, array $data): Rak
    {
        $rak = $this->rakRepository->find($id);
        if (!$rak) {
            throw new RakNotFoundException();
        }

        try {
            DB::beginTransaction();

            $this->rakRepository->update($id, $data);

            DB::commit();

            Log::info('Rak updated successfully', ['rak_id' => $id]);

            return $this->rakRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Rak', [
                'rak_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidRakDataException('Gagal mengupdate rak: ' . $e->getMessage());
        }
    }

    public function deleteRak(int $id): bool
    {
        $rak = $this->rakRepository->find($id);
        if (!$rak) {
            throw new RakNotFoundException();
        }

        try {
            $result = $this->rakRepository->delete($id);
            Log::info('Rak deleted successfully', ['rak_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Rak', [
                'rak_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidRakDataException('Gagal menghapus rak');
        }
    }

    public function getRak(int $id): Rak
    {
        $rak = $this->rakRepository->find($id);
        if (!$rak) {
            throw new RakNotFoundException();
        }
        return $rak;
    }

    public function getPaginatedRak(int $perPage = 10, array $filters = [])
    {
        return $this->rakRepository->paginate($perPage, $filters);
    }

    private function generateKodeRak(): string
    {
        return IdGenerator::generate([
            'table' => 'rak',
            'field' => 'kode_rak',
            'length' => 7,
            'prefix' => 'R-',
        ]);
    }

    private function validateRakData(array $data): void
    {
        if (empty($data['gudang_id'])) {
            throw new InvalidRakDataException('Gudang harus dipilih');
        }
        if (empty($data['nama_rak'])) {
            throw new InvalidRakDataException('Nama rak harus diisi');
        }
    }
}








