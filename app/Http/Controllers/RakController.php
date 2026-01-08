<?php

namespace App\Http\Controllers;

use App\Exceptions\RakNotFoundException;
use App\Exceptions\InvalidRakDataException;
use App\Http\Requests\StoreRakRequest;
use App\Http\Requests\UpdateRakRequest;
use App\Models\Gudang;
use App\Services\RakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RakController extends Controller
{
    public function __construct(
        private RakService $rakService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'gudang_id' => $request->input('gudang_id'),
            ];

            $rak = $this->rakService->getPaginatedRak(10, $filters);
            $gudang = Gudang::orderBy('nama_gudang')->get();
            $totalGudang = Gudang::count();
            
            return view('backend.master-rak.index', compact('rak', 'gudang', 'totalGudang'));
        } catch (\Exception $e) {
            Log::error('Error loading Rak index', ['error' => $e->getMessage()]);
            return view('backend.master-rak.index', [
                'rak' => collect()->paginate(),
                'gudang' => collect(),
                'totalGudang' => 0
            ])->with('error', 'Gagal memuat data rak');
        }
    }

    public function store(StoreRakRequest $request)
    {
        try {
            $rak = $this->rakService->createRak($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Rak berhasil ditambahkan',
                'data' => $rak
            ]);
        } catch (InvalidRakDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Rak', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function update(UpdateRakRequest $request, int $id)
    {
        try {
            $rak = $this->rakService->updateRak($id, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Rak berhasil diupdate',
                'data' => $rak
            ]);
        } catch (RakNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidRakDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Rak', [
                'rak_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->rakService->deleteRak($id);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rak berhasil dihapus'
                ]);
            }
            
            return redirect()->route('backend.master-rak.index')
                ->with('success', 'Rak berhasil dihapus.');
        } catch (RakNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 404);
            }
            return redirect()->route('backend.master-rak.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Rak', [
                'rak_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus rak'
                ], 500);
            }
            
            return redirect()->route('backend.master-rak.index')
                ->with('error', 'Gagal menghapus rak');
        }
    }

    public function show(int $id)
    {
        try {
            $rak = $this->rakService->getRak($id);
            return response()->json([
                'success' => true,
                'data' => $rak
            ]);
        } catch (RakNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing Rak', [
                'rak_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data rak'
            ], 500);
        }
    }
}
