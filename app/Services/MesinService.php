<?php

namespace App\Services;

use App\Exceptions\MesinNotFoundException;
use App\Exceptions\InvalidMesinDataException;
use App\Models\MasterMesin;
use App\Repositories\Interfaces\MesinRepositoryInterface;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MesinService
{
    public function __construct(
        private MesinRepositoryInterface $mesinRepository,
        private SupabaseStorageService $storageService
    ) {}

    /**
     * Create new mesin with gambar upload to Supabase
     */
    public function createMesin(array $data, ?UploadedFile $gambar = null): MasterMesin
    {
        $this->validateMesinData($data);

        try {
            DB::beginTransaction();

            // Process harga pembelian
            $data = $this->processHargaPembelian($data);

            // Process JSON fields
            $data = $this->processJsonFields($data);

            // Upload gambar to Supabase if provided
            if ($gambar) {
                $data = $this->handleGambarUpload($data, $gambar, 'mesin');
            }

            $mesin = $this->mesinRepository->create($data);

            DB::commit();

            Log::info('Mesin created successfully', [
                'mesin_id' => $mesin->id,
                'nama_mesin' => $mesin->nama_mesin,
                'has_gambar' => !empty($mesin->supabase_path)
            ]);

            return $mesin;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Mesin', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new InvalidMesinDataException('Gagal membuat mesin: ' . $e->getMessage());
        }
    }

    /**
     * Update existing mesin with gambar management
     */
    public function updateMesin(int $id, array $data, ?UploadedFile $gambar = null): MasterMesin
    {
        $mesin = $this->mesinRepository->find($id);
        if (!$mesin) {
            throw new MesinNotFoundException();
        }

        try {
            DB::beginTransaction();

            // Process harga pembelian
            $data = $this->processHargaPembelian($data);

            // Process JSON fields
            $data = $this->processJsonFields($data);

            // Handle gambar operations
            $data = $this->handleGambarUpdate($mesin, $data, $gambar);

            $this->mesinRepository->update($id, $data);

            DB::commit();

            Log::info('Mesin updated successfully', ['mesin_id' => $id]);

            return $this->mesinRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Mesin', [
                'mesin_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidMesinDataException('Gagal mengupdate mesin: ' . $e->getMessage());
        }
    }

    /**
     * Delete mesin and associated gambar
     */
    public function deleteMesin(int $id): bool
    {
        $mesin = $this->mesinRepository->find($id);
        if (!$mesin) {
            throw new MesinNotFoundException();
        }

        try {
            // Delete gambar from Supabase first
            if ($mesin->supabase_path) {
                $this->storageService->delete($mesin->supabase_path);
                Log::info('Gambar mesin deleted from Supabase', [
                    'mesin_id' => $id,
                    'path' => $mesin->supabase_path
                ]);
            }

            $result = $this->mesinRepository->delete($id);

            Log::info('Mesin deleted successfully', ['mesin_id' => $id]);
            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to delete Mesin', [
                'mesin_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidMesinDataException('Gagal menghapus mesin');
        }
    }

    /**
     * Get mesin by ID
     */
    public function getMesin(int $id): MasterMesin
    {
        $mesin = $this->mesinRepository->find($id);
        if (!$mesin) {
            throw new MesinNotFoundException();
        }
        return $mesin;
    }

    /**
     * Get paginated mesin with filters
     */
    public function getPaginatedMesin(int $perPage = 10, array $filters = [])
    {
        return $this->mesinRepository->paginate($perPage, $filters);
    }


    /**
     * Validate mesin data
     */
    private function validateMesinData(array $data): void
    {
        if (empty($data['nama_mesin'])) {
            throw new InvalidMesinDataException('Nama mesin harus diisi');
        }
        if (empty($data['tipe_mesin'])) {
            throw new InvalidMesinDataException('Tipe mesin harus dipilih');
        }
        if (empty($data['status'])) {
            throw new InvalidMesinDataException('Status harus dipilih');
        }
    }

    /**
     * Process harga pembelian (remove formatting)
     */
    private function processHargaPembelian(array $data): array
    {
        if (isset($data['harga_pembelian']) && $data['harga_pembelian'] !== null) {
            $data['harga_pembelian'] = str_replace(['.', ','], '', $data['harga_pembelian']);
        }
        return $data;
    }

    /**
     * Process JSON fields from form data
     */
    private function processJsonFields(array $data): array
    {
        $jsonFields = [
            'detail_mesin_json' => 'detail_mesin',
            'biaya_perhitungan_profil_json' => 'biaya_perhitungan_profil'
        ];

        foreach ($jsonFields as $inputField => $dbField) {
            if (isset($data[$inputField])) {
                $data[$dbField] = $this->processJsonField($data[$inputField]);
                unset($data[$inputField]);
            }
        }

        return $data;
    }

    /**
     * Handle gambar upload for new mesin
     */
    private function handleGambarUpload(array $data, UploadedFile $gambar, string $folder): array
    {
        $gambarPath = $this->storageService->upload($gambar, $folder);

        if ($gambarPath) {
            $data['supabase_path'] = $gambarPath;
            Log::info('Gambar uploaded to Supabase', ['path' => $gambarPath]);
        } else {
            Log::warning('Failed to upload gambar to Supabase');
        }

        return $data;
    }

    /**
     * Handle gambar update operations
     */
    private function handleGambarUpdate(MasterMesin $mesin, array $data, ?UploadedFile $gambar): array
    {
        // If new gambar uploaded
        if ($gambar) {
            // Delete old gambar 
            $this->deleteExistingGambar($mesin);

            // Upload new gambar
            return $this->handleGambarUpload($data, $gambar, 'mesin');
        }

        // If delete gambar flag is set
        if (isset($data['hapus_gambar']) && $data['hapus_gambar']) {
            // Delete existing gambar 
            $this->deleteExistingGambar($mesin);
            
            // Clear both fields 
            $data['supabase_path'] = null;
            // $data['cloudinary_public_id'] = null;
            
            Log::info('Gambar mesin deleted via checkbox', ['mesin_id' => $mesin->id]);
        }

        return $data;
    }

    /**
     * Delete existing gambar
     */
    private function deleteExistingGambar(MasterMesin $mesin): void
    {
        // Delete from Supabase 
        if ($mesin->supabase_path) {
            $this->storageService->delete($mesin->supabase_path);
            Log::info('Deleted gambar from Supabase', [
                'mesin_id' => $mesin->id,
                'path' => $mesin->supabase_path
            ]);
        }

        // Delete from Cloudinary
        // if ($mesin->cloudinary_public_id) {
        //     try {
        //         // Uncomment if you still have CloudinaryService
        //         // App::make(CloudinaryService::class)->delete($mesin->cloudinary_public_id);
        //         Log::info('Found old Cloudinary image, manual cleanup needed', [
        //             'mesin_id' => $mesin->id,
        //             'cloudinary_id' => $mesin->cloudinary_public_id
        //         ]);
        //     } catch (\Exception $e) {
        //         Log::warning('Could not delete from Cloudinary', [
        //             'mesin_id' => $mesin->id,
        //             'cloudinary_id' => $mesin->cloudinary_public_id,
        //             'error' => $e->getMessage()
        //         ]);
        //     }
        // }
    }

    /**
     * Process single JSON field
     */
    private function processJsonField(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidMesinDataException('Format JSON tidak valid');
        }

        return is_array($data) ? $data : [];
    }
}