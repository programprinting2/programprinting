<?php

namespace App\Services;

use App\Exceptions\KaryawanNotFoundException;
use App\Exceptions\InvalidKaryawanDataException;
use App\Models\Karyawan;
use App\Repositories\Interfaces\KaryawanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class KaryawanService
{
    public function __construct(
        private KaryawanRepositoryInterface $karyawanRepository
    ) {}

    public function createKaryawan(array $data): Karyawan
    {
        $this->validateKaryawanData($data);

        try {
            DB::beginTransaction();

            $data['id_karyawan'] = $this->generateIdKaryawan();
            
            // Ensure JSON fields are arrays
            $data['alamat'] = $data['alamat'] ?? [];
            $data['rekening'] = $data['rekening'] ?? [];
            $data['komponen_gaji'] = $data['komponen_gaji'] ?? [];

            $karyawan = $this->karyawanRepository->create($data);

            DB::commit();

            Log::info('Karyawan created successfully', [
                'karyawan_id' => $karyawan->id,
                'id_karyawan' => $karyawan->id_karyawan
            ]);

            return $karyawan;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Karyawan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidKaryawanDataException('Gagal membuat karyawan: ' . $e->getMessage());
        }
    }

    public function updateKaryawan(int $id, array $data): Karyawan
    {
        $karyawan = $this->karyawanRepository->find($id);
        if (!$karyawan) {
            throw new KaryawanNotFoundException();
        }

        try {
            DB::beginTransaction();

            // Ensure JSON fields are arrays
            $data['alamat'] = $data['alamat'] ?? [];
            $data['rekening'] = $data['rekening'] ?? [];
            $data['komponen_gaji'] = $data['komponen_gaji'] ?? [];

            $this->karyawanRepository->update($id, $data);

            DB::commit();

            Log::info('Karyawan updated successfully', ['karyawan_id' => $id]);

            return $this->karyawanRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Karyawan', [
                'karyawan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidKaryawanDataException('Gagal mengupdate karyawan: ' . $e->getMessage());
        }
    }

    public function deleteKaryawan(int $id): bool
    {
        $karyawan = $this->karyawanRepository->find($id);
        if (!$karyawan) {
            throw new KaryawanNotFoundException();
        }

        try {
            $result = $this->karyawanRepository->delete($id);
            Log::info('Karyawan deleted successfully', ['karyawan_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Karyawan', [
                'karyawan_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidKaryawanDataException('Gagal menghapus karyawan');
        }
    }

    public function getKaryawan(int $id): Karyawan
    {
        $karyawan = $this->karyawanRepository->find($id);
        if (!$karyawan) {
            throw new KaryawanNotFoundException();
        }
        return $karyawan;
    }

    public function getPaginatedKaryawan(int $perPage = 10, array $filters = [])
    {
        return $this->karyawanRepository->paginate($perPage, $filters);
    }

    private function generateIdKaryawan(): string
    {
        return IdGenerator::generate([
            'table' => 'karyawan',
            'field' => 'id_karyawan',
            'length' => 9,
            'prefix' => 'EMP-'
        ]);
    }

    private function validateKaryawanData(array $data): void
    {
        if (empty($data['nama_lengkap'])) {
            throw new InvalidKaryawanDataException('Nama lengkap harus diisi');
        }
        if (empty($data['posisi'])) {
            throw new InvalidKaryawanDataException('Posisi harus diisi');
        }
        if (empty($data['departemen'])) {
            throw new InvalidKaryawanDataException('Departemen harus diisi');
        }
    }
}








