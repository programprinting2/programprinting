<?php

namespace App\Http\Controllers;

use App\Exceptions\MesinNotFoundException;
use App\Exceptions\InvalidMesinDataException;
use App\Http\Requests\StoreMesinRequest;
use App\Http\Requests\UpdateMesinRequest;
use App\Models\MasterParameter;
use App\Services\MesinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class MesinController extends Controller
{
    public function __construct(
        private MesinService $mesinService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'type' => $request->input('type'),
                'status' => $request->input('status'),
            ];

            $mesin = $this->mesinService->getPaginatedMesin(10, $filters);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('backend.master-mesin.partials.card-view', compact('mesin'))->render(),
                    'table_html' => view('backend.master-mesin.partials.table-view', compact('mesin'))->render(),
                    'pagination' => [
                        'total' => $mesin->total(),
                        'per_page' => $mesin->perPage(),
                        'current_page' => $mesin->currentPage(),
                        'last_page' => $mesin->lastPage(),
                    ],
                    'total_count' => $mesin->total()
                ]);
            }

            // Dropdown data
            $masterTipeMesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
            $tipe_mesin = $masterTipeMesin ? $masterTipeMesin->details()->where('aktif', 1)->get() : collect();
            $paramModeWarna = MasterParameter::where('nama_parameter', 'MODE WARNA')->first();
            $mode_warna_options = $paramModeWarna ? $paramModeWarna->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];
            return view('backend.master-mesin.index', compact('mesin', 'tipe_mesin', 'mode_warna_options'));

        } catch (\Exception $e) {
            Log::error('Error in mesin index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat data mesin'
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $masterTipeMesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
        $tipeMesin = $masterTipeMesin ? $masterTipeMesin->details()->where('aktif', 1)->get() : collect();
        $paramModeWarna = MasterParameter::where('nama_parameter', 'MODE WARNA')->first();
        $modeWarnaOptions = $paramModeWarna ? $paramModeWarna->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];

        return view('backend.master-mesin-form', compact('tipeMesin', 'modeWarnaOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMesinRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $data = $request->validated();
            $gambar = $request->file('gambar');

            $mesin = $this->mesinService->createMesin($data, $gambar);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesin berhasil ditambahkan',
                    'mesin' => $mesin
                ]);
            }

            return redirect()
                ->route('backend.master-mesin.index')
                ->with('success', 'Mesin berhasil ditambahkan');

        } catch (InvalidMesinDataException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Unexpected error creating mesin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        try {
            $mesin = $this->mesinService->getMesin($id);
            return view('backend.master-mesin-detail', compact('mesin'));

        } catch (MesinNotFoundException $e) {
            return redirect()
                ->route('backend.master-mesin.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        try {
            $mesin = $this->mesinService->getMesin($id);

            $masterTipeMesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
            $tipeMesin = $masterTipeMesin ? $masterTipeMesin->details()->where('aktif', 1)->get() : collect();
            $paramModeWarna = MasterParameter::where('nama_parameter', 'MODE WARNA')->first();
            $modeWarnaOptions = $paramModeWarna ? $paramModeWarna->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];

            return view('backend.master-mesin-form', compact('mesin', 'tipeMesin', 'modeWarnaOptions'));

        } catch (MesinNotFoundException $e) {
            return redirect()
                ->route('backend.master-mesin.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMesinRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['hapus_gambar'] = $request->input('hapus_gambar', false);
            $gambar = $request->file('gambar');

            $mesin = $this->mesinService->updateMesin($id, $data, $gambar);

            return response()->json([
                'success' => true,
                'message' => 'Data mesin berhasil diperbarui',
                'mesin' => $mesin
            ]);

        } catch (MesinNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        } catch (InvalidMesinDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Unexpected error updating mesin', [
                'mesin_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->mesinService->deleteMesin($id);

            return redirect()
                ->route('backend.master-mesin.index')
                ->with('success', 'Mesin berhasil dihapus');

        } catch (MesinNotFoundException $e) {
            return redirect()
                ->route('backend.master-mesin.index')
                ->with('error', $e->getMessage());

        } catch (InvalidMesinDataException $e) {
            return redirect()
                ->route('backend.master-mesin.index')
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Unexpected error deleting mesin', [
                'mesin_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('backend.master-mesin.index')
                ->with('error', 'Terjadi kesalahan sistem');
        }
    }

    /**
     * Remove specific biaya tambahan from profile
     */
    public function removeBiayaTambahan(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'profile_index' => 'required|integer|min:0',
                'biaya_nama' => 'required|string|max:255'
            ]);

            $profileIndex = $request->input('profile_index');
            $biayaNama = trim($request->input('biaya_nama'));

            $mesin = $this->mesinService->removeBiayaTambahan($id, $profileIndex, $biayaNama);

            return response()->json([
                'success' => true,
                'message' => 'Biaya tambahan berhasil dihapus',
                'mesin' => $mesin,
                'biaya_perhitungan_profil' => $mesin->biaya_perhitungan_profil
            ]);

        } catch (MesinNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        } catch (InvalidMesinDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Unexpected error removing biaya tambahan', [
                'mesin_id' => $id,
                'profile_index' => $request->input('profile_index'),
                'biaya_nama' => $request->input('biaya_nama'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
}