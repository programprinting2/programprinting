<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpkItemCetakProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spk_item_id' => ['required', 'integer', 'exists:spk_items,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'spk_item_id.required' => 'Item wajib dipilih.',
            'spk_item_id.exists' => 'Item tidak ditemukan.',
            'jumlah.required' => 'Jumlah cetak wajib diisi.',
            'jumlah.min' => 'Jumlah cetak minimal 1.',
        ];
    }
}