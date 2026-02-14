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
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tanggal_spk' => 'required|date',
            'status' => 'nullable|string|in:verifikasi,sudah_bayar,proses_produksi,sudah_cetak,siap_antar',
            'catatan' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1|max:50',
            'items.*.produk_id' => 'nullable|exists:produk,id',
            'items.*.nama_produk' => 'required|string|max:255',
            'items.*.jumlah' => 'required|numeric|min:0.01|max:999999',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.keterangan' => 'nullable|string|max:500',
            'items.*.lebar' => 'nullable|numeric|min:0|max:9999.99',
            'items.*.panjang' => 'nullable|numeric|min:0|max:9999.99',
            'items.*.deadline' => 'nullable|date',
            'items.*.is_urgent' => 'nullable|boolean',
            'items.*.biaya_produk' => 'nullable|numeric|min:0|max:999999999',
            'items.*.biaya_finishing' => 'nullable|numeric|min:0|max:999999999',
            'items.*.tipe_finishing' => 'nullable|array',
            'items.*.tugas_produksi' => 'nullable|array',
            'items.*.file_pendukung' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_id.required' => 'Pelanggan harus dipilih',
            'pelanggan_id.exists' => 'Pelanggan tidak ditemukan',
            'tanggal_spk.required' => 'Tanggal SPK harus diisi',
            'items.required' => 'Minimal harus ada 1 item pekerjaan',
            'items.min' => 'Minimal harus ada 1 item pekerjaan',
            'items.*.nama_produk.required' => 'Nama produk item harus diisi',
            'items.*.jumlah.required' => 'Jumlah item harus diisi',
            'items.*.jumlah.min' => 'Jumlah minimal 0.01',
            'items.*.satuan.required' => 'Satuan item harus diisi',
            'catatan.max' => 'Catatan maksimal 1000 karakter',
        ];
    }

    public function prepareForValidation(): void
    {
        // Form mengirim customer_id; kita pakai pelanggan_id di backend
        if ($this->has('customer_id') && !$this->has('pelanggan_id')) {
            $this->merge(['pelanggan_id' => $this->input('customer_id')]);
        }

        if (!$this->has('items')) {
            return;
        }

        $items = $this->input('items', []);

        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }

        if (!is_array($items)) {
            $items = [];
        }

        foreach ($items as $key => $item) {
            if (!is_array($item)) {
                continue;
            }

            // Normalisasi dari camelCase (JS) ke snake_case
            $items[$key]['tugas_produksi'] = $this->normalizeTugasProduksi($item['tugasProduksi'] ?? $item['tugas_produksi'] ?? []);
            $items[$key]['file_pendukung'] = $this->normalizeFilePendukung($item['filePendukung'] ?? $item['files'] ?? $item['file_pendukung'] ?? []);
            $items[$key]['tipe_finishing'] = $item['tipe_finishing'] ?? [];

            $items[$key]['produk_id'] = !empty($item['produk_id']) ? (int) $item['produk_id'] : null;
            $items[$key]['jumlah'] = isset($item['jumlah']) ? (float) $item['jumlah'] : 0;
            $items[$key]['lebar'] = isset($item['lebar']) && $item['lebar'] !== '' ? (float) $item['lebar'] : null;
            $items[$key]['panjang'] = isset($item['panjang']) && $item['panjang'] !== '' ? (float) $item['panjang'] : null;
            $items[$key]['deadline'] = !empty($item['deadline']) ? $item['deadline'] : null;
            $items[$key]['is_urgent'] = isset($item['is_urgent']) ? (bool) $item['is_urgent'] : false;
            $items[$key]['biaya_finishing'] = isset($item['biaya_finishing']) ? (float) $item['biaya_finishing'] : 0;
        }

        $this->merge(['items' => $items]);
    }

    /**
     * @param array<int, mixed> $raw
     * @return array<int, array<string, mixed>>
     */
    private function normalizeTugasProduksi(array $raw): array
    {
        $out = [];
        foreach ($raw as $t) {
            if (!is_array($t)) {
                continue;
            }
            $out[] = [
                'nama_tugas' => $t['nama_tugas'] ?? $t['nama'] ?? '',
                'ditugaskan_id' => $t['ditugaskan_id'] ?? $t['ditugaskanId'] ?? null,
                'ditugaskan' => $t['ditugaskan'] ?? '',
                'mesin' => $t['mesin'] ?? null,
                'waktu' => (int) ($t['waktu'] ?? 0),
                'biaya' => (int) ($t['biaya'] ?? $t['harga'] ?? 0),
                'deskripsi' => $t['deskripsi'] ?? null,
            ];
        }
        return $out;
    }

    /**
     * @param array<int, mixed> $raw
     * @return array<int, array<string, mixed>>
     */
    private function normalizeFilePendukung(array $raw): array
    {
        $out = [];
        foreach ($raw as $f) {
            if (is_string($f)) {
                $out[] = ['path' => $f, 'name' => basename($f), 'type' => '', 'size' => 0];
                continue;
            }
            if (!is_array($f)) {
                continue;
            }
            $out[] = [
                'path' => $f['path'] ?? '',
                'name' => $f['name'] ?? '',
                'type' => $f['type'] ?? '',
                'size' => (int) ($f['size'] ?? 0),
            ];
        }
        return $out;
    }
}