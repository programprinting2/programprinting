<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();

        // Pencarian berdasarkan nama, posisi, atau departemen
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(posisi) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(departemen) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(id_karyawan) LIKE ?', ['%' . $search . '%']);
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination dengan 10 item per halaman
        $karyawan = $query->orderBy('created_at', 'desc')->paginate(10);

        // Tambahkan parameter pencarian ke pagination
        if ($request->has('search')) {
            $karyawan->appends(['search' => $request->search]);
        }
        if ($request->has('status')) {
            $karyawan->appends(['status' => $request->status]);
        }

        return view('backend.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('backend.karyawan.form', [
            'karyawan' => new Karyawan(),
            'title' => 'Tambah Karyawan',
            'method' => 'POST',
            'action' => route('backend.karyawan.store')
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|in:Belum Menikah,Menikah,Cerai',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gaji_pokok' => 'nullable|integer|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'alamat' => 'nullable|array',
            'rekening' => 'nullable|array',
            'alamat_utama' => 'nullable|integer',
            'rekening_utama' => 'nullable|integer',
            'npwp' => 'nullable|string|max:255',
            'status_pajak' => 'nullable|string|max:255',
            'tarif_pajak' => 'nullable|integer|min:0|max:100',
            'estimasi_hari_kerja' => 'nullable|integer|min:0|max:31',
            'jam_kerja_per_hari' => 'nullable|integer|min:0|max:24',
            'komponen_gaji' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id_karyawan = IdGenerator::generate([
            'table' => 'karyawan',
            'field' => 'id_karyawan',
            'length' => 9,
            'prefix' => 'EMP-'
        ]);

        $data = $request->all();
        $data['id_karyawan'] = $id_karyawan;
        
        // Memastikan data JSON valid
        $data['alamat'] = $request->input('alamat') ? json_decode(json_encode($request->input('alamat')), true) : [];
        $data['rekening'] = $request->input('rekening') ? json_decode(json_encode($request->input('rekening')), true) : [];
        $data['komponen_gaji'] = $request->input('komponen_gaji') ? json_decode(json_encode($request->input('komponen_gaji')), true) : [];
        
        $data['alamat_utama'] = $request->input('alamat_utama');
        $data['rekening_utama'] = $request->input('rekening_utama');

        try {
            Karyawan::create($data);
            return response()->json(['success' => true, 'message' => 'Data karyawan berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan data karyawan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('backend.karyawan.form', [
            'karyawan' => $karyawan,
            'title' => 'Edit Karyawan',
            'method' => 'PUT',
            'action' => route('backend.karyawan.update', $karyawan->id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|in:Belum Menikah,Menikah,Cerai',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gaji_pokok' => 'nullable|integer|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'alamat' => 'nullable|array',
            'rekening' => 'nullable|array',
            'alamat_utama' => 'nullable|integer',
            'rekening_utama' => 'nullable|integer',
            'npwp' => 'nullable|string|max:255',
            'status_pajak' => 'nullable|string|max:255',
            'tarif_pajak' => 'nullable|integer|min:0|max:100',
            'estimasi_hari_kerja' => 'nullable|integer|min:0|max:31',
            'jam_kerja_per_hari' => 'nullable|integer|min:0|max:24',
            'komponen_gaji' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        // Memastikan data JSON valid
        $data['alamat'] = $request->input('alamat') ? json_decode(json_encode($request->input('alamat')), true) : [];
        $data['rekening'] = $request->input('rekening') ? json_decode(json_encode($request->input('rekening')), true) : [];
        $data['komponen_gaji'] = $request->input('komponen_gaji') ? json_decode(json_encode($request->input('komponen_gaji')), true) : [];
        
        $data['alamat_utama'] = $request->input('alamat_utama');
        $data['rekening_utama'] = $request->input('rekening_utama');

        try {
            $karyawan->update($data);
            return response()->json(['success' => true, 'message' => 'Data karyawan berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data karyawan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()
            ->route('backend.karyawan.index')
            ->with('success', 'Data karyawan berhasil dihapus');
    }

    /**
     * Get karyawan data for API
     */
    public function show($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return response()->json($karyawan);
    }
}
