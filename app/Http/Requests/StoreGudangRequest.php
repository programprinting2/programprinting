<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGudangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
        ];
    }

    public function messages(): array
    {
        return [
            'nama_gudang.required' => 'Nama gudang harus diisi',
            'manager.required' => 'Manager harus diisi',
            'kapasitas.required' => 'Kapasitas harus diisi',
            'status.required' => 'Status harus dipilih',
            'alamat.required' => 'Alamat harus diisi',
        ];
    }
}










