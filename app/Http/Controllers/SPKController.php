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
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Illuminate\Http\Request;

class SPKController extends Controller
{
    public function __construct(
        private SpkService $spkService
    ) {
    }

    /**
     * Display a listing of SPKs
     */
    public function index(Request $request): View
    {
        try {
            $filters = $request->only([
                'search',
                'customer_id',
                'status',
            ]);
    
            $filters['created_by'] = auth()->id() ?? 1; 
    
            $spk = $this->spkService->getPaginatedSpk(10, $filters);
            $customers = Pelanggan::where('status', true)->get();
    
            return view('pages.spk.index', compact('spk', 'customers'));
        } catch (\Exception $e) {
            Log::error('Error loading SPK index', ['error' => $e->getMessage()]);
            return view('pages.spk.index', [
                'spk' => new LengthAwarePaginator([], 0, 10),
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

    /**
     * ACC SPK
     */
    public function accToPayment(SPK $spk): RedirectResponse
    {
        try {
            if ($spk->status !== 'draft') {
                return redirect()
                    ->route('spk.index')
                    ->with('error', 'Hanya SPK dengan status draft yang dapat di-ACC ke proses pembayaran.');
            }

            $spk->update([
                'status' => 'proses_bayar',
                'updated_by' => 1,//auth()->id(),
            ]);

            // ActivityLogService::log($spk, 'spk_acc_proses_bayar', 'SPK di-ACC ke proses bayar', 'info');

            return redirect()
                ->route('kasir.index')
                ->with('success', 'SPK berhasil di-ACC ke proses pembayaran. Silakan proses di kasir.');
        } catch (\Exception $e) {
            \Log::error('Error ACC SPK ke proses bayar', [
                'spk_id' => $spk->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('spk.index')
                ->with('error', 'Gagal ACC SPK ke proses pembayaran.');
        }
    }

    public function updateStatus(Request $request, SPK $spk): RedirectResponse
    {
        $action = $request->input('action'); 

        $flow = [
            'draft',
            'proses_bayar',
            'manager_approval_order',
            'manager_approval_produksi',
            'operator_cetak',
            'finishing_qc',
            'siap_diambil',
            'selesai',
        ];

        $currentStatus = $spk->status;
        $index = array_search($currentStatus, $flow, true);

        if ($index === false) {
            return back()->with('error', 'Status SPK saat ini tidak dikenali.');
        }

        $newStatus = $currentStatus;

        if ($action === 'approve') {
            if ($index < count($flow) - 1) {
                $newStatus = $flow[$index + 1];
            }
        } elseif ($action === 'reject') {
            if ($index > 0) {
                $newStatus = $flow[$index - 1];
            }
        } else {
            return back()->with('error', 'Aksi status tidak valid.');
        }

        if ($newStatus === $currentStatus) {
            return back()->with('error', 'Tidak ada perubahan status yang dilakukan.');
        }

        $spk->update(['status' => $newStatus]);

        return back()->with('success', "Status SPK berhasil diubah menjadi {$newStatus}.");
    }
}