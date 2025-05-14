<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        // Data dummy produk untuk slicing UI
        $produk = [
            [
                'kode' => 'BNR01',
                'nama' => 'Banners (4x6 ft)',
                'deskripsi' => 'Durable vinyl banners with metal grommets',
                'kategori' => 'Banners',
                'harga' => 90,
                'stok' => 100,
            ],
            [
                'kode' => 'BRC02',
                'nama' => 'Brochures (Tri-Fold)',
                'deskripsi' => 'Professional tri-fold brochures on premium paper',
                'kategori' => 'Brochures',
                'harga' => 200,
                'stok' => 50,
            ],
            [
                'kode' => 'BC03',
                'nama' => 'Business Cards',
                'deskripsi' => 'Premium 16pt business cards with matte finish',
                'kategori' => 'Business Cards',
                'harga' => 46,
                'stok' => 200,
            ],
        ];
        return view('pages.produk.index', compact('produk'));
    }
} 