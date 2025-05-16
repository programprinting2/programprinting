<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SPKController extends Controller
{
    public function index()
    {
        // Data dummy SPK untuk slicing UI
        $spk = [
            [
                'nomor' => 'SPK0525-291-014',
                'tanggal' => '2025-05-14',
                'pelanggan' => 'PT. Sumber Makmur',
                'status' => 'Draft',
                'prioritas' => 'Normal',
                'item' => [
                    [
                        'nama' => 'Cetak Brosur A4',
                        'jumlah' => 1000,
                        'satuan' => 'lembar',
                        'catatan' => 'Full color, dua sisi',
                    ],
                    [
                        'nama' => 'Desain Grafis',
                        'jumlah' => 1,
                        'satuan' => 'desain',
                        'catatan' => 'Revisi maksimal 3x',
                    ],
                ],
            ],
        ];
        return view('pages.spk.index', compact('spk'));
    }

    public function create()
    {
        return view('pages.spk.create');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan data SPK
        // ...
        return redirect()->route('spk.index')->with('success', 'SPK berhasil ditambahkan.');
    }
} 