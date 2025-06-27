<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Pemasok;
use App\Models\BahanBaku;
use Haruncpi\LaravelIdGenerator\IdGenerator;
// use App\Models\PembelianItem;

class PembelianController extends Controller
{
    public function index()
    {
        $data_pembelian = Pembelian::with('pemasok')
            ->orderByDesc('tanggal')
            ->paginate(10);
        return view('pages.pembelian.index', compact('data_pembelian'));
    }

    public function create()
    {
        $pemasok = Pemasok::orderBy('created_at', 'desc')->get();
        $bahan_baku = BahanBaku::orderBy('created_at', 'desc')->get();
        return view('pages.pembelian.create', compact('pemasok', 'bahan_baku'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'pemasok_id' => 'required|exists:pemasok,id',
            'nomor_form' => 'nullable|string|max:255',
            'jatuh_tempo' => 'nullable|date',
            'catatan' => 'nullable|string',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'biaya_pengiriman' => 'nullable|integer|min:0',
            'tarif_pajak' => 'nullable|numeric|min:0|max:100',
            'nota_kredit' => 'nullable|integer|min:0',
            'biaya_lain' => 'nullable|integer|min:0',
            'items' => 'required|array|min:1',
            'items.*.bahanbaku_id' => 'required|exists:bahan_baku,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer|min:0',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        // Generate kode pembelian otomatis dengan IdGenerator
        $kode_pembelian = IdGenerator::generate([
            'table' => 'pembelian',
            'field' => 'kode_pembelian',
            'length' => 12,
            'prefix' => 'PB-' . date('ym') . '-',
            'reset_on_prefix_change' => true
        ]);

        \DB::beginTransaction();
        try {
            // Hitung subtotal item
            $subtotal = 0;
            $items = [];
            foreach ($request->items as $item) {
                $diskon = isset($item['diskon_persen']) ? floatval($item['diskon_persen']) : 0;
                $item_subtotal = ($item['harga'] * $item['jumlah']) * (1 - $diskon/100);
                $items[] = [
                    'bahanbaku_id' => $item['bahanbaku_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'diskon_persen' => $diskon,
                    'subtotal' => $item_subtotal,
                ];
                $subtotal += $item_subtotal;
            }

            // Hitung diskon total
            $diskon_persen = floatval($request->diskon_persen ?? 0);
            $jumlah_diskon = intval($request->jumlah_diskon ?? 0);
            $biaya_pengiriman = intval($request->biaya_pengiriman ?? 0);
            $tarif_pajak = floatval($request->tarif_pajak ?? 0);
            $nota_kredit = intval($request->nota_kredit ?? 0);
            $biaya_lain = intval($request->biaya_lain ?? 0);

            $diskon_total = $jumlah_diskon + round($subtotal * ($diskon_persen/100));
            $dpp = $subtotal - $diskon_total;
            $pajak = round($dpp * ($tarif_pajak/100));
            $total = $dpp + $pajak + $biaya_pengiriman + $biaya_lain - $nota_kredit;

            // Simpan header pembelian
            $pembelian = Pembelian::create([
                'kode_pembelian' => $kode_pembelian,
                'tanggal' => $request->tanggal,
                'pemasok_id' => $request->pemasok_id,
                'nomor_form' => $request->nomor_form,
                'jatuh_tempo' => $request->jatuh_tempo,
                'catatan' => $request->catatan,
                'diskon_persen' => $diskon_persen,
                'biaya_pengiriman' => $biaya_pengiriman,
                'tarif_pajak' => $tarif_pajak,
                'nota_kredit' => $nota_kredit,
                'biaya_lain' => $biaya_lain,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan detail item
            foreach ($items as $item) {
                $pembelian->items()->create($item);
            }

            \DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['pemasok', 'items.bahanBaku'])->findOrFail($id);
        return view('pages.pembelian.show', compact('pembelian'));
    }

    public function edit($id)
    {
        $pembelian = Pembelian::with(['pemasok', 'items.bahanBaku'])->findOrFail($id);
        $pemasok = Pemasok::orderBy('nama')->get();
        $bahan_baku = BahanBaku::orderBy('nama')->get();
        return view('pages.pembelian.edit', compact('pembelian', 'pemasok', 'bahan_baku'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'pemasok_id' => 'required|exists:pemasok,id',
            'nomor_form' => 'nullable|string|max:255',
            'jatuh_tempo' => 'nullable|date',
            'catatan' => 'nullable|string',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'biaya_pengiriman' => 'nullable|integer|min:0',
            'tarif_pajak' => 'nullable|numeric|min:0|max:100',
            'nota_kredit' => 'nullable|integer|min:0',
            'biaya_lain' => 'nullable|integer|min:0',
            'items' => 'required|array|min:1',
            'items.*.bahanbaku_id' => 'required|exists:bahan_baku,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer|min:0',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        \DB::beginTransaction();
        try {
            $pembelian = Pembelian::findOrFail($id);
            // Hitung subtotal item
            $subtotal = 0;
            $items = [];
            foreach ($request->items as $item) {
                $diskon = isset($item['diskon_persen']) ? floatval($item['diskon_persen']) : 0;
                $item_subtotal = ($item['harga'] * $item['jumlah']) * (1 - $diskon/100);
                $items[] = [
                    'bahanbaku_id' => $item['bahanbaku_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'diskon_persen' => $diskon,
                    'subtotal' => $item_subtotal,
                ];
                $subtotal += $item_subtotal;
            }

            // Hitung diskon total
            $diskon_persen = floatval($request->diskon_persen ?? 0);
            $jumlah_diskon = intval($request->jumlah_diskon ?? 0);
            $biaya_pengiriman = intval($request->biaya_pengiriman ?? 0);
            $tarif_pajak = floatval($request->tarif_pajak ?? 0);
            $nota_kredit = intval($request->nota_kredit ?? 0);
            $biaya_lain = intval($request->biaya_lain ?? 0);

            $diskon_total = $jumlah_diskon + round($subtotal * ($diskon_persen/100));
            $dpp = $subtotal - $diskon_total;
            $pajak = round($dpp * ($tarif_pajak/100));
            $total = $dpp + $pajak + $biaya_pengiriman + $biaya_lain - $nota_kredit;

            // Update header pembelian
            $pembelian->update([
                'tanggal' => $request->tanggal_pembelian,
                'pemasok_id' => $request->pemasok_id,
                'nomor_form' => $request->nomor_form,
                'jatuh_tempo' => $request->jatuh_tempo,
                'catatan' => $request->catatan,
                'diskon_persen' => $diskon_persen,
                'biaya_pengiriman' => $biaya_pengiriman,
                'tarif_pajak' => $tarif_pajak,
                'nota_kredit' => $nota_kredit,
                'biaya_lain' => $biaya_lain,
                'updated_at' => now(),
            ]);

            // Hapus semua item lama, insert ulang item baru
            $pembelian->items()->delete();
            foreach ($items as $item) {
                $pembelian->items()->create($item);
            }

            \DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil diupdate.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update pembelian: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        \DB::beginTransaction();
        try {
            $pembelian = Pembelian::findOrFail($id);
            $pembelian->items()->delete();
            $pembelian->delete();
            \DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }
}
