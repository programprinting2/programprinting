<?php

namespace App\Http\Controllers;

use App\Exceptions\SpkCreationException;
use App\Exceptions\SpkNotFoundException;
use App\Exceptions\InvalidSpkDataException;
use App\Http\Requests\StoreSpkRequest;
use App\Http\Requests\UpdateSpkRequest;
use App\Models\Pelanggan;
use App\Models\BahanBaku;
use App\Models\SPK;
use App\Services\SpkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SPKController extends Controller
{
    public function __construct(
        private SpkService $spkService
    ) {}

    /**
     * Display a listing of SPKs
     */
    public function index(): View
    {
        try {
            $spk = $this->spkService->getPaginatedSpk(10);
            $customers = Pelanggan::where('status', true)->get();

            return view('pages.spk.index', compact('spk', 'customers'));
        } catch (\Exception $e) {
            Log::error('Error loading SPK index', ['error' => $e->getMessage()]);
            return view('pages.spk.index', [
                'spk' => collect()->paginate(),
                'customers' => collect()
            ])->with('error', 'Gagal memuat data SPK');
        }
    }

    /**
     * Show the form for creating a new SPK
     */
    public function create(): View
    {
        $customers = Pelanggan::where('status', true)->get();
        $bahanBaku = BahanBaku::where('status_aktif', true)->get();

        return view('pages.spk.create', compact('customers', 'bahanBaku'));
    }

    /**
     * Store a newly created SPK
     */
    public function store(StoreSpkRequest $request): RedirectResponse
    {
        try {
            $spk = $this->spkService->createSpk($request->validated());

            return redirect()
                ->route('spk.show', $spk)
                ->with('success', "SPK berhasil dibuat dengan nomor: {$spk->nomor_spk}");

        } catch (SpkCreationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->getErrors())
                ->with('error', $e->getMessage());

        } catch (InvalidSpkDataException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Unexpected error creating SPK', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified SPK
     */
    public function show(SPK $spk): View
    {
        try {
            $spk = $this->spkService->getSpkWithRelations($spk->id);

            return view('pages.spk.show', compact('spk'));

        } catch (SpkNotFoundException $e) {
            return redirect()
                ->route('spk.index')
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Error showing SPK', ['spk_id' => $spk->id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('spk.index')
                ->with('error', 'Gagal memuat detail SPK');
        }
    }

    /**
     * Show the form for editing the specified SPK
     */
    public function edit(SPK $spk): View
    {
        try {
            $spk = $this->spkService->getSpkWithRelations($spk->id);
            $customers = Pelanggan::where('status', true)->get();
            $bahanBaku = BahanBaku::where('status_aktif', true)->get();

            return view('pages.spk.edit', compact('spk', 'customers', 'bahanBaku'));

        } catch (SpkNotFoundException $e) {
            return redirect()
                ->route('spk.index')
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Error loading SPK edit form', ['spk_id' => $spk->id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('spk.index')
                ->with('error', 'Gagal memuat form edit SPK');
        }
    }

    /**
     * Update the specified SPK
     */
    public function update(UpdateSpkRequest $request, SPK $spk): RedirectResponse
    {
        try {
            $spk = $this->spkService->updateSpk($spk->id, $request->validated());

            return redirect()
                ->route('spk.show', $spk)
                ->with('success', 'SPK berhasil diperbarui');

        } catch (SpkNotFoundException $e) {
            return redirect()
                ->route('spk.index')
                ->with('error', $e->getMessage());

        } catch (InvalidSpkDataException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Unexpected error updating SPK', [
                'spk_id' => $spk->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified SPK
     */
    public function destroy(SPK $spk): RedirectResponse
    {
        try {
            $this->spkService->deleteSpk($spk->id);

            return redirect()
                ->route('spk.index')
                ->with('success', 'SPK berhasil dihapus');

        } catch (SpkNotFoundException $e) {
            return redirect()
                ->route('spk.index')
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Error deleting SPK', ['spk_id' => $spk->id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('spk.index')
                ->with('error', 'Gagal menghapus SPK');
        }
    }
} 