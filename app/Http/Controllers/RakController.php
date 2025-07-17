<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class RakController extends Controller
{
    // Tampilkan index master rak
    public function index(Request $request)
    {
        $query = Rak::with('gudang');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_rak', 'like', "%$search%")
                  ->orWhere('nama_rak', 'like', "%$search%")
                  ->orWhereHas('gudang', function($q2) use ($search) {
                      $q2->where('nama_gudang', 'like', "%$search%")
                         ->orWhere('kode_gudang', 'like', "%$search%") ;
                  });
            });
        }
        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Filter gudang
        if ($request->filled('gudang_id')) {
            $query->where('gudang_id', $request->gudang_id);
        }
        $rak = $query->orderBy('created_at', 'desc')->paginate(10);
        $gudang = Gudang::orderBy('nama_gudang')->get();
        $totalGudang = Gudang::count();
        return view('backend.master-rak.index', compact('rak', 'gudang', 'totalGudang'));
    }

    // Store rak baru
    public function store(Request $request)
    {
        $data = $request->all();
        // Generate kode rak otomatis tanpa prefix gudang
        $data['kode_rak'] = IdGenerator::generate([
            'table' => 'rak',
            'field' => 'kode_rak',
            'length' => 7, // contoh: R-00001
            'prefix' => 'R-',
        ]);
        $validator = Validator::make($data, [
            'gudang_id' => 'required|exists:gudang,id',
            'kode_rak' => 'required|string|max:20|unique:rak,kode_rak',
            'nama_rak' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:0',
            'jumlah_level' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'kedalaman' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $rak = Rak::create($data);
        return response()->json(['success' => true, 'message' => 'Rak berhasil ditambahkan', 'data' => $rak]);
    }

    // Helper untuk prefix kode rak
    private function getRakPrefix($gudang_id)
    {
        $gudang = Gudang::find($gudang_id);
        return $gudang ? $gudang->kode_gudang . '-' : '';
    }

    // Update rak
    public function update(Request $request, $id)
    {
        $rak = Rak::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'gudang_id' => 'required|exists:gudang,id',
            'kode_rak' => 'required|string|max:20|unique:rak,kode_rak,' . $rak->id,
            'nama_rak' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:0',
            'jumlah_level' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'kedalaman' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $rak->update($request->all());
        return response()->json(['success' => true, 'message' => 'Rak berhasil diupdate', 'data' => $rak]);
    }

    // Hapus rak
    public function destroy($id)
    {
        $rak = Rak::findOrFail($id);
        $rak->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Rak berhasil dihapus']);
        }
        return redirect()->route('backend.master-rak.index')->with('success', 'Rak berhasil dihapus.');
    }

    // Show detail rak (opsional, untuk modal view)
    public function show($id)
    {
        $rak = Rak::with('gudang')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $rak]);
    }
} 