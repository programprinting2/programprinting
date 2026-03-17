<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KasirService;
use App\Models\SPK;
use App\Http\Requests\StoreSpkPaymentRequest;
class KasirController extends Controller
{
    public function __construct(
        private KasirService $kasirService
    ) {}

    public function index(Request $request)
    {
        $spkList = $this->kasirService->getSpkBelumLunas($request);
        $statusPembayaranList = SPK::pembayaranStatusList();
        return view('pages.kasir.index', compact('spkList', 'statusPembayaranList'));
    }

    public function show(SPK $spk)
    {
        $spk->load(['pelanggan', 'items', 'pembayaran']);
        $invoice = $this->kasirService->buildInvoiceFromSpk($spk);

        return view('pages.kasir.detail', compact('invoice', 'spk'));
    }

    public function print(SPK $spk)
    {
        $spk->load(['pelanggan', 'items', 'pembayaran']);
        $invoice = $this->kasirService->buildInvoiceFromSpk($spk);

        $pdf = \PDF::loadView('pages.kasir.cetak', compact('invoice'));

        return $pdf->stream("invoice-{$spk->nomor_spk}.pdf");
    }

    public function payment(SPK $spk)
    {
        $spk->load(['pelanggan', 'pembayaran']);
        return view('pages.kasir.payment', compact('spk'));
    }

    public function storePayment(StoreSpkPaymentRequest $request, SPK $spk)
    {
        $this->kasirService->storePayment($spk, $request->validated());

        return redirect()
           ->route('kasir.index')
           ->with('success', 'Pembayaran berhasil disimpan.');
    }
} 