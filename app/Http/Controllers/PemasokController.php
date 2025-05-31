<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasok;
use App\Models\MasterParameter;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasok = Pemasok::latest()->paginate(10);
        
        // Data untuk dropdown akun
        // $akun_utang = [
        //     (object)['id' => 'Utang Usaha', 'kode' => '2101', 'nama' => 'Utang Usaha'],
        //     (object)['id' => 'Utang Lain-lain', 'kode' => '2102', 'nama' => 'Utang Lain-lain']
        // ];
        
        // $akun_uang_muka = [
        //     (object)['id' => 'Uang Muka Pembelian', 'kode' => '1101', 'nama' => 'Uang Muka Pembelian'],
        //     (object)['id' => 'Kas', 'kode' => '1102', 'nama' => 'Kas'],
        //     (object)['id' => 'Bank', 'kode' => '1103', 'nama' => 'Bank']
        // ];

        // $master_kategori = MasterParameter::where('nama_parameter', 'KATEGORI BAHAN')->first();
        // $kategori = $master_kategori ? $master_kategori->details()->where('aktif', 1)->get() : collect();
        
        return view('backend.pemasok.index', compact('pemasok'));
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
            // 'kategori' => 'required|string|max:100',
            'syarat_pembayaran' => 'nullable|string|max:50',
            'default_diskon' => 'nullable|numeric|min:0|max:100',
            'deskripsi_pembelian' => 'nullable|string|max:255',
            'akun_utang' => 'required|in:Utang Usaha,Utang Lain-lain',
            'akun_uang_muka' => 'required|in:Uang Muka Pembelian,Kas,Bank',
            'npwp' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'wajib_pajak' => 'required|in:0,1',
            'alamat' => 'nullable|array',
            'alamat.*.label' => 'nullable|string|max:100',
            'alamat.*.alamat' => 'nullable|string',
            'alamat.*.kota' => 'nullable|string|max:100',
            'alamat.*.provinsi' => 'nullable|string|max:100',
            'alamat.*.kode_pos' => 'nullable|string|max:20',
            'alamat_utama' => 'nullable|integer',
            'rekening' => 'nullable|array',
            'rekening.*.bank' => 'nullable|string|max:100',
            'rekening.*.cabang' => 'nullable|string|max:100',
            'rekening.*.nomor' => 'nullable|string|max:50',
            'rekening.*.nama_pemilik' => 'nullable|string|max:255',
            'rekening_utama' => 'nullable|integer',
            'utang_awal' => 'nullable|array',
            'utang_awal.*.tanggal' => 'nullable|date',
            'utang_awal.*.jumlah' => 'nullable|numeric|min:0',
            'utang_awal.*.mata_uang' => 'nullable|string|max:10',
            'utang_awal.*.syarat_pembayaran' => 'nullable|string|max:50',
            'utang_awal.*.nomor' => 'nullable|string|max:50',
            'utang_awal.*.keterangan' => 'nullable|string|max:255',
        ]);

        // Generate kode pemasok
        $validatedData['kode_pemasok'] = IdGenerator::generate([
            'table' => 'pemasok',
            'field' => 'kode_pemasok',
            'length' => 9,
            'prefix' => 'SUP-'
        ]);

        // Konversi status dan boolean fields
        $validatedData['status'] = $validatedData['status'] === '1';
        $validatedData['wajib_pajak'] = $validatedData['wajib_pajak'] === '1';

        try {
            Pemasok::create($validatedData);
            return response()->json(['success' => true, 'message' => 'Data pemasok berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'status' => 'required|in:1,0',
                'no_telp' => 'nullable|string|max:20',
                'handphone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                // 'kategori' => 'required|string|max:100',
                'syarat_pembayaran' => 'nullable|string|max:50',
                'default_diskon' => 'nullable|numeric|min:0|max:100',
                'deskripsi_pembelian' => 'nullable|string|max:255',
                'akun_utang' => 'required|in:Utang Usaha,Utang Lain-lain',
                'akun_uang_muka' => 'required|in:Uang Muka Pembelian,Kas,Bank',
                'npwp' => 'nullable|string|max:50',
                'nik' => 'nullable|string|max:50',
                'wajib_pajak' => 'required|in:0,1',
                'alamat' => 'nullable|array',
                'alamat.*.label' => 'nullable|string|max:100',
                'alamat.*.alamat' => 'nullable|string',
                'alamat.*.kota' => 'nullable|string|max:100',
                'alamat.*.provinsi' => 'nullable|string|max:100',
                'alamat.*.kode_pos' => 'nullable|string|max:20',
                'alamat_utama' => 'nullable|integer',
                'rekening' => 'nullable|array',
                'rekening.*.bank' => 'nullable|string|max:100',
                'rekening.*.cabang' => 'nullable|string|max:100',
                'rekening.*.nomor' => 'nullable|string|max:50',
                'rekening.*.nama_pemilik' => 'nullable|string|max:255',
                'rekening_utama' => 'nullable|integer',
                'utang_awal' => 'nullable|array',
                'utang_awal.*.tanggal' => 'nullable|date',
                'utang_awal.*.jumlah' => 'nullable|numeric|min:0',
                'utang_awal.*.mata_uang' => 'nullable|string|max:10',
                'utang_awal.*.syarat_pembayaran' => 'nullable|string|max:50',
                'utang_awal.*.nomor' => 'nullable|string|max:50',
                'utang_awal.*.keterangan' => 'nullable|string|max:255',
            ]);

            // Konversi status dan boolean fields
            $validated['status'] = $validated['status'] === '1';
            $validated['wajib_pajak'] = $validated['wajib_pajak'] === '1';

            $pemasok->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data pemasok berhasil diperbarui.'
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

    public function destroy(Pemasok $pemasok)
    {
        $pemasok->delete();
        return redirect()->route('backend.pemasok.index')
            ->with('success', 'Data pemasok berhasil dihapus.');
    }

    public function show($id)
    {
        try {
            $pemasok = Pemasok::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $pemasok
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pemasok.'
            ], 500);
        }
    }
}
