<?php

namespace App\Http\Controllers;

use App\Exceptions\PembelianNotFoundException;
use App\Exceptions\InvalidPembelianDataException;
use App\Http\Requests\StorePembelianRequest;
use App\Http\Requests\UpdatePembelianRequest;
use App\Models\Pemasok;
use App\Models\BahanBaku;
use App\Models\SubDetailParameter;
use App\Services\PembelianService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PembelianController extends Controller
{
    public function __construct(
        private PembelianService $pembelianService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'pemasok_id' => $request->input('pemasok_id'),
                'tanggal_dari' => $request->input('tanggal_dari'),
                'tanggal_sampai' => $request->input('tanggal_sampai'),
            ];

            $data_pembelian = $this->pembelianService->getPaginatedPembelian(10, $filters);

            // Preserve query parameters
            if ($request->has('search')) {
                $data_pembelian->appends(['search' => $request->search]);
            }
            if ($request->has('pemasok_id')) {
                $data_pembelian->appends(['pemasok_id' => $request->pemasok_id]);
            }
            if ($request->has('tanggal_dari')) {
                $data_pembelian->appends(['tanggal_dari' => $request->tanggal_dari]);
            }
            if ($request->has('tanggal_sampai')) {
                $data_pembelian->appends(['tanggal_sampai' => $request->tanggal_sampai]);
            }

            $pemasok_list = Pemasok::where('status', true)->orderBy('nama')->get();

            return view('pages.pembelian.index', compact('data_pembelian', 'pemasok_list'));
        } catch (\Exception $e) {
            Log::error('Error loading Pembelian index', ['error' => $e->getMessage()]);
            return view('pages.pembelian.index', [
                'data_pembelian' => collect()->paginate(),
                'pemasok_list' => collect()
            ])->with('error', 'Gagal memuat data pembelian');
        }
    }

    public function create()
    {
        $pemasok = Pemasok::orderBy('created_at', 'desc')->get();
        $bahan_baku = BahanBaku::orderBy('created_at', 'desc')->get();
        $satuanList = SubDetailParameter::orderBy('nama_sub_detail_parameter')->get(['id', 'nama_sub_detail_parameter'])->toArray();
        
        return view('pages.pembelian.create', compact('pemasok', 'bahan_baku', 'satuanList'));
    }

    public function store(StorePembelianRequest $request): RedirectResponse
    {
        try {
            $pembelian = $this->pembelianService->createPembelian($request->validated());
            return redirect()->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil ditambahkan.');
        } catch (InvalidPembelianDataException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Pembelian', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function show(string $kode_pembelian): View
    {
        try {
            $pembelian = $this->pembelianService->getPembelianByKode($kode_pembelian);
            $satuanList = SubDetailParameter::orderBy('nama_sub_detail_parameter')
                ->get(['id', 'nama_sub_detail_parameter'])
                ->toArray();
            return view('pages.pembelian.show', compact('pembelian', 'satuanList'));
        } catch (PembelianNotFoundException $e) {
            return redirect()->route('pembelian.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error showing Pembelian', [
                'kode_pembelian' => $kode_pembelian,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('pembelian.index')
                ->with('error', 'Gagal memuat detail pembelian');
        }
    }

    public function edit(string $kode_pembelian): View
    {
        try {
            $pembelian = $this->pembelianService->getPembelianByKode($kode_pembelian);
            $pemasok = Pemasok::orderBy('created_at', 'desc')->get();
            $bahan_baku = BahanBaku::orderBy('created_at', 'desc')->get();
            $satuanList = SubDetailParameter::orderBy('nama_sub_detail_parameter')
                ->get(['id', 'nama_sub_detail_parameter'])
                ->toArray();
            
            return view('pages.pembelian.edit', compact('pembelian', 'pemasok', 'bahan_baku', 'satuanList'));
        } catch (PembelianNotFoundException $e) {
            return redirect()->route('pembelian.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error loading Pembelian edit form', [
                'kode_pembelian' => $kode_pembelian,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('pembelian.index')
                ->with('error', 'Gagal memuat form edit pembelian');
        }
    }

    public function update(UpdatePembelianRequest $request, string $kode_pembelian): RedirectResponse
    {
        try {
            $this->pembelianService->updatePembelian($kode_pembelian, $request->validated());
            return redirect()->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil diupdate.');
        } catch (PembelianNotFoundException $e) {
            return redirect()->route('pembelian.index')
                ->with('error', $e->getMessage());
        } catch (InvalidPembelianDataException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Unexpected error updating Pembelian', [
                'kode_pembelian' => $kode_pembelian,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function destroy(string $kode_pembelian): RedirectResponse
    {
        try {
            $this->pembelianService->deletePembelian($kode_pembelian);
            return redirect()->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil dihapus.');
        } catch (PembelianNotFoundException $e) {
            return redirect()->route('pembelian.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Pembelian', [
                'kode_pembelian' => $kode_pembelian,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal menghapus pembelian');
        }
    }
}
