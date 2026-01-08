<?php

namespace App\Http\Controllers;

use App\Exceptions\KaryawanNotFoundException;
use App\Exceptions\InvalidKaryawanDataException;
use App\Http\Requests\StoreKaryawanRequest;
use App\Http\Requests\UpdateKaryawanRequest;
use App\Services\KaryawanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    public function __construct(
        private KaryawanService $karyawanService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ];

            $karyawan = $this->karyawanService->getPaginatedKaryawan(10, $filters);

            if ($request->has('search')) {
                $karyawan->appends(['search' => $request->search]);
            }
            if ($request->has('status')) {
                $karyawan->appends(['status' => $request->status]);
            }

            return view('backend.karyawan.index', compact('karyawan'));
        } catch (\Exception $e) {
            Log::error('Error loading Karyawan index', ['error' => $e->getMessage()]);
            return view('backend.karyawan.index', [
                'karyawan' => collect()->paginate()
            ])->with('error', 'Gagal memuat data karyawan');
        }
    }

    public function create()
    {
        return view('backend.karyawan.form', [
            'karyawan' => new Karyawan(),
            'title' => 'Tambah Karyawan',
            'method' => 'POST',
            'action' => route('backend.karyawan.store')
        ]);
    }

    public function store(StoreKaryawanRequest $request)
    {
        try {
            $data = $request->validated();
            $karyawan = $this->karyawanService->createKaryawan($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil ditambahkan'
            ]);
        } catch (InvalidKaryawanDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Karyawan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function edit(int $id): View
    {
        try {
            $karyawan = $this->karyawanService->getKaryawan($id);
            return view('backend.karyawan.form', [
                'karyawan' => $karyawan,
                'title' => 'Edit Karyawan',
                'method' => 'PUT',
                'action' => route('backend.karyawan.update', $karyawan->id)
            ]);
        } catch (KaryawanNotFoundException $e) {
            return redirect()->route('backend.karyawan.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error loading Karyawan edit form', [
                'karyawan_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('backend.karyawan.index')
                ->with('error', 'Gagal memuat form edit karyawan');
        }
    }

    public function update(UpdateKaryawanRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            $this->karyawanService->updateKaryawan($id, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui'
            ]);
        } catch (KaryawanNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidKaryawanDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Karyawan', [
                'karyawan_id' => $id,
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
            $this->karyawanService->deleteKaryawan($id);
            return redirect()
                ->route('backend.karyawan.index')
                ->with('success', 'Data karyawan berhasil dihapus');
        } catch (KaryawanNotFoundException $e) {
            return redirect()->route('backend.karyawan.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Karyawan', [
                'karyawan_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('backend.karyawan.index')
                ->with('error', 'Gagal menghapus karyawan');
        }
    }

    public function show(int $id)
    {
        try {
            $karyawan = $this->karyawanService->getKaryawan($id);
            return response()->json($karyawan);
        } catch (KaryawanNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing Karyawan', [
                'karyawan_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data karyawan'
            ], 500);
        }
    }
}
