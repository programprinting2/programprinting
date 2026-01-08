<?php

namespace App\Http\Controllers;

use App\Exceptions\PemasokNotFoundException;
use App\Exceptions\InvalidPemasokDataException;
use App\Http\Requests\StorePemasokRequest;
use App\Http\Requests\UpdatePemasokRequest;
use App\Services\PemasokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PemasokController extends Controller
{
    public function __construct(
        private PemasokService $pemasokService
    ) {}

    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ];

            $pemasok = $this->pemasokService->getPaginatedPemasok(10, $filters);

            if ($request->has('search')) {
                $pemasok->appends(['search' => $request->search]);
            }
            if ($request->has('status')) {
                $pemasok->appends(['status' => $request->status]);
            }

            return view('backend.pemasok.index', compact('pemasok'));
        } catch (\Exception $e) {
            Log::error('Error loading Pemasok index', ['error' => $e->getMessage()]);
            return view('backend.pemasok.index', [
                'pemasok' => collect()->paginate()
            ])->with('error', 'Gagal memuat data pemasok');
        }
    }

    public function store(StorePemasokRequest $request)
    {
        try {
            $data = $request->validated();
            $pemasok = $this->pemasokService->createPemasok($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Data pemasok berhasil ditambahkan.'
            ]);
        } catch (InvalidPemasokDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Pemasok', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function update(UpdatePemasokRequest $request, \App\Models\Pemasok $pemasok)
    {
        try {
            $data = $request->validated();
            $this->pemasokService->updatePemasok($pemasok->id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Data pemasok berhasil diperbarui.'
            ]);
        } catch (PemasokNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidPemasokDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Pemasok', [
                'pemasok_id' => $pemasok->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy(\App\Models\Pemasok $pemasok)
    {
        try {
            $this->pemasokService->deletePemasok($pemasok->id);
            return redirect()->route('backend.pemasok.index')
                ->with('success', 'Data pemasok berhasil dihapus.');
        } catch (PemasokNotFoundException $e) {
            return redirect()->route('backend.pemasok.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Pemasok', [
                'pemasok_id' => $pemasok->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('backend.pemasok.index')
                ->with('error', 'Gagal menghapus pemasok');
        }
    }

    public function show($id)
    {
        try {
            $pemasok = $this->pemasokService->getPemasok($id);
            return response()->json([
                'success' => true,
                'data' => $pemasok
            ]);
        } catch (PemasokNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing Pemasok', [
                'pemasok_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pemasok.'
            ], 500);
        }
    }
}
