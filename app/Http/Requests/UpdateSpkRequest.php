<?php

namespace App\Http\Requests;

class UpdateSpkRequest extends StoreSpkRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['tanggal_spk'] .= '|before_or_equal:today';

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'tanggal_spk.before_or_equal' => 'Tanggal SPK tidak boleh di masa depan',
        ]);
    }
}