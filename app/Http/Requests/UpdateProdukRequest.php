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
            'lebar' => 'nullable|numeric|min:0',
            'panjang' => 'nullable|numeric|min:0',
            'jenis_produk' => 'required|in:produk,jasa',
            'status_aktif' => 'required|boolean',
            'bahan_baku' => 'required|array|min:1',
            'bahan_baku.*.id' => 'required|integer|exists:bahan_baku,id',
            'bahan_baku.*.jumlah' => 'required|numeric|min:0.01',
            'bahan_baku.*.harga' => 'required|integer|min:0',
            'harga_bertingkat_json' => 'nullable|json',
            'harga_reseller_json' => 'nullable|json',
            'foto_pendukung_new.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:5048',
            'video_pendukung_new.*' => 'nullable|file|mimes:mp4,avi,mpeg,quicktime|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:20480',
            'dokumen_pendukung_new.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,csv,jpg,jpeg,png,gif|max:10240',
            'alur_produksi_json' => 'nullable|json',
            'parameter_modal_json' => 'nullable|json',
            'spesifikasi_teknis_json' => 'nullable|json',
            'biaya_tambahan_json' => 'nullable|json',
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
            // 'bahan_baku.required' => 'Bahan baku harus dipilih minimal 1',
            'bahan_baku.*.id.required' => 'ID bahan baku harus diisi',
            'bahan_baku.*.id.exists' => 'Bahan baku tidak ditemukan',
            'bahan_baku.*.jumlah.required' => 'Jumlah bahan baku harus diisi',
            'bahan_baku.*.jumlah.numeric' => 'Jumlah bahan baku harus berupa angka',
            'bahan_baku.*.jumlah.min' => 'Jumlah bahan baku minimal 0.01',
            'bahan_baku.*.harga.required' => 'Harga bahan baku harus diisi',
            'bahan_baku.*.harga.integer' => 'Harga bahan baku harus berupa angka bulat',
            'bahan_baku.*.harga.min' => 'Harga bahan baku minimal 0',
        ];
    }
}
