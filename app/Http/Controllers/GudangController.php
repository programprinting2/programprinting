<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Rak;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Gudang::query()->withCount('rak');

        // Pencarian berdasarkan kode, nama gudang, manager, atau alamat
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_gudang) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nama_gudang) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(manager) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(alamat) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kota) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(provinsi) LIKE ?', ['%' . $search . '%']);
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination dengan 10 item per halaman
        $gudang = $query->orderBy('created_at', 'desc')->paginate(10);

        // Tambahkan parameter pencarian ke pagination
        if ($request->has('search')) {
            $gudang->appends(['search' => $request->search]);
        }
        if ($request->has('status')) {
            $gudang->appends(['status' => $request->status]);
        }

        $totalRak = Rak::count();
        return view('backend.master-gudang.index', compact('gudang', 'totalRak'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:100',
            'manager' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'deskripsi' => 'nullable|string',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:50',
            'provinsi' => 'required|string|max:50',
            'kode_pos' => 'required|string|max:10',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);
        
        try {
            $validated['kode_gudang'] = IdGenerator::generate([
                'table' => 'gudang',
                'field' => 'kode_gudang',
                'length' => 7,
                'prefix' => 'WH-'
            ]);
            
            Gudang::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gudang  $gudang
     * @return \Illuminate\Http\Response
     */
    public function show(Gudang $gudang)
    {
        return view('gudang.show', compact('gudang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gudang  $gudang
     * @return \Illuminate\Http\Response
     */
    public function edit(Gudang $gudang)
    {
        return response()->json([
            'success' => true,
            'data' => $gudang
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gudang  $gudang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $gudang = Gudang::findOrFail($id);
        
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:100',
            'manager' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'deskripsi' => 'nullable|string',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:50',
            'provinsi' => 'required|string|max:50',
            'kode_pos' => 'required|string|max:10',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);
        
        try {
            $gudang->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $gudang = Gudang::findOrFail($id);
            $gudang->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data gudang berhasil dihapus'
                ]);
            }
            
            return redirect()->route('backend.master-gudang.index')->with('success', 'Gudang berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data gudang: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('backend.master-gudang.index')->with('error', 'Gagal menghapus gudang.');
        }
    }
}
