<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Pembelian;

class PembelianController extends Controller
{
    public function index()
    {
        // $data_pembelian = Pembelian::paginate(10);
        return view('pages.pembelian.index');
    }

    public function create()
    {
        return view('pages.pembelian.create');
    }

    public function store(Request $request)
    {
        // Validasi data input
        $validated = $request->validate([
            'nomor_faktur' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'supplier_id' => 'required|integer',
        ]);

        return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }
}
