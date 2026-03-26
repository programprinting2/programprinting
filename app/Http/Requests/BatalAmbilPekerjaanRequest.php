<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatalAmbilPekerjaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'queue_ids' => ['nullable', 'array', 'min:1'],
            'queue_ids.*' => ['integer', 'distinct', 'exists:spk_item_cetak_queue,id'],
            'spk_item_ids' => ['nullable', 'array', 'min:1'],
            'spk_item_ids.*' => ['integer', 'distinct', 'exists:spk_items,id'],
            'mesin_id' => ['required', 'integer', 'exists:mesin,id'],
        ];
    }
}