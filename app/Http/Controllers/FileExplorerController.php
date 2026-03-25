<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Cache;


class FileExplorerController extends Controller
{
    protected array $allowedExtensions = ['jpg','jpeg','png','gif','webp','bmp','pdf'];
    /**
     * List files and directories in a given path.
     */
    public function index(Request $request): JsonResponse
    {
        $basePath = config('app.explorer_base_path', 'F:/PESANAN/');

        $requestedPath = $request->get('path'); 
        $requestedPathStr = is_string($requestedPath) ? trim($requestedPath) : '';

        $userId = auth()->id();
        $lastPathCacheKey = $userId ? ('explorer_last_path:user:' . $userId) : null;

        $lastPathTtl = now()->addMinutes(15);

        if ($requestedPathStr === '') {
            $cachedLastPath = $lastPathCacheKey ? Cache::get($lastPathCacheKey) : null;

            $currentPath = (is_string($cachedLastPath) && trim($cachedLastPath) !== '')
                ? $cachedLastPath
                : $basePath;
        } else {
            $currentPath = $requestedPathStr;
        }

        $currentPathReal = realpath($currentPath);
        $basePathReal = realpath($basePath);

        if (
            !$currentPathReal ||
            !File::isDirectory($currentPathReal) ||
            ($basePathReal && !str_starts_with($currentPathReal, $basePathReal))
        ) {
            $currentPath = $basePath;
            $currentPathReal = realpath($currentPath) ?: $currentPath;
        }

        if ($lastPathCacheKey) {
            Cache::put($lastPathCacheKey, $currentPathReal, $lastPathTtl);
        }

        try {
            $cacheKey = 'explorer_' . md5($currentPathReal);

            $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($currentPathReal) {
                $directories = [];
                $files = [];

                $dirList = File::directories($currentPathReal);
                foreach ($dirList as $path) {
                    $directories[] = [
                        'name' => basename($path),
                        'path' => str_replace('\\', '/', $path),
                        'type' => 'directory',
                    ];
                }

                foreach (File::files($currentPathReal) as $file) {
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $this->allowedExtensions, true)) {
                        continue;
                    }

                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => str_replace('\\', '/', $file->getRealPath()),
                        'type' => 'file',
                        'extension' => $ext,
                        'size' => $file->getSize(),
                    ];
                }

                return [
                    'directories' => $directories,
                    'files' => $files,
                ];
            });

            return response()->json([
                'success' => true,
                'current_path' => str_replace('\\', '/', $currentPathReal),
                'parent_path' => str_replace('\\', '/', dirname($currentPathReal)),
                'directories' => $data['directories'],
                'files' => $data['files'],
                'sep' => DIRECTORY_SEPARATOR,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Serve file untuk preview (gambar / PDF).
     */
    public function previewFile(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $path = $request->query('path');
        if (!$path || trim($path) === '') {
            abort(404);
        }

        // $basePath = realpath(config('app.explorer_base_path', 'F:/PESANAN/'));
        // $realPath = realpath($path);
        $realPath = $this->resolvePreviewPath($path);
        if (!$realPath) {
            abort(404);
        }
        // if (!$realPath || !str_starts_with($realPath, $basePath)) {
        //     abort(403, 'Akses path tidak diizinkan.');
        // }

        $mime = \Illuminate\Support\Facades\File::mimeType($realPath);
        $allowedMimes = [
            'image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
            'application/pdf'
        ];
        if (!in_array($mime, $allowedMimes)) {
            abort(403, 'Tipe file tidak didukung untuk preview.');
        }

        // ETag & Last-Modified berdasarkan path + mtime + size agar cache invalid saat isi file berubah
        $mtime = filemtime($realPath);
        $size = filesize($realPath);
        $etag = '"' . md5($realPath . $mtime . $size) . '"';
        $lastModified = gmdate('D, d M Y H:i:s \G\M\T', $mtime);

        $request->headers->set('If-None-Match', $request->header('If-None-Match'));
        if ($request->header('If-None-Match') === $etag) {
            return response('', 304)->withHeaders([
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'ETag' => $etag,
                'Last-Modified' => $lastModified,
            ]);
        }
        if ($request->header('If-Modified-Since') === $lastModified) {
            return response('', 304)->withHeaders([
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'ETag' => $etag,
                'Last-Modified' => $lastModified,
            ]);
        }

        $headers = [
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'ETag' => $etag,
            'Last-Modified' => $lastModified,
        ];

        if ($mime === 'application/pdf') {
            return response()->file($realPath, array_merge($headers, ['Content-Type' => $mime]));
        }

        try {
            $img = Image::read($realPath)
                ->scaleDown(width: 1024)
                ->toWebp(quality: 70);

            return response($img->toString(), 200, array_merge($headers, [
                'Content-Type' => 'image/webp',
            ]));
        } catch (\Exception $e) {
            return response()->stream(function () use ($realPath) {
                $stream = fopen($realPath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, array_merge($headers, ['Content-Type' => $mime]));
        }
    }

    /**
     * Cek apakah file ada (untuk deteksi file _output).
     */
    public function fileExists(Request $request): JsonResponse
    {
        $path = $request->query('path');

        if (!$path || trim($path) === '') {
            return response()->json(['success' => true, 'exists' => false])
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($path));
        $dir = dirname($normalized);
        $baseName = pathinfo($normalized, PATHINFO_FILENAME);

        if (!$dir || !$baseName || !is_dir($dir)) {
            return response()->json(['success' => true, 'exists' => false])
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'BMP'];
        $exists = false;

        foreach ($extensions as $ext) {
            $tryPath = $dir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;
            if (File::exists($tryPath) && is_file($tryPath)) {
                $exists = true;
                break;
            }
        }

        return response()->json(['success' => true, 'exists' => $exists])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

        /**
     * Resolve path ke file yang benar-benar ada (nama file tanpa ketat ekstensi).
     */
    private function resolvePreviewPath(string $path): ?string
    {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($path));
        if (File::exists($normalized) && is_file($normalized)) {
            return realpath($normalized) ?: $normalized;
        }

        $dir = dirname($normalized);
        $baseName = pathinfo($normalized, PATHINFO_FILENAME);
        if (!$dir || !$baseName || !is_dir($dir)) {
            return null;
        }

        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'pdf', 'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'BMP', 'PDF'];
        foreach ($extensions as $ext) {
            $tryPath = $dir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;
            if (File::exists($tryPath) && is_file($tryPath)) {
                return realpath($tryPath) ?: $tryPath;
            }
        }

        return null;
    }

    public function getImageInfo(Request $request): JsonResponse
    {
        $path = $request->query('path');
        if (!$path || !File::exists($path)) {
            return response()->json(['success' => false, 'message' => 'Path tidak valid'], 404);
        }

        $basePath = realpath(config('app.explorer_base_path', 'F:/PESANAN/'));
        $realPath = realpath($path);
        if (!$basePath || !$realPath || !str_starts_with($realPath, $basePath)) {
            return response()->json(['success' => false, 'message' => 'Akses path tidak diizinkan'], 403);
        }

        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        if (!in_array($ext, $imageExts)) {
            return response()->json(['success' => false, 'message' => 'Hanya file gambar yang didukung'], 400);
        }

        $baseUrl = rtrim(config('app.file_info_api_base_url', 'http://127.0.0.1:9001'), '/');
        $apiUrl = $baseUrl . '/ui/read-info';

        $cacheKey = 'image_info:' . sha1($realPath . '|' . filemtime($realPath));
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            return response()->json(['success' => true, 'data' => $data]);
        }
        try {
            $response = Http::timeout(10)->post($apiUrl, [
                'filepath' => $realPath,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil info file dari API',
                ], 502);
            }

            $data = $response->json();
            Cache::put($cacheKey, $data, 3600);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 502);
        }
    }

    public function getPdfInfo(Request $request): JsonResponse
    {
        $path = $request->query('path');
        if (!$path || !File::exists($path)) {
            return response()->json(['success' => false, 'message' => 'Path tidak valid'], 404);
        }

        $basePath = realpath(config('app.explorer_base_path', 'F:/PESANAN/'));
        $realPath = realpath($path);
        if (!$basePath || !$realPath || !str_starts_with($realPath, $basePath)) {
            return response()->json(['success' => false, 'message' => 'Akses path tidak diizinkan'], 403);
        }

        $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return response()->json(['success' => false, 'message' => 'Hanya file PDF yang didukung'], 400);
        }

        $baseUrl = rtrim(config('app.file_info_api_base_url', 'http://127.0.0.1:9001'), '/');
        $apiUrl = $baseUrl . '/ui/read-pdf-info';

        $cacheKey = 'pdf_info:' . sha1($realPath . '|' . filemtime($realPath));
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            return response()->json(['success' => true, 'data' => $data]);
        }
        
        try {
            $response = Http::timeout(10)->post($apiUrl, [
                'filepath' => $realPath,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil info PDF dari API',
                ], 502);
            }

            $data = $response->json();
            Cache::put($cacheKey, $data, 3600);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 502);
        }
    }

    public function processImageTools(Request $request): JsonResponse
    {
        $data = $request->all();
        $filepath = $data['AlamatFile'] ?? null;

        if (!$filepath || !File::exists($filepath)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Path file tidak valid.',
            ], 404);
        }

        $basePath = realpath(config('app.explorer_base_path', 'F:/PESANAN/'));
        $realPath = realpath($filepath);

        if (!$basePath || !$realPath || !str_starts_with($realPath, $basePath)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses path tidak diizinkan.',
            ], 403);
        }

        $baseUrl = rtrim(config('app.file_info_api_base_url', 'http://127.0.0.1:9001'), '/');
        $apiUrl  = $baseUrl . '/ui/image-processing';

        try {
            $response = Http::timeout(20)->post($apiUrl, $data);

            if (!$response->successful()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal memproses gambar di API backend.',
                ], 502);
            }

            $json = $response->json() ?? [];

            return response()->json($json);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ], 502);
        }
    }

    public function openFolderLocation(Request $request): JsonResponse
    {
        $path = $request->query('path');
        if (!$path || !File::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Path tidak valid'
            ], 404);
        }

        $basePath = realpath(config('app.explorer_base_path', 'F:/PESANAN/'));
        $realPath = realpath($path);
        
        if (!$basePath || !$realPath || !str_starts_with($realPath, $basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses path tidak diizinkan'
            ], 403);
        }

        try {
            if (is_file($realPath)) {
                $command = 'explorer.exe /select,"' . str_replace('/', '\\', $realPath) . '"';
            } else {
                $command = 'explorer.exe "' . str_replace('/', '\\', $realPath) . '"';
            }
            
            if (PHP_OS_FAMILY === 'Windows') {
                pclose(popen('start /B ' . $command, 'r'));
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Fitur ini hanya tersedia di Windows'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Folder dibuka di Windows Explorer'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
