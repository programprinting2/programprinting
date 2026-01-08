<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBahanBakuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|max:255',
            'kategori_id' => 'required|exists:detail_parameters,id',
            'sub_kategori_id' => 'required|exists:sub_detail_parameter,id',
            'satuan_utama_id' => 'required|exists:detail_parameters,id',
            'status_aktif' => 'required|in:0,1',
            'konversi_satuan_json' => 'nullable|string',
            'pemasok_utama_id' => 'nullable|exists:pemasok,id',
            'harga_terakhir' => 'nullable|numeric|min:0',
            'stok_saat_ini' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'stok_maksimum' => 'nullable|integer|min:0',
            'detail_spesifikasi_json' => 'nullable|json',
            'foto_pendukung_new.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:5048',
            'video_pendukung_new.*' => 'nullable|file|mimes:mp4,avi,mpeg,quicktime|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:20480',
            'dokumen_pendukung_new.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,csv,jpg,jpeg,png,gif|max:10240',
            'keterangan' => 'nullable|string',
            'link_pendukung_json' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'kategori_id.required' => 'Kategori harus dipilih',
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'sub_kategori_id.required' => 'Sub kategori harus dipilih',
            'sub_kategori_id.exists' => 'Sub kategori tidak ditemukan',
            'satuan_utama_id.required' => 'Satuan utama harus dipilih',
            'satuan_utama_id.exists' => 'Satuan utama tidak ditemukan',
            'status_aktif.required' => 'Status aktif harus dipilih',
        ];
    }
}








