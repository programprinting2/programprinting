<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MasterParameter;

class StoreMesinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $master_tipe_mesin = MasterParameter::where('nama_parameter', 'TIPE MESIN')->first();
        $valid_tipe_mesin = $master_tipe_mesin ? $master_tipe_mesin->details()->where('aktif', 1)->pluck('nama_detail_parameter')->toArray() : [];

        return [
            'nama_mesin' => 'required|string|max:255',
            'tipe_mesin' => ['required', 'string', 'max:255', function($attribute, $value, $fail) use ($valid_tipe_mesin) {
                if (!in_array($value, $valid_tipe_mesin)) {
                    $fail('Tipe mesin yang dipilih tidak valid.');
                }
            }],
            'merek' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'nomor_seri' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Maintenance,Rusak,Tidak Aktif',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tanggal_pembelian' => 'nullable|date',
            'harga_pembelian' => ['nullable', function($attribute, $value, $fail) {
                if ($value !== null) {
                    $cleanValue = str_replace(['.', ','], '', $value);
                    if (!is_numeric($cleanValue)) {
                        $fail('Format harga pembelian tidak valid.');
                    }
                }
            }],
            'deskripsi' => 'nullable|string|max:1000',
            'lebar_media_maksimum' => 'nullable|numeric|min:0',
            'detail_mesin_json' => 'nullable|json',
            'catatan_tambahan' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'harga_tinta_per_liter' => 'nullable|numeric|min:0',
            'konsumsi_tinta_per_m2' => 'nullable|numeric|min:0',
            'biaya_perhitungan_profil_json' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_mesin.required' => 'Nama mesin harus diisi',
            'tipe_mesin.required' => 'Tipe mesin harus dipilih',
            'status.required' => 'Status harus dipilih',
        ];
    }
}








