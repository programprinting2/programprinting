<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected string $baseUrl;
    protected string $bucket;
    protected string $apiKey; 

    public function __construct()
    {
        $this->baseUrl = env('SUPABASE_URL');
        $this->bucket = env('SUPABASE_BUCKET', 'qdesign-public');
        $this->apiKey = env('SUPABASE_SECRET'); // Service role key untuk bypass RLS
    }

    /**
     * Check if Supabase configuration is complete
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->bucket) && !empty($this->apiKey);
    }


    /**
     * Upload file to Supabase Storage using REST API
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder Folder path in Supabase Storage
     * @param string|null $fileName Custom file name (without extension)
     * @return string|null Path to uploaded file
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', ?string $fileName = null): ?string
    {
        if (!$this->isConfigured()) {
            Log::error('SUPABASE CONFIG INCOMPLETE - UPLOAD CANCELLED');
            return null;
        }

        try {
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $fileName ?? Str::slug($originalName) . '_' . time() . '_' . Str::random(8);
            $fileName = $fileName . '.' . $extension;

            // Build full path
            $fullPath = rtrim($folder, '/') . '/' . $fileName;

            Log::info('UPLOAD ATTEMPT VIA REST API', [
                'path' => $fullPath,
                'file_name' => $file->getClientOriginalName(),
                'bucket' => $this->bucket,
                'file_size' => $file->getSize()
            ]);

            $url = $this->baseUrl . '/storage/v1/object/' . $this->bucket . '/' . $fullPath;

            // ✅ HEADER UNTUK BYPASS RLS - menggunakan service role key
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey, // Service role key
                    'apikey' => $this->apiKey,
                    'Content-Type' => $file->getClientMimeType(),
                    'x-upsert' => 'true', // Allow overwrite
                    'cache-control' => 'max-age=3600',
                ])
                ->withBody(file_get_contents($file->getRealPath()), $file->getClientMimeType())
                ->post($url);

            Log::info('REST API UPLOAD RESPONSE', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                Log::info('FILE UPLOADED SUCCESSFULLY', ['path' => $fullPath]);
                return $fullPath;
            }

            Log::error('UPLOAD FAILED VIA REST API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('UPLOAD EXCEPTION', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Upload multiple files to Supabase Storage
     *
     * @param array $files Array of UploadedFile
     * @param string $folder Folder path in Supabase Storage
     * @return array Array of uploaded file paths
     */
    public function uploadMultiple(array $files, string $folder = 'uploads'): array
    {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->upload($file, $folder);
                if ($path) {
                    $uploadedPaths[] = $path;
                }
            }
        }

        Log::info('Batch upload completed', [
            'total_files' => count($files),
            'uploaded_count' => count($uploadedPaths)
        ]);

        return $uploadedPaths;
    }

    /**
     * Delete file from Supabase Storage
     *
     * @param string $path File path in Supabase Storage
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            $url = $this->baseUrl . '/storage/v1/object/' . $this->bucket . '/' . ltrim($path, '/');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'apikey' => $this->apiKey,
                ])
                ->delete($url);

            $success = $response->successful();

            if ($success) {
                Log::info('File deleted successfully', ['path' => $path]);
            } else {
                Log::error('Failed to delete file', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Delete exception', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            return false;
        }
    }

    /**
     * Delete multiple files from Supabase Storage
     *
     * @param array $paths Array of file paths
     * @return bool
     */
    public function deleteMultiple(array $paths): bool
    {
        $allDeleted = true;

        foreach ($paths as $path) {
            if (!$this->delete($path)) {
                $allDeleted = false;
            }
        }

        Log::info('Batch delete completed', [
            'total_files' => count($paths),
            'all_successful' => $allDeleted
        ]);

        return $allDeleted;
    }

    /**
     * Get public URL for file in Supabase Storage
     *
     * @param string $path File path in Supabase Storage
     * @param string|null $size Optional size variant (thumbnail, small, medium, large)
     * @return string|null Public URL
     */
    public function getUrl(string $path, ?string $size = null): ?string
    {
        if (empty($path)) {
            return null;
        }

        try {
            // ✅ SUPABASE PUBLIC URL FORMAT
            $url = $this->baseUrl . '/storage/v1/object/public/' . $this->bucket . '/' . ltrim($path, '/');

            Log::info('PUBLIC URL GENERATED', [
                'path' => $path,
                'url' => $url
            ]);

            return $url;
        } catch (\Exception $e) {
            Log::error('URL GENERATION ERROR', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            return null;
        }
    }

    /**
     * Check if file exists in Supabase Storage
     *
     * @param string $path File path
     * @return bool
     */
    public function exists(string $path): bool
    {
        try {
            $url = $this->baseUrl . '/storage/v1/object/' . $this->bucket . '/' . ltrim($path, '/');

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'apikey' => $this->apiKey,
                ])
                ->head($url);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exists check failed', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            return false;
        }
    }

}


