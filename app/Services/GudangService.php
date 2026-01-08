<?php

namespace App\Services;

use App\Exceptions\GudangNotFoundException;
use App\Exceptions\InvalidGudangDataException;
use App\Models\Gudang;
use App\Repositories\Interfaces\GudangRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class GudangService
{
    public function __construct(
        private GudangRepositoryInterface $gudangRepository
    ) {}

    public function createGudang(array $data): Gudang
    {
        $this->validateGudangData($data);

        try {
            DB::beginTransaction();

            $data['kode_gudang'] = $this->generateKodeGudang();

            $gudang = $this->gudangRepository->create($data);

            DB::commit();

            Log::info('Gudang created successfully', [
                'gudang_id' => $gudang->id,
                'kode_gudang' => $gudang->kode_gudang
            ]);

            return $gudang;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Gudang', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidGudangDataException('Gagal membuat gudang: ' . $e->getMessage());
        }
    }

    public function updateGudang(int $id, array $data): Gudang
    {
        $gudang = $this->gudangRepository->find($id);
        if (!$gudang) {
            throw new GudangNotFoundException();
        }

        try {
            DB::beginTransaction();

            $this->gudangRepository->update($id, $data);

            DB::commit();

            Log::info('Gudang updated successfully', ['gudang_id' => $id]);

            return $this->gudangRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Gudang', [
                'gudang_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidGudangDataException('Gagal mengupdate gudang: ' . $e->getMessage());
        }
    }

    public function deleteGudang(int $id): bool
    {
        $gudang = $this->gudangRepository->find($id);
        if (!$gudang) {
            throw new GudangNotFoundException();
        }

        try {
            $result = $this->gudangRepository->delete($id);
            Log::info('Gudang deleted successfully', ['gudang_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Gudang', [
                'gudang_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidGudangDataException('Gagal menghapus gudang');
        }
    }

    public function getGudang(int $id): Gudang
    {
        $gudang = $this->gudangRepository->find($id);
        if (!$gudang) {
            throw new GudangNotFoundException();
        }
        return $gudang;
    }

    public function getPaginatedGudang(int $perPage = 10, array $filters = [])
    {
        return $this->gudangRepository->paginate($perPage, $filters);
    }

    private function generateKodeGudang(): string
    {
        return IdGenerator::generate([
            'table' => 'gudang',
            'field' => 'kode_gudang',
            'length' => 7,
            'prefix' => 'WH-'
        ]);
    }

    private function validateGudangData(array $data): void
    {
        if (empty($data['nama_gudang'])) {
            throw new InvalidGudangDataException('Nama gudang harus diisi');
        }
        if (empty($data['manager'])) {
            throw new InvalidGudangDataException('Manager harus diisi');
        }
    }
}








