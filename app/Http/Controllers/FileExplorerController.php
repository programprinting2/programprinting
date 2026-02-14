<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

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

            return response()->json([
                'success' => true,
                'current_path' => str_replace('\\', '/', $currentPath),
                'parent_path' => str_replace('\\', '/', dirname($currentPath)),
                'directories' => $directories,
                'files' => $files,
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
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
            'application/pdf'
        ];
        if (!in_array($mime, $allowedMimes)) {
            abort(403, 'Tipe file tidak didukung untuk preview.');
        }

        if ($mime === 'application/pdf') {
            return response()->file($realPath, ['Content-Type' => $mime]);
        }

        $img = Image::make($realPath);

        $img->resize(1024, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    
        return response($img->encode('webp'), 200)
            ->header('Content-Type', 'image/webp');
    }
}
