<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkCompleteSpkItemCetakRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'spk_item_ids.required' => 'Pilih minimal 1 item untuk bulk print.',
            'spk_item_ids.array' => 'Format data bulk print tidak valid.',
            'spk_item_ids.min' => 'Pilih minimal 1 item untuk bulk print.',
        ];
    }
}