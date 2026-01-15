<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'kategori_utama_id' => 'required|exists:detail_parameters,id',
            'sub_kategori_id' => 'nullable|exists:sub_detail_parameter,id',
            'satuan_id' => 'required|exists:detail_parameters,id',
            'sub_satuan_id' => 'required|exists:sub_detail_parameter,id',
            'lebar' => 'nullable|integer|min:0',
            'panjang' => 'nullable|integer|min:0',
            'jenis_produk' => 'required|in:produk,jasa',
            'status_aktif' => 'required|boolean',
            'bahan_baku_json' => 'nullable|json',
            'harga_bertingkat_json' => 'nullable|json',
            'harga_reseller_json' => 'nullable|json',
            'foto_pendukung_new.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:5048',
            'video_pendukung_new.*' => 'nullable|file|mimes:mp4,avi,mpeg,quicktime|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:20480',
            'dokumen_pendukung_new.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,csv,jpg,jpeg,png,gif|max:10240',
            'alur_produksi_json' => 'nullable|json',
            'parameter_modal_json' => 'nullable|json',
            'spesifikasi_teknis_json' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk harus diisi',
            'kategori_utama_id.required' => 'Kategori utama harus dipilih',
            'kategori_utama_id.exists' => 'Kategori utama tidak ditemukan',
            'satuan_id.required' => 'Satuan harus dipilih',
            'satuan_id.exists' => 'Satuan tidak ditemukan',
        ];
    }
}
