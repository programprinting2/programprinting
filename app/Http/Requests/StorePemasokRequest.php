<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePemasokRequest extends FormRequest
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
            'website' => 'nullable|url|max:2048',
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
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pemasok harus diisi',
            'status.required' => 'Status harus dipilih',
            'akun_utang.required' => 'Akun utang harus dipilih',
            'akun_uang_muka.required' => 'Akun uang muka harus dipilih',
        ];
    }
}










