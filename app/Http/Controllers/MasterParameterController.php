<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterParameter;
use Illuminate\Support\Facades\Storage;
use App\Models\DetailParameter;
use App\Models\SubDetailParameter;
use Illuminate\Support\Facades\DB;

class MasterParameterController extends Controller
{
    public function index()
    {
        $data_parameter = MasterParameter::all();
        return view("pages.backend.master-parameter", ['data_parameter' => $data_parameter]);
    }

    // CRUD Kategori Parameter
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_parameter' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'icon' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        $validated['aktif'] = $request->has('aktif') ? 1 : 0;
        $param = MasterParameter::create($validated);
        if ($request->ajax()) return response()->json($param);
        return redirect()->route('backend.master-parameter')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $param = MasterParameter::findOrFail($id);
        $validated = $request->validate([
            'nama_parameter' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'icon' => 'nullable|string',
            'aktif' => 'boolean',
        ]);
        $validated['aktif'] = $request->has('aktif') ? 1 : 0;
        $param->update($validated);
        if ($request->ajax()) return response()->json($param);
        return redirect()->route('backend.master-parameter')->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy($id)
    {
        $param = MasterParameter::findOrFail($id);
        $param->delete();
        if (request()->ajax()) return response()->json(['success'=>true]);
        return redirect()->route('backend.master-parameter')->with('success', 'Kategori berhasil dihapus.');
    }

    // CRUD Detail Parameter
    public function detail($id)
    {
        $details = DetailParameter::where('master_parameter_id', $id)->get();
        return response()->json($details);
    }

    public function storeDetail(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'nama_detail_parameter' => 'required|string|max:255',
                'isi_parameter' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'aktif' => 'required|in:0,1',
                'sub_details' => 'nullable|string', // JSON string dari frontend
            ]);
            
            $validated['master_parameter_id'] = $id;
            $validated['aktif'] = (int)$request->input('aktif', 1);
            
            $detail = DetailParameter::create($validated);
            
            // Proses sub detail parameter
            if ($request->has('sub_details') && $request->sub_details) {
                $subDetails = json_decode($request->sub_details, true);
                if (is_array($subDetails)) {
                    foreach ($subDetails as $subDetail) {
                        if (!empty($subDetail['nama_sub_detail_parameter'])) {
                            SubDetailParameter::create([
                                'detail_parameter_id' => $detail->id,
                                'nama_sub_detail_parameter' => $subDetail['nama_sub_detail_parameter'],
                                'keterangan' => $subDetail['keterangan'] ?? null,
                                'aktif' => $subDetail['aktif'] ?? true,
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            return response()->json($detail);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDetail(Request $request, $id, $detailId)
    {
        try {
            DB::beginTransaction();
            
            $detail = DetailParameter::findOrFail($detailId);
            $validated = $request->validate([
                'nama_detail_parameter' => 'required|string|max:255',
                'isi_parameter' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'aktif' => 'required|in:0,1',
                'sub_details' => 'nullable|string', // JSON string dari frontend
            ]);
            
            $validated['aktif'] = (int)$request->input('aktif', 1);
            $detail->update($validated);
            
            // Proses sub detail parameter
            if ($request->has('sub_details') && $request->sub_details) {
                $subDetails = json_decode($request->sub_details, true);
                if (is_array($subDetails)) {
                    $existingSubDetails = SubDetailParameter::where('detail_parameter_id', $detailId)->get();
                    $existingIds = $existingSubDetails->pluck('id')->toArray();
                    $newIds = collect($subDetails)->pluck('id')->filter()->toArray();

                    // Hapus sub detail yang tidak ada di data baru
                    $toDelete = array_diff($existingIds, $newIds);
                    if (!empty($toDelete)) {
                        foreach ($toDelete as $subId) {
                            // Set null pada bahan_baku yang referensi ke sub detail ini
                            DB::table('bahan_baku')->where('sub_kategori_id', $subId)->update(['sub_kategori_id' => null]);
                            // Hapus sub detail
                            SubDetailParameter::where('id', $subId)->delete();
                        }
                    }

                    // Update/insert sub detail
                    foreach ($subDetails as $subDetail) {
                        if (!empty($subDetail['id']) && in_array($subDetail['id'], $existingIds)) {
                            // Update
                            SubDetailParameter::where('id', $subDetail['id'])->update([
                                'nama_sub_detail_parameter' => $subDetail['nama_sub_detail_parameter'],
                                'keterangan' => $subDetail['keterangan'] ?? null,
                                'aktif' => $subDetail['aktif'] ?? true,
                            ]);
                        } elseif (empty($subDetail['id']) && !empty($subDetail['nama_sub_detail_parameter'])) {
                            // Insert baru
                            SubDetailParameter::create([
                                'detail_parameter_id' => $detailId,
                                'nama_sub_detail_parameter' => $subDetail['nama_sub_detail_parameter'],
                                'keterangan' => $subDetail['keterangan'] ?? null,
                                'aktif' => $subDetail['aktif'] ?? true,
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            return response()->json($detail);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyDetail($id, $detailId)
    {
        $detail = DetailParameter::findOrFail($detailId);
        $detail->delete();
        return response()->json(['success'=>true]);
    }

    // public function filter(Request $request)
    // {
    //     $search = $request->query('search', '');
    //     $data_kontak = MasterKontak::where('nama', 'like', "%$search%")
    //         ->orWhere('tipe', 'like', "%$search%")
    //         ->orWhere('HP', 'like', "%$search%")
    //         ->orWhere('alamat', 'like', "%$search%")
    //         ->orWhere('catatan', 'like', "%$search%")
    //         ->get();

    //     return response()->json($data_kontak);
    // }
}
