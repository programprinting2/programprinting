<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        // Pencarian berdasarkan nama, kode, atau kontak
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_pelanggan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(no_telp) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(handphone) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination dengan 10 item per halaman
        $pelanggan = $query->orderBy('created_at', 'desc')->paginate(10);

        // Tambahkan parameter pencarian ke pagination
        if ($request->has('search')) {
            $pelanggan->appends(['search' => $request->search]);
        }
        if ($request->has('status')) {
            $pelanggan->appends(['status' => $request->status]);
        }

        return view('backend.pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('backend.pelanggan.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:1,0',
            'no_telp' => 'nullable|string|max:20',
            'handphone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'kategori_harga' => 'nullable|string|max:50',
            'syarat_pembayaran' => 'nullable|string|max:50',
            'default_diskon' => 'nullable|numeric|min:0|max:100',
            'npwp' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'batas_umur_faktur_check' => 'nullable|boolean',
            'batas_umur_faktur' => 'nullable|integer|min:0',
            'batas_total_piutang_check' => 'nullable|boolean',
            'batas_total_piutang_nilai' => 'nullable|numeric|min:0',
            'alamat' => 'nullable|array',
            'alamat.*.label' => 'nullable|string|max:100',
            'alamat.*.alamat' => 'nullable|string',
            'alamat.*.kota' => 'nullable|string|max:100',
            'alamat.*.provinsi' => 'nullable|string|max:100',
            'alamat.*.kode_pos' => 'nullable|string|max:20',
            'alamat_utama' => 'nullable|integer',
            'kontak' => 'nullable|array',
            'kontak.*.nama' => 'nullable|string|max:255',
            'kontak.*.posisi' => 'nullable|string|max:100',
            'kontak.*.email' => 'nullable|email|max:255',
            'kontak.*.handphone' => 'nullable|string|max:20',
            'piutang_awal' => 'nullable|array',
            'piutang_awal.*.tanggal' => 'nullable|date',
            'piutang_awal.*.jumlah' => 'nullable|numeric|min:0',
            'piutang_awal.*.mata_uang' => 'nullable|string|max:10',
            'piutang_awal.*.syarat_pembayaran' => 'nullable|string|max:50',
            'piutang_awal.*.nomor' => 'nullable|string|max:50',
            'piutang_awal.*.keterangan' => 'nullable|string|max:255',
            'data_lain' => 'nullable|array',
            'data_lain.batas_umur_faktur' => 'nullable|integer|min:0',
            'data_lain.batas_total_piutang_nilai' => 'nullable|numeric|min:0',
        ]);

        // Generate kode pelanggan
        $validatedData['kode_pelanggan'] = IdGenerator::generate([
            'table' => 'pelanggan',
            'field' => 'kode_pelanggan',
            'length' => 9,
            'prefix' => 'CUST-'
        ]);

        // Konversi status dan boolean fields
        $validatedData['status'] = $validatedData['status'] === '1';
        $validatedData['wajib_pajak'] = $request->boolean('wajib_pajak');

        // Handle data_lain
        if (isset($validatedData['data_lain'])) {
            $validatedData['data_lain']['batas_umur_faktur_check'] = $request->boolean('data_lain.batas_umur_faktur_check');
            $validatedData['data_lain']['batas_total_piutang_check'] = $request->boolean('data_lain.batas_total_piutang_check');

            // Map batas_total_piutang_nilai
            if ($validatedData['data_lain']['batas_total_piutang_check'] && isset($validatedData['data_lain']['batas_total_piutang_nilai'])) {
                $validatedData['data_lain']['batas_total_piutang'] = $validatedData['data_lain']['batas_total_piutang_nilai'];
            }
            unset($validatedData['data_lain']['batas_total_piutang_nilai']);
        }

        try {
            Pelanggan::create($validatedData);
            return response()->json(['success' => true, 'message' => 'Data pelanggan berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('backend.pelanggan.form', [
            'pelanggan' => $pelanggan,
            'title' => 'Edit Pelanggan',
            'method' => 'PUT',
            'action' => route('backend.pelanggan.update', $pelanggan->id)
        ]);
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'status' => 'required|in:1,0',
                'no_telp' => 'nullable|string|max:20',
                'handphone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'alamat' => 'nullable|array',
                'alamat.*.label' => 'nullable|string|max:100',
                'alamat.*.alamat' => 'nullable|string',
                'alamat.*.kota' => 'nullable|string|max:100',
                'alamat.*.provinsi' => 'nullable|string|max:100',
                'alamat.*.kode_pos' => 'nullable|string|max:20',
                'alamat_utama' => 'nullable|integer',
                'kategori_harga' => 'nullable|string|max:50',
                'syarat_pembayaran' => 'nullable|string|max:50',
                'default_diskon' => 'nullable|numeric|min:0|max:100',
                'npwp' => 'nullable|string|max:50',
                'nik' => 'nullable|string|max:50',
                'wajib_pajak' => 'required|in:1,0',
                'kontak' => 'nullable|array',
                'kontak.*.nama' => 'nullable|string|max:255',
                'kontak.*.posisi' => 'nullable|string|max:100',
                'kontak.*.email' => 'nullable|email|max:255',
                'kontak.*.handphone' => 'nullable|string|max:20',
                'piutang_awal' => 'nullable|array',
                'piutang_awal.*.tanggal' => 'nullable|date',
                'piutang_awal.*.jumlah' => 'nullable|numeric|min:0',
                'piutang_awal.*.mata_uang' => 'nullable|string|max:10',
                'piutang_awal.*.syarat_pembayaran' => 'nullable|string|max:50',
                'piutang_awal.*.nomor' => 'nullable|string|max:50',
                'piutang_awal.*.keterangan' => 'nullable|string|max:255',
                'data_lain' => 'nullable|array',
                'data_lain.batas_umur_faktur_check' => 'required|in:1,0',
                'data_lain.batas_umur_faktur' => 'nullable|integer|min:0',
                'data_lain.batas_total_piutang_check' => 'required|in:1,0',
                'data_lain.batas_total_piutang_nilai' => 'nullable|numeric|min:0'
            ]);

            // Konversi boolean fields
            $validated['status'] = $validated['status'] === '1';
            $validated['wajib_pajak'] = $validated['wajib_pajak'] === '1';
            
            if (isset($validated['data_lain'])) {
                $validated['data_lain']['batas_umur_faktur_check'] = $validated['data_lain']['batas_umur_faktur_check'] === '1';
                $validated['data_lain']['batas_total_piutang_check'] = $validated['data_lain']['batas_total_piutang_check'] === '1';
            }

            $pelanggan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('backend.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function show($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pelanggan.'
            ], 500);
        }
    }
}