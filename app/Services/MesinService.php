<?php

namespace App\Services;

use App\Exceptions\MesinNotFoundException;
use App\Exceptions\InvalidMesinDataException;
use App\Models\MasterMesin;
use App\Jobs\RecalculateProdukModalJob;
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
        $oldBiayaProfil = $mesin->biaya_perhitungan_profil;
        if (!$mesin) {
            throw new MesinNotFoundException();
        }

        try {
            DB::beginTransaction();

            Log::info('DEBUG Mesin Update - Data sebelum processing', [
                'mesin_id' => $id,
                'biaya_perhitungan_profil_json' => $data['biaya_perhitungan_profil_json'] ?? 'NOT SET',
                'has_biaya_json' => isset($data['biaya_perhitungan_profil_json'])
            ]);

            // Process harga pembelian
            $data = $this->processHargaPembelian($data);

            // Process JSON fields
            $data = $this->processJsonFields($data);

            Log::info('DEBUG Mesin Update - Data setelah processing', [
                'mesin_id' => $id,
                'biaya_perhitungan_profil' => $data['biaya_perhitungan_profil'] ?? 'NOT SET',
                'final_data_keys' => array_keys($data)
            ]);

            // Handle gambar operations
            $data = $this->handleGambarUpdate($mesin, $data, $gambar);

            $this->mesinRepository->update($id, $data);

            DB::commit();

            Log::info('Mesin updated successfully', ['mesin_id' => $id]);

            $updatedMesin = $this->mesinRepository->find($id);
            Log::info('DEBUG Mesin Update - Data tersimpan di DB', [
                'mesin_id' => $id,
                'saved_biaya_perhitungan_profil' => $updatedMesin->biaya_perhitungan_profil
            ]);
           
            $this->handleCascadeUpdate($updatedMesin, $oldBiayaProfil);
            return $updatedMesin;
            

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
                $decodedData = $this->processJsonField($data[$inputField]);
                
                // Untuk biaya_perhitungan_profil, sinkronkan dengan data dari frontend
                if ($dbField === 'biaya_perhitungan_profil' && is_array($decodedData)) {
                    $decodedData = array_map(function($profile) {
                        if (isset($profile['settings']) && is_array($profile['settings'])) {
                            // Jika frontend tidak mengirim biaya_tambahan_profil, hapus dari database
                            // Jika frontend mengirim array kosong, hapus dari database  
                            // Jika frontend mengirim array dengan item, simpan seperti apa adanya
                            if (!isset($profile['settings']['biaya_tambahan_profil']) || 
                                empty($profile['settings']['biaya_tambahan_profil']) ||
                                !is_array($profile['settings']['biaya_tambahan_profil']) ||
                                count($profile['settings']['biaya_tambahan_profil']) === 0) {
                                unset($profile['settings']['biaya_tambahan_profil']);
                            }
                            // Jika ada biaya_tambahan_profil, biarkan seperti apa adanya
                        }
                        return $profile;
                    }, $decodedData);
                }
                
                $data[$dbField] = $decodedData;
            } else {
                // Field tidak dikirim, set ke array kosong untuk menghapus data
                $data[$dbField] = [];
            }
            unset($data[$inputField]);
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

    /**
     * Remove specific biaya tambahan from a profile
     */
    public function removeBiayaTambahan(int $id, int $profileIndex, string $biayaNama): MasterMesin
    {
        $mesin = $this->mesinRepository->find($id);
        if (!$mesin) {
            throw new MesinNotFoundException();
        }

        $biayaNama = trim($biayaNama);
        if (empty($biayaNama)) {
            throw new InvalidMesinDataException('Nama biaya tambahan tidak boleh kosong');
        }

        try {
            DB::beginTransaction();

            $profiles = $mesin->biaya_perhitungan_profil ?? [];

            Log::info('DEBUG removeBiayaTambahan - Data sebelum proses', [
                'mesin_id' => $id,
                'profile_index' => $profileIndex,
                'biaya_nama' => $biayaNama,
                'profiles_count' => count($profiles),
                'target_profile_exists' => isset($profiles[$profileIndex])
            ]);

            // Validasi profile index exists
            if (!isset($profiles[$profileIndex])) {
                throw new InvalidMesinDataException('Profile tidak ditemukan pada index ' . $profileIndex);
            }

            $profile = $profiles[$profileIndex];

            // Validasi biaya_tambahan_profil exists
            if (!isset($profile['settings']['biaya_tambahan_profil']) || 
                !is_array($profile['settings']['biaya_tambahan_profil'])) {
                throw new InvalidMesinDataException('Biaya tambahan tidak ditemukan dalam profile ini');
            }

            $biayaTambahan = $profile['settings']['biaya_tambahan_profil'];

            Log::info('DEBUG removeBiayaTambahan - Biaya tambahan sebelum hapus', [
                'mesin_id' => $id,
                'biaya_tambahan' => $biayaTambahan
            ]);

            // Cari dan hapus item berdasarkan nama
            $foundIndex = null;
            foreach ($biayaTambahan as $index => $biaya) {
                if (isset($biaya['nama']) && trim($biaya['nama']) === $biayaNama) {
                    $foundIndex = $index;
                    break;
                }
            }

            if ($foundIndex === null) {
                throw new InvalidMesinDataException('Biaya tambahan dengan nama "' . $biayaNama . '" tidak ditemukan');
            }

            Log::info('DEBUG removeBiayaTambahan - Item ditemukan untuk dihapus', [
                'mesin_id' => $id,
                'found_index' => $foundIndex,
                'item_to_remove' => $biayaTambahan[$foundIndex]
            ]);

            // Hapus item dari array menggunakan unset + array_values untuk reindex
            unset($biayaTambahan[$foundIndex]);
            $biayaTambahan = array_values($biayaTambahan); // Reindex array

            Log::info('DEBUG removeBiayaTambahan - Biaya tambahan setelah hapus', [
                'mesin_id' => $id,
                'biaya_tambahan_after' => $biayaTambahan,
                'is_empty' => empty($biayaTambahan)
            ]);

            // Jika array kosong setelah penghapusan, hapus property biaya_tambahan_profil
            if (empty($biayaTambahan)) {
                unset($profile['settings']['biaya_tambahan_profil']);
                Log::info('DEBUG removeBiayaTambahan - Property biaya_tambahan_profil dihapus karena kosong', [
                    'mesin_id' => $id
                ]);
            } else {
                // Jika masih ada item, update array
                $profile['settings']['biaya_tambahan_profil'] = $biayaTambahan;
            }

            // Update profile di array profiles
            $profiles[$profileIndex] = $profile;

            // Update total biaya profile
            $this->updateProfileTotal($profiles[$profileIndex]);

            Log::info('DEBUG removeBiayaTambahan - Profile setelah update total', [
                'mesin_id' => $id,
                'profile_total' => $profiles[$profileIndex]['total'] ?? 'NO_TOTAL'
            ]);

            // Simpan perubahan
            $mesin->biaya_perhitungan_profil = $profiles;
            $saved = $mesin->save();

            Log::info('DEBUG removeBiayaTambahan - Save result', [
                'mesin_id' => $id,
                'save_success' => $saved
            ]);

            DB::commit();

            // Ambil ulang data dari database untuk verifikasi
            $updatedMesin = $this->mesinRepository->find($id);

            Log::info('Biaya tambahan removed successfully', [
                'mesin_id' => $id,
                'profile_index' => $profileIndex,
                'biaya_nama' => $biayaNama,
                'final_biaya_tambahan' => $updatedMesin->biaya_perhitungan_profil[$profileIndex]['settings']['biaya_tambahan_profil'] ?? 'PROPERTY_REMOVED',
                'final_total' => $updatedMesin->biaya_perhitungan_profil[$profileIndex]['total'] ?? 'NO_TOTAL'
            ]);

            return $updatedMesin;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to remove biaya tambahan', [
                'mesin_id' => $id,
                'profile_index' => $profileIndex,
                'biaya_nama' => $biayaNama,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new InvalidMesinDataException('Gagal menghapus biaya tambahan: ' . $e->getMessage());
        }
    }

    /**
     * Update total biaya for a profile
     */
    private function updateProfileTotal(array &$profile): void
    {
        $tipe = $profile['tipe'] ?? '';
        $totalBiaya = 0.0;
        $satuan = '';

        // Hitung biaya berdasarkan tipe perhitungan
        switch ($tipe) {
            case 'per_satuan_area':
                $hargaTinta = (float)($profile['settings']['harga_tinta_per_liter'] ?? 0);
                $konsumsiTinta = (float)($profile['settings']['konsumsi_tinta_per_m2'] ?? 0);
                // Konversi mL ke L (1L = 1000mL)
                $totalBiaya = $hargaTinta * ($konsumsiTinta / 1000);
                $satuan = 'Rp/m²';
                break;

            case 'per_klik':
                $hargaPerKlik = (float)($profile['settings']['harga_per_klik'] ?? 0);
                $jumlahKlik = max(1, (int)($profile['settings']['jumlah_klik'] ?? 1));
                $totalBiaya = $hargaPerKlik * $jumlahKlik;
                $satuan = 'Rp/klik';
                break;

            case 'per_lembar':
                $totalBiaya = (float)($profile['settings']['biaya_per_lembar'] ?? 0);
                $satuan = 'Rp/lembar';
                break;

            case 'per_waktu':
                $totalBiaya = (float)($profile['settings']['biaya_per_menit'] ?? 0);
                $satuan = 'Rp/menit';
                break;

            case 'per_berat':
                $totalBiaya = (float)($profile['settings']['biaya_per_kg'] ?? 0);
                $satuan = 'Rp/kg';
                break;

            case 'per_job':
                $totalBiaya = (float)($profile['settings']['biaya_per_job'] ?? 0);
                $satuan = 'Rp/job';
                break;

            default:
                $totalBiaya = 0.0;
                $satuan = '';
                break;
        }

        // Tambahkan biaya tambahan dari array biaya_tambahan_profil
        if (isset($profile['settings']['biaya_tambahan_profil']) && 
            is_array($profile['settings']['biaya_tambahan_profil'])) {
            
            foreach ($profile['settings']['biaya_tambahan_profil'] as $biaya) {
                if (isset($biaya['nilai']) && is_numeric($biaya['nilai'])) {
                    $totalBiaya += (float)$biaya['nilai'];
                }
            }
        }

        // Update total dan satuan di profile
        $profile['total'] = round($totalBiaya, 2); 
    
    }

    /**
     * Handle cascade update when mesin biaya changes
     */
    private function handleCascadeUpdate(MasterMesin $mesin, array $oldBiayaProfil = null): void
    {
        // Check if biaya actually changed
        $newBiayaProfil = $mesin->biaya_perhitungan_profil ?? [];
        if ($oldBiayaProfil && !$this->hasBiayaChanged($newBiayaProfil, $oldBiayaProfil)) {
            return; // No change, skip
        }

        // Mark produk yang perlu recalc
        $this->markAffectedProdukForRecalc($mesin->id);

        // Dispatch background job
        RecalculateProdukModalJob::dispatch($mesin->id);

        Log::info('Mesin biaya updated, cascade update initiated', [
            'mesin_id' => $mesin->id,
            'mesin_name' => $mesin->nama_mesin
        ]);
    }

    /**
     * Check if biaya profil has changed
     */
    private function hasBiayaChanged(array $newBiaya, array $oldBiaya): bool
    {
        return json_encode($newBiaya) !== json_encode($oldBiaya);
    }

    /**
     * Mark produk yang menggunakan mesin tertentu untuk recalc
     */
    private function markAffectedProdukForRecalc(int $mesinId): int
    {
        $affectedCount = DB::update("
            UPDATE produk 
            SET needs_recalc = true, updated_at = NOW()
            WHERE ? = ANY(mesin_ids)
        ", [$mesinId]);

        Log::info('Marked produk for recalc', [
            'mesin_id' => $mesinId,
            'affected_produk_count' => $affectedCount
        ]);

        return $affectedCount;
    }
}