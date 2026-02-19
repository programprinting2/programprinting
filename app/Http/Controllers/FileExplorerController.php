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
        $currentPath = $request->get('path', $basePath);

        if (!File::exists($currentPath) || !File::isDirectory($currentPath)) {
            $currentPath = $basePath;
        }

        try {
            // cache key unik berdasarkan path
            $cacheKey = 'explorer_' . md5($currentPath);

            $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($currentPath) {

                $directories = [];
                $files = [];

                // Get directories
                $dirList = File::directories($currentPath);
                foreach ($dirList as $path) {
                    $directories[] = [
                        'name' => basename($path),
                        'path' => str_replace('\\', '/', $path),
                        'type' => 'directory'
                    ];
                }

                // Get files
                foreach (File::files($currentPath) as $file) {
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $this->allowedExtensions)) continue;

                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => str_replace('\\', '/', $file->getRealPath()),
                        'type' => 'file',
                        'extension' => $ext,
                        'size' => $file->getSize()
                    ];
                }

                return [
                    'directories' => $directories,
                    'files' => $files,
                ];
            });

            return response()->json([
                'success' => true,
                'current_path' => str_replace('\\', '/', $currentPath),
                'parent_path' => str_replace('\\', '/', dirname($currentPath)),
                'directories' => $data['directories'],
                'files' => $data['files'],
                'sep' => DIRECTORY_SEPARATOR
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve file untuk preview (gambar / PDF).
     */
    public function previewFile(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $path = $request->query('path');
        if (!$path || !\Illuminate\Support\Facades\File::exists($path)) {
            abort(404);
        }

        $basePath = realpath(config('app.explorer_base_path', 'F:/PESANAN/'));
        $realPath = realpath($path);
        if (!$realPath || !str_starts_with($realPath, $basePath)) {
            abort(403, 'Akses path tidak diizinkan.');
        }

        $mime = \Illuminate\Support\Facades\File::mimeType($realPath);
        $allowedMimes = [
            'image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
            'application/pdf'
        ];
        if (!in_array($mime, $allowedMimes)) {
            abort(403, 'Tipe file tidak didukung untuk preview.');
        }

        if ($mime === 'application/pdf') {
            return response()->file($realPath, ['Content-Type' => $mime, 'Cache-Control' => 'public, max-age=604800']);
        }

        try {
            $img = Image::read($realPath)
                ->scaleDown(width: 1024)
                ->toWebp(quality: 70);
                
                return response($img->toString(), 200, [
                    'Content-Type' => 'image/webp',
                    'Cache-Control' => 'public, max-age=604800',
                ]);
        } catch (\Exception $e) {
            return response()->stream(function() use ($realPath) {
                $stream = fopen($realPath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, ['Content-Type' => $mime]);
        }
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
