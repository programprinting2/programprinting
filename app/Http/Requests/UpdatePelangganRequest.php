<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
            'data_lain.batas_total_piutang_nilai' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pelanggan harus diisi',
            'status.required' => 'Status harus dipilih',
            'email.email' => 'Format email tidak valid',
            'website.url' => 'Format URL tidak valid',
        ];
    }
}










