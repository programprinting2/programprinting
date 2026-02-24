<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Services\SpkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class PekerjaanController extends Controller
{
    public function __construct(
        private SpkService $spkService
    ) {}

    public function index(Request $request): View
    {
        try {
            $filters = $request->only([
                'search',
                'customer_id',
                'status',
            ]);

            // Pekerjaan = semua SPK, jadi cukup pakai getPaginatedSpk biasa
            $spk = $this->spkService->getPaginatedSpk(10, $filters);
            $customers = Pelanggan::where('status', true)->get();

            return view('pages.pekerjaan.index', compact('spk', 'customers'));
        } catch (\Exception $e) {
            Log::error('Error loading Pekerjaan index', ['error' => $e->getMessage()]);

            return view('pages.pekerjaan.index', [
                'spk' => new LengthAwarePaginator([], 0, 10),
                'customers' => collect(),
            ])->with('error', 'Gagal memuat data pekerjaan');
        }
    }
}