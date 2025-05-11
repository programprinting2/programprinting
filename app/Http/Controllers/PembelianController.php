<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index()
    {
        return view('pages.pembelian.index');
    }
    
    public function store(Request $request)
    {
        // Validasi data input
        $validated = $request->validate([
            'nomor_faktur' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'supplier_id' => 'required|integer',
        ]);
        
        // Ini hanya placeholder, akan diimplementasikan setelah model dibuat
        // Pembelian::create($validated);
        
        return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }
}
