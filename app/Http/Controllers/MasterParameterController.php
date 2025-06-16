<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterParameter;
use Illuminate\Support\Facades\Storage;
use App\Models\DetailParameter;

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
            $validated = $request->validate([
                'nama_detail_parameter' => 'required|string|max:255',
                'isi_parameter' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'aktif' => 'required|in:0,1',
            ]);
            
            $validated['master_parameter_id'] = $id;
            $validated['aktif'] = (int)$request->input('aktif', 1);
            
            $detail = DetailParameter::create($validated);
            return response()->json($detail);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDetail(Request $request, $id, $detailId)
    {
        try {
            $detail = DetailParameter::findOrFail($detailId);
            $validated = $request->validate([
                'nama_detail_parameter' => 'required|string|max:255',
                'isi_parameter' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'aktif' => 'required|in:0,1',
            ]);
            
            $validated['aktif'] = (int)$request->input('aktif', 1);
            $detail->update($validated);
            return response()->json($detail);
        } catch (\Exception $e) {
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
