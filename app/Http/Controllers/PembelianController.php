<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Pemasok;
use App\Models\BahanBaku;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\DetailParameter;
// use App\Models\PembelianItem;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with(['pemasok']);

        // Pencarian berdasarkan kode pembelian, nama pemasok, atau nomor form
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_pembelian) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nomor_form) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('pemasok', function($pemasokQuery) use ($search) {
                      $pemasokQuery->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
                                   ->orWhereRaw('LOWER(kode_pemasok) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }

        // Filter berdasarkan pemasok
        if ($request->filled('pemasok_id')) {
            $query->where('pemasok_id', $request->pemasok_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_pembelian', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_pembelian', '<=', $request->tanggal_sampai);
        }

        // Pagination dengan 10 item per halaman
        $data_pembelian = $query->orderBy('created_at', 'desc')->paginate(10);

        // Tambahkan parameter pencarian ke pagination
        if ($request->has('search')) {
            $data_pembelian->appends(['search' => $request->search]);
        }
        if ($request->has('pemasok_id')) {
            $data_pembelian->appends(['pemasok_id' => $request->pemasok_id]);
        }
        if ($request->has('tanggal_dari')) {
            $data_pembelian->appends(['tanggal_dari' => $request->tanggal_dari]);
        }
        if ($request->has('tanggal_sampai')) {
            $data_pembelian->appends(['tanggal_sampai' => $request->tanggal_sampai]);
        }

        // Data untuk dropdown pemasok
        $pemasok_list = Pemasok::where('status', true)->orderBy('nama')->get();

        return view('pages.pembelian.index', compact('data_pembelian', 'pemasok_list'));
    }

    public function create()
    {
        $pemasok = Pemasok::orderBy('created_at', 'desc')->get();
        $bahan_baku = BahanBaku::orderBy('created_at', 'desc')->get();
        $satuanList = DetailParameter::orderBy('nama_detail_parameter')->get(['id', 'nama_detail_parameter'])->toArray();
        
        return view('pages.pembelian.create', compact('pemasok', 'bahan_baku', 'satuanList'));
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
                $bahanBaku = \App\Models\BahanBaku::find($item['bahanbaku_id']);
                $jumlah_input = $item['jumlah'];
                $jumlah_utama = $jumlah_input; // Default: jumlah input = jumlah utama
                
                // Jika ada satuan yang dipilih dan bukan satuan utama, konversi ke satuan utama
                if ($bahanBaku && isset($item['satuan']) && $item['satuan'] && is_array($bahanBaku->konversi_satuan_json)) {
                    foreach ($bahanBaku->konversi_satuan_json as $konv) {
                        if (isset($konv['satuan_dari']) && (string)$konv['satuan_dari'] === (string)$item['satuan']) {
                            // Konversi: jumlah_input × faktor_konversi = jumlah_satuan_utama
                            $faktor_konversi = isset($konv['jumlah']) ? floatval($konv['jumlah']) : 1;
                            $jumlah_utama = $jumlah_input * $faktor_konversi;
                            break;
                        }
                    }
                }
                
                // Perhitungan subtotal: gunakan jumlah input user, bukan jumlah yang sudah dikonversi
                $item_subtotal = $item['harga'] * $jumlah_input * (1 - $diskon/100);
                $items[] = [
                    'bahanbaku_id' => $item['bahanbaku_id'],
                    'jumlah' => $jumlah_utama, // Simpan jumlah dalam satuan utama
                    'satuan' => $item['satuan'] ?? null,
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

            $diskon_total = $jumlah_diskon;
            $dpp = $subtotal - $diskon_total;
            $pajak = round($dpp * ($tarif_pajak/100));
            $total = $dpp + $pajak + $biaya_pengiriman + $biaya_lain - $nota_kredit;

            // Simpan header pembelian
            $pembelian = Pembelian::create([
                'kode_pembelian' => $kode_pembelian,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'pemasok_id' => $request->pemasok_id,
                'nomor_form' => $request->nomor_form,
                'jatuh_tempo' => $request->jatuh_tempo,
                'catatan' => $request->catatan,
                'diskon_persen' => $diskon_persen,
                'biaya_pengiriman' => $biaya_pengiriman,
                'tarif_pajak' => $tarif_pajak,
                'nota_kredit' => $nota_kredit,
                'biaya_lain' => $biaya_lain,
                'total' => $total,
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

    public function show($kode_pembelian)
    {
        $pembelian = Pembelian::with(['pemasok', 'items.bahanBaku'])
            ->where('kode_pembelian', $kode_pembelian)
            ->firstOrFail();
        return view('pages.pembelian.show', compact('pembelian'));
    }

    public function edit($kode_pembelian)
    {
        $pembelian = Pembelian::with(['pemasok', 'items.bahanBaku'])
            ->where('kode_pembelian', $kode_pembelian)
            ->firstOrFail();
        $pemasok = Pemasok::orderBy('created_at', 'desc')->get();
        $bahan_baku = BahanBaku::orderBy('created_at', 'desc')->get();
        $satuanList = DetailParameter::orderBy('nama_detail_parameter')->get(['id', 'nama_detail_parameter'])->toArray();
        
        return view('pages.pembelian.edit', compact('pembelian', 'pemasok', 'bahan_baku', 'satuanList'));
    }

    public function update(Request $request, $kode_pembelian)
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
            $pembelian = Pembelian::where('kode_pembelian', $kode_pembelian)->firstOrFail();
            // Hitung subtotal item
            $subtotal = 0;
            $items = [];
            foreach ($request->items as $item) {
                $diskon = isset($item['diskon_persen']) ? floatval($item['diskon_persen']) : 0;
                $bahanBaku = \App\Models\BahanBaku::find($item['bahanbaku_id']);
                $jumlah_input = $item['jumlah'];
                $jumlah_utama = $jumlah_input; // Default: jumlah input = jumlah utama
                
                // Jika ada satuan yang dipilih dan bukan satuan utama, konversi ke satuan utama
                if ($bahanBaku && isset($item['satuan']) && $item['satuan'] && is_array($bahanBaku->konversi_satuan_json)) {
                    foreach ($bahanBaku->konversi_satuan_json as $konv) {
                        if (isset($konv['satuan_dari']) && (string)$konv['satuan_dari'] === (string)$item['satuan']) {
                            // Konversi: jumlah_input × faktor_konversi = jumlah_satuan_utama
                            $faktor_konversi = isset($konv['jumlah']) ? floatval($konv['jumlah']) : 1;
                            $jumlah_utama = $jumlah_input * $faktor_konversi;
                            break;
                        }
                    }
                }
                
                // Perhitungan subtotal: gunakan jumlah input user, bukan jumlah yang sudah dikonversi
                $item_subtotal = $item['harga'] * $jumlah_input * (1 - $diskon/100);
                $items[] = [
                    'bahanbaku_id' => $item['bahanbaku_id'],
                    'jumlah' => $jumlah_utama, // Simpan jumlah dalam satuan utama
                    'satuan' => $item['satuan'] ?? null,
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

            $diskon_total = $jumlah_diskon;
            $dpp = $subtotal - $diskon_total;
            $pajak = round($dpp * ($tarif_pajak/100));
            $total = $dpp + $pajak + $biaya_pengiriman + $biaya_lain - $nota_kredit;

            // Update header pembelian
            $pembelian->update([
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'pemasok_id' => $request->pemasok_id,
                'nomor_form' => $request->nomor_form,
                'jatuh_tempo' => $request->jatuh_tempo,
                'catatan' => $request->catatan,
                'diskon_persen' => $diskon_persen,
                'biaya_pengiriman' => $biaya_pengiriman,
                'tarif_pajak' => $tarif_pajak,
                'nota_kredit' => $nota_kredit,
                'biaya_lain' => $biaya_lain,
                'total' => $total,
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

    public function destroy($kode_pembelian)
    {
        \DB::beginTransaction();
        try {
            $pembelian = Pembelian::where('kode_pembelian', $kode_pembelian)->firstOrFail();
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
