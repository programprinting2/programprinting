<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePembelianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_pembelian' => 'required|date',
            'pemasok_id' => 'required|exists:pemasok,id',
            'nomor_form' => 'nullable|string|max:255',
            'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_pembelian',
            'catatan' => 'nullable|string',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'jumlah_diskon' => 'nullable|integer|min:0',
            'biaya_pengiriman' => 'nullable|integer|min:0',
            'tarif_pajak' => 'nullable|numeric|min:0|max:100',
            'nota_kredit' => 'nullable|integer|min:0',
            'biaya_lain' => 'nullable|integer|min:0',
            'items' => 'required|array|min:1|max:100',
            'items.*.bahanbaku_id' => 'required|exists:bahan_baku,id',
            'items.*.jumlah' => 'required|integer|min:1|max:999999',
            'items.*.harga' => 'required|integer|min:0|max:999999999',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
            'items.*.satuan' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_pembelian.required' => 'Tanggal pembelian harus diisi',
            'pemasok_id.required' => 'Pemasok harus dipilih',
            'pemasok_id.exists' => 'Pemasok tidak ditemukan',
            'items.required' => 'Minimal harus ada 1 item pembelian',
            'items.*.bahanbaku_id.required' => 'Bahan baku item harus dipilih',
            'items.*.jumlah.required' => 'Jumlah item harus diisi',
            'items.*.harga.required' => 'Harga item harus diisi',
        ];
    }
}










