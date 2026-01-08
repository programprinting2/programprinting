<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:pelanggan,id',
            'tanggal_spk' => 'required|date|before_or_equal:today',
            'prioritas' => 'required|in:rendah,normal,tinggi,mendesak',
            'catatan' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1|max:50',
            'items.*.nama_produk' => 'required|string|max:255',
            'items.*.jumlah' => 'required|integer|min:1|max:999999',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.keterangan' => 'nullable|string|max:500',
            'items.*.bahan_id' => 'nullable|exists:bahan_baku,id',
            'items.*.lebar' => 'nullable|numeric|min:0|max:9999.99',
            'items.*.panjang' => 'nullable|numeric|min:0|max:9999.99',
            'items.*.biaya_desain' => 'nullable|integer|min:0|max:999999999',
            'items.*.biaya_finishing' => 'nullable|integer|min:0|max:999999999',
            'items.*.tipe_finishing' => 'nullable|array',
            'items.*.tugas_produksi' => 'nullable|array',
            'items.*.file_pendukung' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pelanggan harus dipilih',
            'customer_id.exists' => 'Pelanggan tidak ditemukan',
            'tanggal_spk.required' => 'Tanggal SPK harus diisi',
            'tanggal_spk.before_or_equal' => 'Tanggal SPK tidak boleh di masa depan',
            'prioritas.required' => 'Prioritas harus dipilih',
            'prioritas.in' => 'Prioritas tidak valid',
            'items.required' => 'Minimal harus ada 1 item pekerjaan',
            'items.min' => 'Minimal harus ada 1 item pekerjaan',
            'items.*.nama_produk.required' => 'Nama produk item harus diisi',
            'items.*.jumlah.required' => 'Jumlah item harus diisi',
            'items.*.jumlah.min' => 'Jumlah minimal 1',
            'items.*.satuan.required' => 'Satuan item harus diisi',
            'catatan.max' => 'Catatan maksimal 1000 karakter',
        ];
    }

    public function prepareForValidation(): void
    {
        // Handle items if it's a JSON string (from hidden input)
        if ($this->has('items')) {
            $items = $this->input('items', []);
            
            // If items is a JSON string, decode it
            if (is_string($items)) {
                $decoded = json_decode($items, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $items = $decoded;
                } else {
                    $items = [];
                }
            }
            
            // Ensure items is an array
            if (!is_array($items)) {
                $items = [];
            }
            
            // Clean and cast item data
            foreach ($items as $key => $item) {
                if (!is_array($item)) {
                    continue;
                }
                
                if (isset($item['jumlah'])) {
                    $items[$key]['jumlah'] = (int) $item['jumlah'];
                }
                if (isset($item['lebar'])) {
                    $items[$key]['lebar'] = $item['lebar'] ? (float) $item['lebar'] : null;
                }
                if (isset($item['panjang'])) {
                    $items[$key]['panjang'] = $item['panjang'] ? (float) $item['panjang'] : null;
                }
                if (isset($item['biaya_desain'])) {
                    $items[$key]['biaya_desain'] = $item['biaya_desain'] ? (int) $item['biaya_desain'] : 0;
                }
                if (isset($item['biaya_finishing'])) {
                    $items[$key]['biaya_finishing'] = $item['biaya_finishing'] ? (int) $item['biaya_finishing'] : 0;
                }
                
                // Ensure optional fields have default values
                $items[$key]['tipe_finishing'] = $items[$key]['tipe_finishing'] ?? [];
                $items[$key]['tugas_produksi'] = $items[$key]['tugas_produksi'] ?? [];
                $items[$key]['file_pendukung'] = $items[$key]['file_pendukung'] ?? [];
            }
            
            $this->merge(['items' => $items]);
        }
    }
}

