<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmbilPekerjaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spk_item_ids' => ['required', 'array', 'min:1'],
            'spk_item_ids.*' => ['integer', 'distinct', 'exists:spk_items,id'],
            'mesin_id' => ['required', 'integer', 'exists:mesin,id'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ];
    }
}