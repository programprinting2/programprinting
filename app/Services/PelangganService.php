<?php

namespace App\Services;

use App\Exceptions\PelangganNotFoundException;
use App\Exceptions\InvalidPelangganDataException;
use App\Models\Pelanggan;
use App\Repositories\Interfaces\PelangganRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PelangganService
{
    public function __construct(
        private PelangganRepositoryInterface $pelangganRepository
    ) {}

    /**
     * Create new Pelanggan
     */
    public function createPelanggan(array $data): Pelanggan
    {
        $this->validatePelangganData($data);

        try {
            DB::beginTransaction();

            // Generate kode pelanggan
            $data['kode_pelanggan'] = $this->generateKodePelanggan();

            // Convert status
            $data['status'] = $data['status'] === '1' || $data['status'] === 1;
            $data['wajib_pajak'] = isset($data['wajib_pajak']) ? ($data['wajib_pajak'] === '1' || $data['wajib_pajak'] === 1) : false;

            // Handle data_lain
            if (isset($data['data_lain'])) {
                if (isset($data['data_lain']['batas_total_piutang_nilai'])) {
                    $data['data_lain']['batas_total_piutang'] = $data['data_lain']['batas_total_piutang_nilai'];
                    unset($data['data_lain']['batas_total_piutang_nilai']);
                }
            }

            $pelanggan = $this->pelangganRepository->create($data);

            DB::commit();

            Log::info('Pelanggan created successfully', [
                'pelanggan_id' => $pelanggan->id,
                'kode_pelanggan' => $pelanggan->kode_pelanggan
            ]);

            return $pelanggan;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Pelanggan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidPelangganDataException('Gagal membuat pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Update existing Pelanggan
     */
    public function updatePelanggan(int $id, array $data): Pelanggan
    {
        $pelanggan = $this->pelangganRepository->find($id);
        if (!$pelanggan) {
            throw new PelangganNotFoundException();
        }

        try {
            DB::beginTransaction();

            // Convert status
            $data['status'] = $data['status'] === '1' || $data['status'] === 1;
            $data['wajib_pajak'] = isset($data['wajib_pajak']) ? ($data['wajib_pajak'] === '1' || $data['wajib_pajak'] === 1) : false;

            // Handle data_lain
            if (isset($data['data_lain'])) {
                if (isset($data['data_lain']['batas_total_piutang_nilai'])) {
                    $data['data_lain']['batas_total_piutang'] = $data['data_lain']['batas_total_piutang_nilai'];
                    unset($data['data_lain']['batas_total_piutang_nilai']);
                }
            }

            $this->pelangganRepository->update($id, $data);

            DB::commit();

            Log::info('Pelanggan updated successfully', ['pelanggan_id' => $id]);

            return $this->pelangganRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Pelanggan', [
                'pelanggan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidPelangganDataException('Gagal mengupdate pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Delete Pelanggan
     */
    public function deletePelanggan(int $id): bool
    {
        $pelanggan = $this->pelangganRepository->find($id);
        if (!$pelanggan) {
            throw new PelangganNotFoundException();
        }

        try {
            $result = $this->pelangganRepository->delete($id);
            
            Log::info('Pelanggan deleted successfully', ['pelanggan_id' => $id]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Pelanggan', [
                'pelanggan_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidPelangganDataException('Gagal menghapus pelanggan');
        }
    }

    /**
     * Get Pelanggan by ID
     */
    public function getPelanggan(int $id): Pelanggan
    {
        $pelanggan = $this->pelangganRepository->find($id);
        if (!$pelanggan) {
            throw new PelangganNotFoundException();
        }
        return $pelanggan;
    }

    /**
     * Get paginated Pelanggan list with filters
     */
    public function getPaginatedPelanggan(int $perPage = 10, array $filters = [])
    {
        return $this->pelangganRepository->paginate($perPage, $filters);
    }

    /**
     * Generate unique kode pelanggan
     */
    private function generateKodePelanggan(): string
    {
        return IdGenerator::generate([
            'table' => 'pelanggan',
            'field' => 'kode_pelanggan',
            'length' => 9,
            'prefix' => 'CUST-'
        ]);
    }

    /**
     * Validate Pelanggan data
     */
    private function validatePelangganData(array $data): void
    {
        if (empty($data['nama'])) {
            throw new InvalidPelangganDataException('Nama pelanggan harus diisi');
        }
    }
}








