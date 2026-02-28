<?php

namespace App\Http\Requests;

use App\Models\SPK;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSpkPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah' => 'required|numeric|min:0.01',
            'metode' => 'required|string|in:Transfer Bank,Tunai,QRIS,Debit,Credit Card',
            'tanggal' => 'required|date',
            'referensi' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var SPK|null $spk */
            $spk = $this->route('spk');
            if (!$spk instanceof SPK) {
                return;
            }

            if ($spk->status_pembayaran == 'lunas') {
                $validator->errors()->add('jumlah', 'SPK sudah lunas.');
                return;
            }

            $jumlah = (float) $this->input('jumlah');
            if ($jumlah - 0.00001 > (float) $spk->sisa_pembayaran) {
                $validator->errors()->add('jumlah', 'Jumlah pembayaran melebihi sisa pembayaran.');
            }
        });
    }
}