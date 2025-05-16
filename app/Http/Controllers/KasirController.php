<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $invoices = [
            [
                'no' => 'INV-2023-001',
                'customer' => 'PT Maju Bersama Indonesia',
                'status' => 'pending',
                'total' => 450000,
                'sisa' => 450000,
            ],
            [
                'no' => 'INV-2023-002',
                'customer' => 'PT Global Media Indonesia',
                'status' => 'partial',
                'total' => 850000,
                'sisa' => 350000,
                'sudah_dibayar' => 500000,
            ],
            [
                'no' => 'INV-2023-003',
                'customer' => 'Toko Bahagia',
                'status' => 'paid',
                'total' => 275000,
                'sisa' => 0,
            ],
        ];
        return view('pages.kasir.index', compact('invoices'));
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

    public function payment($no)
    {
        // Data dummy invoice untuk halaman pembayaran
        $invoice = [
            'no' => 'INV-2023-001',
            'status' => 'pending',
            'tanggal' => '15 Juli 2023',
            'jatuh_tempo' => '15 Agustus 2023',
            'customer' => [
                'nama' => 'PT Maju Bersama Indonesia',
                'email' => 'admin@majubersama.co.id',
                'telp' => '021-5552890',
            ],
            'total' => 450000,
            'dibayar' => 0,
            'sisa' => 450000,
        ];
        
        return view('pages.kasir.payment', compact('invoice'));
    }
    
    public function storePayment(Request $request, $no)
    {
        // Validasi input pembayaran
        $validated = $request->validate([
            'jumlah' => 'required|numeric',
            'metode' => 'required|string',
            'tanggal' => 'required|date',
            'referensi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);
        
        // Simulasi penyimpanan pembayaran
        
        // Redirect ke halaman detail invoice dengan pesan sukses
        return redirect()->route('kasir.invoice.show', $no)
            ->with('success', 'Pembayaran berhasil disimpan');
    }
} 