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
            'customer_id' => 'required|exists:pelanggan,id',
            'tanggal_spk' => 'required|date|before_or_equal:today',
            'prioritas' => 'required|in:rendah,normal,tinggi,mendesak',
            'catatan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pelanggan harus dipilih',
            'customer_id.exists' => 'Pelanggan tidak ditemukan',
            'tanggal_spk.required' => 'Tanggal SPK harus diisi',
            'tanggal_spk.before_or_equal' => 'Tanggal SPK tidak boleh di masa depan',
            'prioritas.required' => 'Prioritas harus dipilih',
            'prioritas.in' => 'Prioritas tidak valid',
            'catatan.max' => 'Catatan maksimal 1000 karakter',
        ];
    }
}










