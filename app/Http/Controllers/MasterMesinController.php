<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterMesin;
use Illuminate\Support\Facades\Storage;

class MasterMesinController extends Controller
{
    public function index()
    {
        $data_mesin = MasterMesin::all();
        return view("pages.backend.master-mesin", ['data_mesin' => $data_mesin]);
    }

    private function generateKodeMesin()
    {
        $lastMesin = MasterMesin::orderBy('id', 'desc')->first();
        $lastKode = $lastMesin ? intval(substr($lastMesin->kode_mesin, 1)) : 0;
        $newKode = $lastKode + 1;
        return 'M' . str_pad($newKode, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mesin' => 'required|string|max:255',
            'model_mesin' => 'required|string|max:255',
            'jenis_mesin' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'non_produksi' => 'required|boolean',
            'tanggal_beli' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nomor_seri' => 'nullable|string|max:255',
            'pabrikan' => 'nullable|string|max:255',
            'lokasi_pemeliharaan' => 'nullable|string|max:255',
            'tanggal_pemeliharaan_terakhir' => 'nullable|date',
            'tanggal_pemeliharaan_selanjutnya' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        // Ensure kode_mesin is generated and added to the validated data
        $validated['kode_mesin'] = $this->generateKodeMesin();
        
        // Handle checkbox with name="aktif" - last value will be used if multiple values present
        // This works because when checkbox is checked, input name="aktif" with value="1" will override the hidden input
        $validated['aktif'] = $request->input('aktif', 0);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('images', $filename, 'public');
            $validated['gambar'] = $filename;
        }

        MasterMesin::create($validated);

        return redirect()->route('backend.master-mesin')->with('success', 'Data mesin berhasil ditambahkan.');
    }

    public function delete($id)
    {
        $data_mesin = MasterMesin::find($id);
        // dd($data_mesin);
        if ($data_mesin) {
            if ($data_mesin->gambar && Storage::disk('public')->exists('images/' . $data_mesin->gambar)) {
                Storage::disk('public')->delete('images/' . $data_mesin->gambar);
            }
            $data_mesin->delete();
            return redirect()->route('backend.master-mesin')->with('success', 'Data mesin berhasil dihapus.');
        } else {
            return redirect()->route('backend.master-mesin')->with('error', 'Data mesin tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $mesin = MasterMesin::findOrFail($id);

        $validatedData = $request->validate([
            'kode_mesin' => 'required|string|max:255',
            'nama_mesin' => 'required|string|max:255',
            'model_mesin' => 'nullable|string|max:255',
            'jenis_mesin' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'non_produksi' => 'required|boolean',
            'tanggal_beli' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nomor_seri' => 'nullable|string|max:255',
            'pabrikan' => 'nullable|string|max:255',
            'lokasi_pemeliharaan' => 'nullable|string|max:255',
            'tanggal_pemeliharaan_terakhir' => 'nullable|date',
            'tanggal_pemeliharaan_selanjutnya' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $mesin->fill($validatedData);

        // Correctly handle the "aktif" field
        $mesin->aktif = $request->input('aktif', 0); // Default to 0 if not provided, last value will be used if multiple values are present

        if ($request->hasFile('gambar')) {
            if ($mesin->gambar && Storage::disk('public')->exists('images/' . $mesin->gambar)) {
                Storage::disk('public')->delete('images/' . $mesin->gambar);
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('images', $filename, 'public');
            $mesin->gambar = $filename;
        } elseif ($request->clear_gambar) {
            if ($mesin->gambar && Storage::disk('public')->exists('images/' . $mesin->gambar)) {
                Storage::disk('public')->delete('images/' . $mesin->gambar);
            }
            $mesin->gambar = null;
        }

        $mesin->save();

        return redirect()->route('backend.master-mesin')->with('success', 'Data mesin berhasil diperbarui.');
    }

    public function filter(Request $request)
    {
        $search = $request->query('search', '');
        $data_mesin = MasterMesin::where('nama_mesin', 'like', "%$search%")
            ->orWhere('jenis_mesin', 'like', "%$search%")
            ->orWhere('keterangan', 'like', "%$search%")
            ->orWhere('tanggal_beli', 'like', "%$search%")
            ->get();

        return response()->json($data_mesin);
    }
}
