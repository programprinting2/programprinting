<?php

namespace App\Http\Controllers;

use App\Exceptions\PelangganNotFoundException;
use App\Exceptions\InvalidPelangganDataException;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use App\Services\PelangganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelangganController extends Controller
{
    public function __construct(
        private PelangganService $pelangganService
    ) {}

    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ];

            $pelanggan = $this->pelangganService->getPaginatedPelanggan(10, $filters);

            // Preserve query parameters in pagination
            if ($request->has('search')) {
                $pelanggan->appends(['search' => $request->search]);
            }
            if ($request->has('status')) {
                $pelanggan->appends(['status' => $request->status]);
            }

            return view('backend.pelanggan.index', compact('pelanggan'));
        } catch (\Exception $e) {
            Log::error('Error loading Pelanggan index', ['error' => $e->getMessage()]);
            return view('backend.pelanggan.index', [
                'pelanggan' => collect()->paginate()
            ])->with('error', 'Gagal memuat data pelanggan');
        }
    }

    public function create()
    {
        return view('backend.pelanggan.create');
    }

    public function store(StorePelangganRequest $request)
    {
        try {
            $data = $request->validated();
            $data['wajib_pajak'] = $request->boolean('wajib_pajak');
            $data['data_lain']['batas_umur_faktur_check'] = $request->boolean('data_lain.batas_umur_faktur_check');
            $data['data_lain']['batas_total_piutang_check'] = $request->boolean('data_lain.batas_total_piutang_check');

            $pelanggan = $this->pelangganService->createPelanggan($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil ditambahkan.'
            ]);
        } catch (InvalidPelangganDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Pelanggan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('backend.pelanggan.form', [
            'pelanggan' => $pelanggan,
            'title' => 'Edit Pelanggan',
            'method' => 'PUT',
            'action' => route('backend.pelanggan.update', $pelanggan->id)
        ]);
    }

    public function update(UpdatePelangganRequest $request, \App\Models\Pelanggan $pelanggan)
    {
        try {
            $data = $request->validated();
            
            $this->pelangganService->updatePelanggan($pelanggan->id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui.'
            ]);
        } catch (PelangganNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (InvalidPelangganDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Pelanggan', [
                'pelanggan_id' => $pelanggan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy(\App\Models\Pelanggan $pelanggan)
    {
        try {
            $this->pelangganService->deletePelanggan($pelanggan->id);
            return redirect()->route('backend.pelanggan.index')
                ->with('success', 'Data pelanggan berhasil dihapus.');
        } catch (PelangganNotFoundException $e) {
            return redirect()->route('backend.pelanggan.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Pelanggan', [
                'pelanggan_id' => $pelanggan->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('backend.pelanggan.index')
                ->with('error', 'Gagal menghapus pelanggan');
        }
    }

    public function show($id)
    {
        try {
            $pelanggan = $this->pelangganService->getPelanggan($id);
            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        } catch (PelangganNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing Pelanggan', [
                'pelanggan_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pelanggan.'
            ], 500);
        }
    }
}