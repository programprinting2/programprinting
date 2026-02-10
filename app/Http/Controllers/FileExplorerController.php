<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

class FileExplorerController extends Controller
{
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
            $fileList = File::files($currentPath);
            foreach ($fileList as $file) {
                try {
                    if (!$file->isReadable()) {
                        continue;
                    }

                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => str_replace('\\', '/', $file->getRealPath()),
                        'type' => 'file',
                        'size' => $file->getSize(),
                        'extension' => $file->getExtension()
                    ];
                } catch (\Throwable $e) {
                    continue;
                }
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
}
