<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubDetailParameter;
use App\Models\DetailParameter;

class SubDetailParameterController extends Controller
{
    /**
     * Display a listing of sub detail parameters for a specific detail parameter.
     */
    public function index($masterParameterId, $detailId)
    {
        try {
            $subDetails = SubDetailParameter::where('detail_parameter_id', $detailId)
                ->where('aktif', true)
                ->get();
            
            return response()->json($subDetails);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data'], 500);
        }
    }

    /**
     * Show the form for creating a new sub detail parameter.
     */
    public function create($masterParameterId, $detailId)
    {
        // Not used in this implementation
        return response()->json(['message' => 'Method not used']);
    }

    /**
     * Store a newly created sub detail parameter.
     */
    public function store(Request $request, $masterParameterId, $detailId)
    {
        try {
            $request->validate([
                'nama_sub_detail_parameter' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'aktif' => 'boolean'
            ]);

            // Verify that detail parameter exists
            $detailParameter = DetailParameter::find($detailId);
            if (!$detailParameter) {
                return response()->json(['error' => 'Detail parameter tidak ditemukan'], 404);
            }

            $subDetail = SubDetailParameter::create([
                'detail_parameter_id' => $detailId,
                'nama_sub_detail_parameter' => $request->nama_sub_detail_parameter,
                'keterangan' => $request->keterangan,
                'aktif' => $request->aktif ?? true
            ]);

            return response()->json($subDetail, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    /**
     * Display the specified sub detail parameter.
     */
    public function show($masterParameterId, $detailId, $subDetailId)
    {
        try {
            $subDetail = SubDetailParameter::where('detail_parameter_id', $detailId)
                ->where('id', $subDetailId)
                ->first();

            if (!$subDetail) {
                return response()->json(['error' => 'Sub detail parameter tidak ditemukan'], 404);
            }

            return response()->json($subDetail);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data'], 500);
        }
    }

    /**
     * Show the form for editing the specified sub detail parameter.
     */
    public function edit($masterParameterId, $detailId, $subDetailId)
    {
        // Not used in this implementation
        return response()->json(['message' => 'Method not used']);
    }

    /**
     * Update the specified sub detail parameter.
     */
    public function update(Request $request, $masterParameterId, $detailId, $subDetailId)
    {
        try {
            $request->validate([
                'nama_sub_detail_parameter' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'aktif' => 'boolean'
            ]);

            $subDetail = SubDetailParameter::where('detail_parameter_id', $detailId)
                ->where('id', $subDetailId)
                ->first();

            if (!$subDetail) {
                return response()->json(['error' => 'Sub detail parameter tidak ditemukan'], 404);
            }

            $subDetail->update([
                'nama_sub_detail_parameter' => $request->nama_sub_detail_parameter,
                'keterangan' => $request->keterangan,
                'aktif' => $request->aktif ?? true
            ]);

            return response()->json($subDetail);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengupdate data'], 500);
        }
    }

    /**
     * Remove the specified sub detail parameter.
     */
    public function destroy($masterParameterId, $detailId, $subDetailId)
    {
        try {
            $subDetail = SubDetailParameter::where('detail_parameter_id', $detailId)
                ->where('id', $subDetailId)
                ->first();

            if (!$subDetail) {
                return response()->json(['error' => 'Sub detail parameter tidak ditemukan'], 404);
            }

            $subDetail->delete();

            return response()->json(['message' => 'Sub detail parameter berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
