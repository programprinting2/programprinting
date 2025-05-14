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
} 