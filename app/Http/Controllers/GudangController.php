<?php

namespace App\Http\Controllers;

use App\Exceptions\GudangNotFoundException;
use App\Exceptions\InvalidGudangDataException;
use App\Http\Requests\StoreGudangRequest;
use App\Http\Requests\UpdateGudangRequest;
use App\Models\Rak;
use App\Services\GudangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GudangController extends Controller
{
    public function __construct(
        private GudangService $gudangService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ];

            $gudang = $this->gudangService->getPaginatedGudang(10, $filters);

            if ($request->has('search')) {
                $gudang->appends(['search' => $request->search]);
            }
            if ($request->has('status')) {
                $gudang->appends(['status' => $request->status]);
            }

            $totalRak = Rak::count();
            return view('backend.master-gudang.index', compact('gudang', 'totalRak'));
        } catch (\Exception $e) {
            Log::error('Error loading Gudang index', ['error' => $e->getMessage()]);
            return view('backend.master-gudang.index', [
                'gudang' => collect()->paginate(),
                'totalRak' => 0
            ])->with('error', 'Gagal memuat data gudang');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGudangRequest $request)
    {
        try {
            $gudang = $this->gudangService->createGudang($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil ditambahkan.'
            ]);
        } catch (InvalidGudangDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Gudang', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function show(\App\Models\Gudang $gudang): View
    {
        return view('gudang.show', compact('gudang'));
    }

    public function edit(\App\Models\Gudang $gudang)
    {
        try {
            $gudang = $this->gudangService->getGudang($gudang->id);
            return response()->json([
                'success' => true,
                'data' => $gudang
            ]);
        } catch (GudangNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error loading Gudang edit', [
                'gudang_id' => $gudang->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data gudang'
            ], 500);
        }
    }

    public function update(UpdateGudangRequest $request, int $id)
    {
        try {
            $this->gudangService->updateGudang($id, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil diperbarui.'
            ]);
        } catch (GudangNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidGudangDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Gudang', [
                'gudang_id' => $id,
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
            $this->gudangService->deleteGudang($id);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data gudang berhasil dihapus'
                ]);
            }
            
            return redirect()->route('backend.master-gudang.index')
                ->with('success', 'Gudang berhasil dihapus.');
        } catch (GudangNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 404);
            }
            return redirect()->route('backend.master-gudang.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Gudang', [
                'gudang_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data gudang'
                ], 500);
            }
            
            return redirect()->route('backend.master-gudang.index')
                ->with('error', 'Gagal menghapus gudang.');
        }
    }
}
