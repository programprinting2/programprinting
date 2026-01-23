@extends('layout.master')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
    <h3 class="fw-bold mb-0">Detail Pembelian</h3>
    <div class="text-muted small">dari {{ $pembelian->pemasok->nama ?? '-' }}</div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
      <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
    <a href="{{ route('pembelian.edit', $pembelian->kode_pembelian) }}" class="btn btn-primary">
      <i class="fa fa-edit me-1"></i> Edit Pembelian
    </a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
    <ul class="nav nav-tabs mb-3" id="tabPembelianDetail" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="item-tab" data-bs-toggle="tab" data-bs-target="#itemPembelian" type="button"
        role="tab">Item Pembelian</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detailPesanan"
        type="button" role="tab">Detail Pesanan</button>
      </li>
      
    </ul>
    <div class="tab-content" id="tabPembelianDetailContent">
      <div class="tab-pane fade" id="detailPesanan" role="tabpanel">
      <div class="card mb-3">
        <div class="card-body">
        <div class="row">
          <div class="col-md-6">
          <div class="mb-2"><span class="text-muted">Kode Pembelian</span><br><span
            class="fw-semibold">{{ $pembelian->kode_pembelian }}</span></div>
          <div class="mb-2"><span class="text-muted">Tanggal Pesanan</span><br><span
            class="fw-semibold">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->locale('id')->translatedFormat('d F Y') }}</span>
          </div>
          <div class="mb-2"><span class="text-muted">Tanggal Jatuh Tempo</span><br><span
            class="fw-semibold">{{ $pembelian->jatuh_tempo ? \Carbon\Carbon::parse($pembelian->jatuh_tempo)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
          </div>
          <div class="mb-2"><span class="text-muted">Nomor Form</span><br><span
            class="fw-semibold">{{ $pembelian->nomor_form ?? '-' }}</span></div>
          </div>
          <div class="col-md-6">
          <div class="mb-2"><span class="text-muted">Diskon (%)</span><br><span
            class="fw-semibold">{{ $pembelian->diskon_persen ?? '-' }}%</span></div>
          <div class="mb-2"><span class="text-muted">Catatan</span><br><span
            class="fw-semibold">{{ $pembelian->catatan ?? '-' }}</span></div>
          <div class="mb-2"><span class="text-muted">Total Pembelian</span><br><span
            class="fw-semibold text-success">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span></div>
          </div>
        </div>
        </div>
      </div>
      </div>
      <div class="tab-pane fade show active" id="itemPembelian" role="tabpanel">
      <div class="card mb-3">
        <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fa fa-cube me-1"></i> Item Pembelian</h5>
        <div class="table-responsive">
          <table class="table">
          <thead>
            <tr>
            <th>Kode Bahan</th>
            <th>Bahan Baku</th>
            <th>Qty Pesan</th>
            <th>Satuan</th>
            <th>Harga Satuan</th>
            <th>Diskon (%)</th>
            <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pembelian->items as $item)
        <tr>
        <td>{{ $item->bahanBaku->kode_bahan }}</td>
        <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
        @php
            $satuanId = $item->satuan;
            $jumlahTersimpan = $item->jumlah;
            $bahanBaku = $item->bahanBaku;
            $qtyTampil = $jumlahTersimpan;
            $namaSatuan = $satuanId;
            // Cari nama satuan
            if(isset($satuanList)) {
                foreach($satuanList as $s) {
                    if($s['id'] == $satuanId) { $namaSatuan = $s['nama_sub_detail_parameter']; break; }
                }
            }
            // Konversi qty jika satuan bukan satuan utama
            if($bahanBaku && $satuanId && $bahanBaku->konversi_satuan_json && is_array($bahanBaku->konversi_satuan_json)) {
                $satuanUtamaId = $bahanBaku->satuan_utama_id ?? null;
                if($satuanUtamaId && $satuanId != $satuanUtamaId) {
                    foreach($bahanBaku->konversi_satuan_json as $konv) {
                        if(isset($konv['satuan_dari']) && $konv['satuan_dari'] == $satuanId && isset($konv['jumlah']) && $konv['jumlah'] > 0) {
                            $qtyTampil = $jumlahTersimpan / $konv['jumlah'];
                            break;
                        }
                    }
                }
            }
        @endphp
        <td class="text-end">{{ rtrim(rtrim(number_format($qtyTampil, 4, '.', ''), '0'), '.') }}</td>
        <td>{{ $namaSatuan }}</td>
        <td class="text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
        <td class="text-end">{{ $item->diskon_persen ?? 0 }}%</td>
        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
        <td colspan="5" class="text-center text-muted">Tidak ada item pembelian</td>
        </tr>
        @endforelse
          </tbody>
          <tfoot>
            <tr>
            <th colspan="6" class="text-end">Subtotal Item:</th>
            <th class="text-end">Rp {{ number_format($pembelian->items->sum('subtotal'), 0, ',', '.') }}</th>
            </tr>
          </tfoot>
          </table>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
    <div class="col-md-4 mt-5">
    <div class="card">
      <div class="card-body">
      <h5 class="fw-bold mb-3"><i class="fa fa-dollar-sign me-1"></i> Ringkasan Biaya</h5>

      <!-- Informasi Item -->
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted small">Jumlah Item</span>
        <span class="small">{{ $pembelian->items->count() }} jenis</span>
      </div>

      <hr class="my-2">

      <!-- Breakdown Biaya -->
      <div class="d-flex justify-content-between mb-1">
        <span>Subtotal</span>
        <span>Rp {{ number_format($pembelian->items->sum('subtotal'), 0, ',', '.') }}</span>
      </div>

      @if($pembelian->diskon_persen > 0)
      <div class="d-flex justify-content-between mb-1 text-danger">
      <span>Diskon ({{ $pembelian->diskon_persen }}%)</span>
      <span>- Rp
      {{ number_format($pembelian->items->sum('subtotal') * ($pembelian->diskon_persen / 100), 0, ',', '.') }}</span>
      </div>
    @endif

      @if($pembelian->biaya_pengiriman > 0)
      <div class="d-flex justify-content-between mb-1">
      <span>Biaya Pengiriman</span>
      <span>Rp {{ number_format($pembelian->biaya_pengiriman, 0, ',', '.') }}</span>
      </div>
    @endif

      @if($pembelian->biaya_lain > 0)
      <div class="d-flex justify-content-between mb-1">
      <span>Biaya Lain</span>
      <span>Rp {{ number_format($pembelian->biaya_lain, 0, ',', '.') }}</span>
      </div>
    @endif

      @if($pembelian->nota_kredit > 0)
      <div class="d-flex justify-content-between mb-1 text-success">
      <span>Nota Kredit</span>
      <span>- Rp {{ number_format($pembelian->nota_kredit, 0, ',', '.') }}</span>
      </div>
    @endif

      @if($pembelian->tarif_pajak > 0)
      <div class="d-flex justify-content-between mb-1">
      <span>Pajak ({{ $pembelian->tarif_pajak }}%)</span>
      <span>Rp
      {{ number_format(($pembelian->items->sum('subtotal') * (1 - ($pembelian->diskon_persen / 100))) * ($pembelian->tarif_pajak / 100), 0, ',', '.')}}</span>
      </div>
    @endif

      <hr class="my-2">

      <!-- Total Final -->
      <div class="d-flex justify-content-between fw-bold">
        <span>Total Pembayaran</span>
        <span class="text-success">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span>
      </div>

      <!-- Informasi Pemasok -->
      <!-- <hr class="my-3">
      <div class="text-center">
      <div class="text-muted small mb-1">Pemasok</div>
      <div class="fw-semibold">{{ $pembelian->pemasok->nama ?? '-' }}</div>
      @if($pembelian->pemasok && $pembelian->pemasok->alamat && is_array($pembelian->pemasok->alamat) && isset($pembelian->pemasok->alamat[$pembelian->pemasok->alamat_utama]))
      <div class="text-muted small">{{ $pembelian->pemasok->alamat[$pembelian->pemasok->alamat_utama]['alamat'] }}</div>
      @if($pembelian->pemasok->alamat[$pembelian->pemasok->alamat_utama]['kota'])
        <div class="text-muted small">{{ $pembelian->pemasok->alamat[$pembelian->pemasok->alamat_utama]['kota'] }}</div>
      @endif
      @endif
      </div> -->
      </div>
    </div>
    </div>
  </div>
@endsection