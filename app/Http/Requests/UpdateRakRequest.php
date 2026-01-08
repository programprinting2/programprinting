<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rakId = $this->route('rak') ?? $this->route('id');
        
        return [
            'gudang_id' => 'required|exists:gudang,id',
            'kode_rak' => 'required|string|max:20|unique:rak,kode_rak,' . $rakId,
            'nama_rak' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:0',
            'jumlah_level' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'kedalaman' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'gudang_id.required' => 'Gudang harus dipilih',
            'nama_rak.required' => 'Nama rak harus diisi',
        ];
    }
}








