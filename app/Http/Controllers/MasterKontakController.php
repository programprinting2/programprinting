<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterKontak;
use Illuminate\Support\Facades\Storage;

class MasterKontakController extends Controller
{
    public function index()
    {
        $data_kontak = MasterKontak::all();
        return view("pages.backend.master-kontak", ['data_kontak' => $data_kontak]);
    }

    public function store(Request $request)
    {
                
        $validated = $request->validate([
            'tipe' => 'required|string|in:staff,customer,supplier',
            'nama' => 'required|string|max:100',
            'HP' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        MasterKontak::create($validated);

        return redirect()->route('backend.master-kontak')->with('success', 'Data kontak berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data_kontak = MasterKontak::findOrFail($id);
        

        $validated = $request->validate([
            'tipe' => 'required|string|in:staff,customer,supplier',
            'nama' => 'required|string|max:100',
            'HP' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data_kontak->update($validated);

        return redirect()->route('backend.master-kontak')->with('success', 'Data kontak berhasil diperbarui.');
    }

    public function delete($id)
    {
        $data_kontak = MasterKontak::find($id);

        if ($data_kontak) {
            $data_kontak->delete();
            return redirect()->route('backend.master-kontak')->with('success', 'Data kontak berhasil dihapus.');
        } else {
            return redirect()->route('backend.master-kontak')->with('error', 'Data kontak tidak ditemukan.');
        }
    }

    public function filter(Request $request)
    {
        $search = $request->query('search', '');
        $data_kontak = MasterKontak::where('nama', 'like', "%$search%")
            ->orWhere('tipe', 'like', "%$search%")
            ->orWhere('HP', 'like', "%$search%")
            ->orWhere('alamat', 'like', "%$search%")
            ->orWhere('catatan', 'like', "%$search%")
            ->get();

        return response()->json($data_kontak);
    }
}
