<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CariController extends Controller
{
    public function CariBahanBaku(Request $request)
    {
        // tampilakan semua data bahan BahanBaku
        // $bahanBaku = BahanBaku::all();
        // dd($bahanBaku);
        // return response()->json($bahanBaku);
        $searchTerm = $request->input('searchBahanBaku');

        $bahanBaku = BahanBaku::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where('kode_bahan', 'like', "%{$searchTerm}%")
                      ->orWhere('nama_bahan', 'like', "%{$searchTerm}%");
            })
            ->get(); 

        // return json_encode($bahanBaku);
        return response()->json($bahanBaku);
    }

}


