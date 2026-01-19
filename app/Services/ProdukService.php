<?php

namespace App\Services;

use App\Exceptions\ProdukNotFoundException;
use App\Exceptions\InvalidProdukDataException;
use App\Models\Produk;
use App\Repositories\Interfaces\ProdukRepositoryInterface;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ProdukService
{
    public function __construct(
        private ProdukRepositoryInterface $produkRepository,
        private SupabaseStorageService $storageService
    ) {
    }

    public function createProduk(array $data, array $files = []): Produk
    {
        $this->validateProdukData($data);

        try {
            DB::beginTransaction();

            $data['kode_produk'] = $this->generateKodeProduk();
            $data['lebar'] = $data['lebar'] ?? 0;
            $data['panjang'] = $data['panjang'] ?? 0;

            // Process JSON fields
            // $data['bahan_baku_json'] = $this->processJsonField($data['bahan_baku_json'] ?? null);
            $data['harga_bertingkat_json'] = $this->processJsonField($data['harga_bertingkat_json'] ?? null);
            $data['harga_reseller_json'] = $this->processJsonField($data['harga_reseller_json'] ?? null);
            $data['alur_produksi_json'] = $this->processJsonField($data['alur_produksi_json'] ?? null);
            $data['parameter_modal_json'] = $this->processJsonField($data['parameter_modal_json'] ?? null);
            $data['spesifikasi_teknis_json'] = $this->processJsonField($data['spesifikasi_teknis_json'] ?? null);
            $data['biaya_tambahan_json'] = $this->processJsonField($data['biaya_tambahan_json'] ?? null);

            // Process file uploads - menggunakan method yang mengembalikan URL
            $data['foto_pendukung_json'] = $this->uploadFilesWithUrl(
                $files['foto_pendukung_new'] ?? [],
                'produk/foto'
            );

            $data['video_pendukung_json'] = $this->uploadFilesWithUrl(
                $files['video_pendukung_new'] ?? [],
                'produk/video'
            );

            $data['dokumen_pendukung_json'] = $this->uploadDocumentsWithUrl(
                $files['dokumen_pendukung_new'] ?? [],
                'produk/dokumen'
            );

            if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
                $bahanBakuData = $data['bahan_baku'];
            }

            unset($data['bahan_baku']);
            $produk = $this->produkRepository->create($data);

            // Sync bahan baku ke relational table
            if (isset($bahanBakuData) && is_array($bahanBakuData)) {
                $produk->syncBahanBakus($bahanBakuData);
            }
            
            DB::commit();

            Log::info('Produk created successfully', [
                'produk_id' => $produk->id,
                'kode_produk' => $produk->kode_produk
            ]);

            return $produk->load('bahanBakus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create Produk', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw new InvalidProdukDataException('Gagal membuat produk: ' . $e->getMessage());
        }
    }

    public function updateProduk(int $id, array $data, array $files = []): Produk
    {
        $produk = $this->produkRepository->find($id);
        if (!$produk) {
            throw new ProdukNotFoundException();
        }

        $this->validateProdukData($data);

        try {
            DB::beginTransaction();
            $data['lebar'] = $data['lebar'] ?? 0;
            $data['panjang'] = $data['panjang'] ?? 0;

            // Process JSON fields
            // $data['bahan_baku_json'] = $this->processJsonField($data['bahan_baku_json'] ?? null);
            $data['harga_bertingkat_json'] = $this->processJsonField($data['harga_bertingkat_json'] ?? null);
            $data['harga_reseller_json'] = $this->processJsonField($data['harga_reseller_json'] ?? null);
            $data['alur_produksi_json'] = $this->processJsonField($data['alur_produksi_json'] ?? null);
            $data['parameter_modal_json'] = $this->processJsonField($data['parameter_modal_json'] ?? null);
            // $data['dokumen_pendukung_json'] = $this->processJsonField($data['dokumen_pendukung_json'] ?? null);
            $data['spesifikasi_teknis_json'] = $this->processJsonField($data['spesifikasi_teknis_json'] ?? null);
            $data['biaya_tambahan_json'] = $this->processJsonField($data['biaya_tambahan_json'] ?? null);

            // Handle existing files
            // $existingFoto = $this->processExistingFiles($data['foto_pendukung_existing_json'] ?? null);
            // $existingVideo = $this->processExistingFiles($data['video_pendukung_existing_json'] ?? null);
            // $existingDokumen = $this->processExistingFiles($data['dokumen_pendukung_existing_json'] ?? null);

            // Upload new files
            // $newFoto = $this->uploadFilesWithUrl($files['foto_pendukung_new'] ?? [], 'produk/foto');
            // $newVideo = $this->uploadFilesWithUrl($files['video_pendukung_new'] ?? [], 'produk/video');
            // $newDokumen = $this->uploadDocumentsWithUrl($files['dokumen_pendukung_new'] ?? [], 'produk/dokumen');

            // Merge existing and new files
            // $data['foto_pendukung_json'] = array_merge($existingFoto, $newFoto);
            // $data['video_pendukung_json'] = array_merge($existingVideo, $newVideo);
            // $data['dokumen_pendukung_json'] = array_merge($existingDokumen, $newDokumen);

            // Process file uploads jika ada
            if (!empty($files['foto_pendukung_new'])) {
                $existingFotos = json_decode($produk->foto_pendukung_json ?? '[]', true);
                $newFotos = $this->uploadFilesWithUrl($files['foto_pendukung_new'], 'produk/foto');
                $data['foto_pendukung_json'] = array_merge($existingFotos, $newFotos);
            }

            if (!empty($files['video_pendukung_new'])) {
                $existingVideos = json_decode($produk->video_pendukung_json ?? '[]', true);
                $newVideos = $this->uploadFilesWithUrl($files['video_pendukung_new'], 'produk/video');
                $data['video_pendukung_json'] = array_merge($existingVideos, $newVideos);
            }

            if (!empty($files['dokumen_pendukung_new'])) {
                $existingDokumens = json_decode($produk->dokumen_pendukung_json ?? '[]', true);
                $newDokumens = $this->uploadFilesWithUrl($files['dokumen_pendukung_new'], 'produk/dokumen');
                $data['dokumen_pendukung_json'] = array_merge($existingDokumens, $newDokumens);
            }

            if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
                $bahanBakuData = $data['bahan_baku'];
            }
            
            unset($data['bahan_baku']); 
            $produk = $this->produkRepository->update($id, $data);

            if (isset($bahanBakuData) && is_array($bahanBakuData)) {
                $produk->syncBahanBakus($bahanBakuData);
            }

            DB::commit();

            Log::info('Produk updated successfully', ['produk_id' => $id]);

            return $produk->load('bahanBakus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update Produk', [
                'produk_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidProdukDataException('Gagal mengupdate produk: ' . $e->getMessage());
        }
    }

    public function deleteProduk(int $id): bool
    {
        $produk = $this->produkRepository->find($id);
        if (!$produk) {
            throw new ProdukNotFoundException();
        }

        try {
            // Delete associated files from Supabase
            $this->deleteProdukFiles($produk);

            $result = $this->produkRepository->delete($id);

            Log::info('Produk deleted successfully', ['produk_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete Produk', [
                'produk_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new InvalidProdukDataException('Gagal menghapus produk');
        }
    }

    public function getProduk(int $id): Produk
    {
        $produk = $this->produkRepository->findWithRelations($id);
        if (!$produk) {
            throw new ProdukNotFoundException();
        }
        return $produk;
    }

    public function getPaginatedProduk(int $perPage = 10, array $filters = [])
    {
        return $this->produkRepository->paginate($perPage, $filters);
    }

    private function generateKodeProduk(): string
    {
        return IdGenerator::generate([
            'table' => 'produk',
            'field' => 'kode_produk',
            'length' => 8,
            'prefix' => 'PRD-'
        ]);
    }

    private function validateProdukData(array $data): void
    {
        if (empty($data['nama_produk'])) {
            throw new InvalidProdukDataException('Nama produk harus diisi');
        }
        if (empty($data['kategori_utama_id'])) {
            throw new InvalidProdukDataException('Kategori utama harus dipilih');
        }
        if (empty($data['satuan_id'])) {
            throw new InvalidProdukDataException('Satuan harus dipilih');
        }
        if (empty($data['sub_satuan_id'])) {
            throw new InvalidProdukDataException('Detail satuan harus dipilih');
        }
        if (!empty($data['satuan_id']) && !empty($data['sub_satuan_id'])) {
            $subSatuan = \App\Models\SubDetailParameter::where('id', $data['sub_satuan_id'])
                ->where('detail_parameter_id', $data['satuan_id'])
                ->where('aktif', 1)
                ->first();
            
            if (!$subSatuan) {
                throw new InvalidProdukDataException('Detail satuan tidak valid untuk satuan yang dipilih');
            }
        }
        if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
            if (empty($data['bahan_baku'])) {
                throw new InvalidProdukDataException('Bahan baku tidak boleh kosong');
            }
    
            foreach ($data['bahan_baku'] as $index => $bahanBaku) {
                if (!isset($bahanBaku['id']) || !isset($bahanBaku['jumlah']) || !isset($bahanBaku['harga'])) {
                    throw new InvalidProdukDataException("Bahan baku #{$index}: id, jumlah, dan harga harus diisi");
                }
    
                if ($bahanBaku['jumlah'] <= 0) {
                    throw new InvalidProdukDataException("Bahan baku #{$index}: jumlah harus lebih dari 0");
                }
    
                if ($bahanBaku['harga'] < 0) {
                    throw new InvalidProdukDataException("Bahan baku #{$index}: harga tidak boleh negatif");
                }
            }
        }
    }

    private function processJsonField(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidProdukDataException('Format JSON tidak valid');
        }

        return is_array($data) ? $data : [];
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
                        'url' => $path, // For backward compatibility
                        'ukuran' => $file->getSize(),
                        'tipe' => $file->getClientMimeType(),
                        'jenis' => $file->getClientMimeType(), // For backward compatibility
                    ];
                }
            }
        }

        return $uploadedDocs;
    }

    /**
     * Upload files and return URLs instead of paths
     */
    private function uploadFilesWithUrl(array $files, string $folder): array
    {
        Log::info('STARTING FILE UPLOAD PROCESS', [
            'file_count' => count($files),
            'folder' => $folder
        ]);

        $uploadedUrls = [];

        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                Log::info('PROCESSING FILE', [
                    'index' => $index,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize()
                ]);

                $path = $this->storageService->upload($file, $folder);

                Log::info('UPLOAD RESULT FOR FILE', [
                    'file_name' => $file->getClientOriginalName(),
                    'upload_path' => $path,
                    'path_null' => is_null($path)
                ]);

                if ($path) {
                    $url = $this->storageService->getUrl($path);

                    Log::info('URL GENERATION RESULT', [
                        'path' => $path,
                        'generated_url' => $url,
                        'url_null' => is_null($url)
                    ]);

                    if ($url) {
                        $uploadedUrls[] = $url;
                    } else {
                        Log::warning('URL GENERATION FAILED, USING PATH AS FALLBACK', [
                            'path' => $path,
                            'folder' => $folder,
                            'file_name' => $file->getClientOriginalName()
                        ]);
                        $uploadedUrls[] = $path;
                    }
                } else {
                    Log::error('UPLOAD FAILED FOR FILE', [
                        'file_name' => $file->getClientOriginalName()
                    ]);
                }
            }
        }

        Log::info('FILE UPLOAD PROCESS COMPLETED', [
            'uploaded_count' => count($uploadedUrls),
            'uploaded_urls' => $uploadedUrls
        ]);

        return $uploadedUrls;
    }

    /**
     * Upload documents and return structured data with URLs
     */
    private function uploadDocumentsWithUrl(array $files, string $folder): array
    {
        $uploadedDocs = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->storageService->upload($file, $folder);
                if ($path) {
                    $url = $this->storageService->getUrl($path);
                    if ($url) {
                        $uploadedDocs[] = [
                            'nama' => $file->getClientOriginalName(),
                            'path' => $path,
                            'url' => $url,
                            'ukuran' => $file->getSize(),
                            'tipe' => $file->getClientMimeType(),
                            'jenis' => $file->getClientMimeType(),
                        ];
                    } else {
                        // FALLBACK: Jika URL gagal, simpan dengan path
                        Log::warning('Failed to generate URL for uploaded document, using path instead', [
                            'path' => $path,
                            'folder' => $folder,
                            'file_name' => $file->getClientOriginalName()
                        ]);
                        $uploadedDocs[] = [
                            'nama' => $file->getClientOriginalName(),
                            'path' => $path,
                            'url' => $path, // Fallback to path
                            'ukuran' => $file->getSize(),
                            'tipe' => $file->getClientMimeType(),
                            'jenis' => $file->getClientMimeType(),
                        ];
                    }
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

    private function deleteProdukFiles(Produk $produk): void
    {
        // Delete foto
        $fotos = $produk->foto_pendukung_json ?? [];
        foreach ($fotos as $foto) {
            if (is_string($foto)) {
                // Handle both Supabase path and old storage path
                $path = str_replace('/storage/', '', $foto);
                $this->storageService->delete($path);
            }
        }

        // Delete video
        $videos = $produk->video_pendukung_json ?? [];
        foreach ($videos as $video) {
            if (is_string($video)) {
                // Handle both Supabase path and old storage path
                $path = str_replace('/storage/', '', $video);
                $this->storageService->delete($path);
            }
        }

        // Delete dokumen
        $dokumens = $produk->dokumen_pendukung_json ?? [];
        foreach ($dokumens as $dokumen) {
            // Handle both 'path' and 'url' keys for backward compatibility
            $path = $dokumen['path'] ?? $dokumen['url'] ?? null;
            if ($path) {
                // Handle both Supabase path and old storage path
                $path = str_replace('/storage/', '', $path);
                $this->storageService->delete($path);
            }
        }
    }
}


