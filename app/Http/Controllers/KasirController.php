<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KasirService;
use App\Models\SPK;
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

    public function show($no)
    {
        // Data dummy detail invoice
        $invoice = [
            'no' => 'INV-2023-001',
            'status' => 'pending',
            'tanggal' => '15 Juli 2023',
            'jatuh_tempo' => '15 Agustus 2023',
            'spk_no' => 'SPK-2023-001',
            'customer' => [
                'nama' => 'PT Maju Bersama Indonesia',
                'email' => 'admin@majubersama.co.id',
                'telp' => '021-5552890',
                'catatan' => 'Pembayaran dapat dilakukan via transfer bank',
            ],
            'ringkasan' => [
                'subtotal' => 800000,
                'pajak' => 88000,
                'diskon' => 50000,
                'total' => 838000,
                'dibayar' => 0,
                'sisa' => 450000,
            ],
            'items' => [
                [
                    'deskripsi' => 'Cetak Spanduk Outdoor',
                    'jumlah' => 2,
                    'harga' => 150000,
                    'pajak' => '11%',
                    'diskon' => '0%',
                    'total' => 300000,
                ],
                [
                    'deskripsi' => 'Cetak Brosur A4 Full Color',
                    'jumlah' => 500,
                    'harga' => 1000,
                    'pajak' => '11%',
                    'diskon' => '10%',
                    'total' => 500000,
                ],
            ],
            'pembayaran' => [],
        ];
        return view('pages.kasir.detail', compact('invoice'));
    }

    public function print($no)
    {
        // Data dummy detail invoice (sama dengan show)
        $invoice = [
            'no' => 'INV-2023-001',
            'status' => 'pending',
            'tanggal' => '15 Juli 2023',
            'jatuh_tempo' => '15 Agustus 2023',
            'spk_no' => 'SPK-1',
            'customer' => [
                'nama' => 'PT Maju Bersama Indonesia',
                'email' => 'admin@majubersama.co.id',
                'telp' => '021-5552890',
                'catatan' => 'Pembayaran dapat dilakukan via transfer bank',
            ],
            'ringkasan' => [
                'subtotal' => 800000,
                'pajak' => 88000,
                'diskon' => 50000,
                'total' => 838000,
                'dibayar' => 0,
                'sisa' => 450000,
            ],
            'items' => [
                [
                    'deskripsi' => 'Cetak Spanduk Outdoor',
                    'jumlah' => 2,
                    'harga' => 150000,
                    'pajak' => '11%',
                    'diskon' => '0%',
                    'total' => 300000,
                ],
                [
                    'deskripsi' => 'Cetak Brosur A4 Full Color',
                    'jumlah' => 500,
                    'harga' => 1000,
                    'pajak' => '11%',
                    'diskon' => '10%',
                    'total' => 500000,
                ],
            ],
            'pembayaran' => [],
        ];

        // Load dompdf
        $pdf = \PDF::loadView('pages.kasir.cetak', compact('invoice'));
        
        // Render file PDF
        return $pdf->stream("invoice-{$no}.pdf");
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
            ->route('kasir.spk.payment', $spk)
            ->with('success', 'Pembayaran berhasil disimpan.');
    }
} 