<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tanggal_spk' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_id.required' => 'Pelanggan harus dipilih',
            'pelanggan_id.exists' => 'Pelanggan tidak ditemukan',
            'tanggal_spk.required' => 'Tanggal SPK harus diisi',
            'tanggal_spk.before_or_equal' => 'Tanggal SPK tidak boleh di masa depan',
            'catatan.max' => 'Catatan maksimal 1000 karakter',
        ];
    }
}










