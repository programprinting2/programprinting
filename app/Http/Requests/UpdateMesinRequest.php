<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MasterParameter;

class UpdateMesinRequest extends FormRequest
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
            'tanggal_pembelian' => 'nullable|date',
            'harga_pembelian' => ['nullable', function($attribute, $value, $fail) {
                if ($value !== null) {
                    $cleanValue = str_replace(['.', ','], '', $value);
                    if (!is_numeric($cleanValue)) {
                        $fail('Format harga pembelian tidak valid.');
                    }
                }
            }],
            'lebar_media_maksimum' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'detail_mesin_json' => 'required|json',
            'biaya_perhitungan_profil_json' => 'required|json',
            'hapus_gambar' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_mesin.required' => 'Nama mesin harus diisi',
            'tipe_mesin.required' => 'Tipe mesin harus dipilih',
            'detail_mesin_json.required' => 'Detail mesin harus diisi',
            'biaya_perhitungan_profil_json.required' => 'Biaya perhitungan profil harus diisi',
        ];
    }
}








