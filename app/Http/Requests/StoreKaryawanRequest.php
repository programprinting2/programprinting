<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'tanggal_lahir' => 'nullable|date|before:today',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|in:Belum Menikah,Menikah,Cerai',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gaji_pokok' => 'nullable|integer|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'alamat' => 'nullable|array',
            'alamat.*.label' => 'nullable|string|max:100',
            'alamat.*.alamat' => 'nullable|string',
            'alamat.*.kota' => 'nullable|string|max:100',
            'alamat.*.provinsi' => 'nullable|string|max:100',
            'alamat.*.kode_pos' => 'nullable|string|max:20',
            'rekening' => 'nullable|array',
            'rekening.*.bank' => 'nullable|string|max:100',
            'rekening.*.nomor' => 'nullable|string|max:50',
            'rekening.*.nama_pemilik' => 'nullable|string|max:255',
            'alamat_utama' => 'nullable|integer',
            'rekening_utama' => 'nullable|integer',
            'npwp' => 'nullable|string|max:255',
            'status_pajak' => 'nullable|string|max:255',
            'tarif_pajak' => 'nullable|integer|min:0|max:100',
            'estimasi_hari_kerja' => 'nullable|integer|min:0|max:31',
            'jam_kerja_per_hari' => 'nullable|integer|min:0|max:24',
            'komponen_gaji' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'posisi.required' => 'Posisi harus diisi',
            'departemen.required' => 'Departemen harus diisi',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'status.required' => 'Status harus dipilih',
        ];
    }
}










