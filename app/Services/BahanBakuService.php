<?php

namespace App\Services;

use App\Exceptions\BahanBakuNotFoundException;
use App\Exceptions\InvalidBahanBakuDataException;
use App\Models\BahanBaku;
use App\Repositories\Interfaces\BahanBakuRepositoryInterface;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class BahanBakuService
{
    public function __construct(
        private BahanBakuRepositoryInterface $bahanBakuRepository,
        private SupabaseStorageService $storageService
    ) {}

    public function createBahanBaku(array $data, array $files = []): BahanBaku
    {
        $this->validateBahanBakuData($data);

        try {
            DB::beginTransaction();

            $data['kode_bahan'] = $this->generateKodeBahan();
            
            // Process JSON fields
            $data['konversi_satuan_json'] = $this->processKonversiSatuan($data['konversi_satuan_json'] ?? null);
            $data['detail_spesifikasi_json'] = $this->processJsonField($data['detail_spesifikasi_json'] ?? null);
            $data['link_pendukung_json'] = $this->processLinkPendukung($data['link_pendukung_json'] ?? null);
            
            // Process numeric fields
            $data['stok_saat_ini'] = isset($data['stok_saat_ini']) ? (int) $data['stok_saat_ini'] : 0;
            $data['stok_minimum'] = isset($data['stok_minimum']) ? (int) $data['stok_minimum'] : 0;
            $data['stok_maksimum'] = isset($data['stok_maksimum']) ? (int) $data['stok_maksimum'] : 0;
            $data['status_aktif'] = (bool) ($data['status_aktif'] ?? false);

            // Process file uploads
            $data['foto_pendukung_json'] = $this->uploadFiles(
                $files['foto_pendukung_new'] ?? [],
                'bahan_baku/foto'
            );
            
            $data['video_pendukung_json'] = $this->uploadFiles(
                $files['video_pendukung_new'] ?? [],
                'bahan_baku/video'
            );
            
            $data['dokumen_pendukung_json'] = $this->uploadDocuments(
                $files['dokumen_pendukung_new'] ?? [],
                'bahan_baku/dokumen'
            );

            $bahanBaku = $this->bahanBakuRepository->create($data);

            DB::commit();

            Log::info('Bahan Baku created successfully', [
                'bahan_baku_id' => $bahanBaku->id,
                'kode_bahan' => $bahanBaku->kode_bahan
            ]);

            return $bahanBaku;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Bahan Baku', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidBahanBakuDataException('Gagal membuat bahan baku: ' . $e->getMessage());
        }
    }

    public function updateBahanBaku(int $id, array $data, array $files = []): BahanBaku
    {
        $bahanBaku = $this->bahanBakuRepository->find($id);
        if (!$bahanBaku) {
            throw new BahanBakuNotFoundException();
        }

        try {
            DB::beginTransaction();

            // Process JSON fields
            $data['konversi_satuan_json'] = $this->processKonversiSatuan($data['konversi_satuan_json'] ?? null);
            $data['detail_spesifikasi_json'] = $this->processJsonField($data['detail_spesifikasi_json'] ?? null);
            $data['link_pendukung_json'] = $this->processLinkPendukung($data['link_pendukung_json'] ?? null);
            
            // Process numeric fields
            $data['stok_saat_ini'] = isset($data['stok_saat_ini']) ? (int) $data['stok_saat_ini'] : 0;
            $data['stok_minimum'] = isset($data['stok_minimum']) ? (int) $data['stok_minimum'] : 0;
            $data['stok_maksimum'] = isset($data['stok_maksimum']) ? (int) $data['stok_maksimum'] : 0;
            $data['status_aktif'] = (bool) ($data['status_aktif'] ?? false);

            // Handle existing files
            $existingFoto = $this->processExistingFiles($data['foto_pendukung_existing_json'] ?? null);
            $existingVideo = $this->processExistingFiles($data['video_pendukung_existing_json'] ?? null);
            $existingDokumen = $this->processExistingFiles($data['dokumen_pendukung_existing_json'] ?? null);
            
            // Get old files for comparison
            $oldFoto = $this->ensureArray($bahanBaku->foto_pendukung_json ?? []);
            $oldVideo = $this->ensureArray($bahanBaku->video_pendukung_json ?? []);
            $oldDokumen = $this->ensureArray($bahanBaku->dokumen_pendukung_json ?? []);

            // Upload new files
            $newFoto = $this->uploadFiles($files['foto_pendukung_new'] ?? [], 'bahan_baku/foto');
            $newVideo = $this->uploadFiles($files['video_pendukung_new'] ?? [], 'bahan_baku/video');
            $newDokumen = $this->uploadDocuments($files['dokumen_pendukung_new'] ?? [], 'bahan_baku/dokumen');
            
            // Merge existing and new files
            $data['foto_pendukung_json'] = array_merge($existingFoto, $newFoto);
            $data['video_pendukung_json'] = array_merge($existingVideo, $newVideo);

            // Delete removed files from Supabase
            $this->deleteRemovedFiles($oldFoto, $existingFoto);
            $this->deleteRemovedFiles($oldVideo, $existingVideo);
            
            // Handle documents - merge existing with new
            $existingDokumen = $this->processExistingFiles($data['dokumen_pendukung_json'] ?? null);
            if (!empty($newDokumen)) {
                // Merge existing and new documents
                $data['dokumen_pendukung_json'] = array_merge($existingDokumen, $newDokumen);
            } else {
                // Keep existing documents if no new ones uploaded
                $data['dokumen_pendukung_json'] = $existingDokumen;
            }
            
            // Delete removed documents from Supabase
            $this->deleteRemovedDocuments($oldDokumen, $existingDokumen);

            $this->bahanBakuRepository->update($id, $data);

            DB::commit();

            Log::info('Bahan Baku updated successfully', ['bahan_baku_id' => $id]);

            return $this->bahanBakuRepository->find($id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Bahan Baku', [
                'bahan_baku_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidBahanBakuDataException('Gagal mengupdate bahan baku: ' . $e->getMessage());
        }
    }

    public function deleteBahanBaku(int $id): bool
    {
        $bahanBaku = $this->bahanBakuRepository->find($id);
        if (!$bahanBaku) {
            throw new BahanBakuNotFoundException();
        }

        try {
            // Delete associated files from Supabase
            $this->deleteBahanBakuFiles($bahanBaku);

            $result = $this->bahanBakuRepository->delete($id);
            
            Log::info('Bahan Baku deleted successfully', ['bahan_baku_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Bahan Baku', [
                'bahan_baku_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidBahanBakuDataException('Gagal menghapus bahan baku');
        }
    }

    public function getBahanBaku(int $id): BahanBaku
    {
        $bahanBaku = $this->bahanBakuRepository->findWithRelations($id);
        if (!$bahanBaku) {
            throw new BahanBakuNotFoundException();
        }
        return $bahanBaku;
    }

    public function getPaginatedBahanBaku(int $perPage = 10, array $filters = [])
    {
        return $this->bahanBakuRepository->paginate($perPage, $filters);
    }

    private function generateKodeBahan(): string
    {
        return IdGenerator::generate([
            'table' => 'bahan_baku',
            'field' => 'kode_bahan',
            'length' => 8,
            'prefix' => 'MAT-'
        ]);
    }

    private function validateBahanBakuData(array $data): void
    {
        if (empty($data['nama_bahan'])) {
            throw new InvalidBahanBakuDataException('Nama bahan harus diisi');
        }
        if (empty($data['kategori_id'])) {
            throw new InvalidBahanBakuDataException('Kategori harus dipilih');
        }
        if (empty($data['sub_kategori_id'])) {
            throw new InvalidBahanBakuDataException('Sub kategori harus dipilih');
        }
    }

    private function processKonversiSatuan(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidBahanBakuDataException('Format konversi satuan tidak valid');
        }

        return array_map(function($item) {
            return [
                'satuan_dari' => $item['satuan_dari'] ?? '',
                'jumlah' => $item['jumlah'] ?? 1
            ];
        }, $data);
    }

    private function processJsonField(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidBahanBakuDataException('Format JSON tidak valid');
        }

        return is_array($data) ? $data : [];
    }

    private function processLinkPendukung(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidBahanBakuDataException('Format link pendukung tidak valid');
        }

        return array_map(function($item) {
            if (is_string($item)) {
                return ['url' => $item, 'keterangan' => ''];
            }
            return [
                'url' => $item['url'] ?? '',
                'keterangan' => $item['keterangan'] ?? ''
            ];
        }, $data);
    }

    private function uploadFiles(array $files, string $folder): array
    {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->storageService->upload($file, $folder);
                if ($path) {
                    $uploadedPaths[] = $path;
                }
            }
        }

        return $uploadedPaths;
    }

    /**
    * Ensure value is array - handle both JSON strings and arrays from database
    */
    private function ensureArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    private function uploadDocuments(array $files, string $folder): array
    {
        $uploadedDocs = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->storageService->upload($file, $folder);
                if ($path) {
                    $uploadedDocs[] = [
                        'nama' => $file->getClientOriginalName(),
                        'path' => $path,
                        'ukuran' => $file->getSize(),
                        'tipe' => $file->getClientMimeType(),
                    ];
                }
            }
        }

        return $uploadedDocs;
    }

    private function processExistingFiles(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function deleteBahanBakuFiles(BahanBaku $bahanBaku): void
    {
        // Delete foto
        $fotos = $bahanBaku->foto_pendukung_json ?? [];
        foreach ($fotos as $foto) {
            if (is_string($foto)) {
                // Handle both Supabase path and old storage path
                $path = $this->normalizePath($foto);
                if ($path) {
                    $this->storageService->delete($path);
                }
            }
        }

        // Delete video
        $videos = $bahanBaku->video_pendukung_json ?? [];
        foreach ($videos as $video) {
            if (is_string($video)) {
                // Handle both Supabase path and old storage path
                $path = $this->normalizePath($video);
                if ($path) {
                    $this->storageService->delete($path);
                }
            }
        }

        // Delete dokumen
        $dokumens = $bahanBaku->dokumen_pendukung_json ?? [];
        foreach ($dokumens as $dokumen) {
            // Handle both 'path' and 'url' keys for backward compatibility
            $path = $dokumen['path'] ?? $dokumen['url'] ?? null;
            if ($path) {
                $path = $this->normalizePath($path);
                if ($path) {
                    $this->storageService->delete($path);
                }
            }
        }
    }

    /**
     * Delete files that were removed during update
     */
    private function deleteRemovedFiles(array $oldFiles, array $existingFiles): void
    {
        $removedFiles = array_diff($oldFiles, $existingFiles);
        foreach ($removedFiles as $file) {
            if (is_string($file)) {
                $path = $this->normalizePath($file);
                if ($path) {
                    $this->storageService->delete($path);
                }
            }
        }
    }

    /**
     * Delete documents that were removed during update
     */
    private function deleteRemovedDocuments(array $oldDocs, array $existingDocs): void
    {
        // Extract paths from old and existing documents
        $oldPaths = array_map(function($doc) {
            return $doc['path'] ?? $doc['url'] ?? null;
        }, $oldDocs);
        
        $existingPaths = array_map(function($doc) {
            return $doc['path'] ?? $doc['url'] ?? null;
        }, $existingDocs);
        
        $removedPaths = array_diff($oldPaths, $existingPaths);
        foreach ($removedPaths as $path) {
            if ($path) {
                $path = $this->normalizePath($path);
                if ($path) {
                    $this->storageService->delete($path);
                }
            }
        }
    }

    /**
     * Normalize file path - handle both old storage format and Supabase format
     */
    private function normalizePath(string $path): ?string
    {
        // If path starts with /storage/, it's old local storage format
        // We need to extract the actual path for Supabase
        if (str_starts_with($path, '/storage/')) {
            // Remove /storage/ prefix to get the actual storage path
            $normalized = str_replace('/storage/', '', $path);
            return $normalized ?: null;
        }
        
        // If path is already a Supabase path (from SupabaseStorageService), return as is
        // Supabase paths typically look like: "bahan_baku/foto/filename.jpg"
        return $path;
    }
}


