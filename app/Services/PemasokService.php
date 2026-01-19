<?php

namespace App\Services;

use App\Exceptions\PemasokNotFoundException;
use App\Exceptions\InvalidPemasokDataException;
use App\Models\Pemasok;
use App\Repositories\Interfaces\PemasokRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PemasokService
{
    public function __construct(
        private PemasokRepositoryInterface $pemasokRepository
    ) {}

    public function createPemasok(array $data): Pemasok
    {
        $this->validatePemasokData($data);

        try {
            DB::beginTransaction();

            $data['kode_pemasok'] = $this->generateKodePemasok();
            $data['status'] = $data['status'] === '1' || $data['status'] === 1;
            $data['wajib_pajak'] = isset($data['wajib_pajak']) ? ($data['wajib_pajak'] === '1' || $data['wajib_pajak'] === 1) : false;

            $pemasok = $this->pemasokRepository->create($data);

            DB::commit();

            Log::info('Pemasok created successfully', [
                'pemasok_id' => $pemasok->id,
                'kode_pemasok' => $pemasok->kode_pemasok
            ]);

            return $pemasok;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Pemasok', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidPemasokDataException('Gagal membuat pemasok: ' . $e->getMessage());
        }
    }

    public function updatePemasok(int $id, array $data): Pemasok
    {
        $pemasok = $this->pemasokRepository->find($id);
        if (!$pemasok) {
            throw new PemasokNotFoundException();
        }

        try {
            DB::beginTransaction();

            $data['status'] = $data['status'] === '1' || $data['status'] === 1;
            $data['wajib_pajak'] = isset($data['wajib_pajak']) ? ($data['wajib_pajak'] === '1' || $data['wajib_pajak'] === 1) : false;

            $this->pemasokRepository->update($id, $data);

            DB::commit();

            Log::info('Pemasok updated successfully', ['pemasok_id' => $id]);

            return $this->pemasokRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Pemasok', [
                'pemasok_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidPemasokDataException('Gagal mengupdate pemasok: ' . $e->getMessage());
        }
    }

    public function deletePemasok(int $id): bool
    {
        $pemasok = $this->pemasokRepository->find($id);
        if (!$pemasok) {
            throw new PemasokNotFoundException();
        }

        try {
            $result = $this->pemasokRepository->delete($id);
            Log::info('Pemasok deleted successfully', ['pemasok_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Pemasok', [
                'pemasok_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidPemasokDataException('Gagal menghapus pemasok');
        }
    }

    public function getPemasok(int $id): Pemasok
    {
        $pemasok = $this->pemasokRepository->find($id);
        if (!$pemasok) {
            throw new PemasokNotFoundException();
        }
        return $pemasok;
    }

    public function getPaginatedPemasok(int $perPage = 10, array $filters = [])
    {
        return $this->pemasokRepository->paginate($perPage, $filters);
    }

    private function generateKodePemasok(): string
    {
        return IdGenerator::generate([
            'table' => 'pemasok',
            'field' => 'kode_pemasok',
            'length' => 9,
            'prefix' => 'SUP-'
        ]);
    }

    private function validatePemasokData(array $data): void
    {
        if (empty($data['nama'])) {
            throw new InvalidPemasokDataException('Nama pemasok harus diisi');
        }
    }
}










