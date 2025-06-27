@extends('layout.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="fw-bold mb-0">Detail Pembelian</h3>
    <div class="text-muted small">{{ $pembelian->pemasok->nama ?? '-' }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="btn btn-outline-primary"><i class="fa fa-edit me-1"></i> Edit</a>
    <a href="#" class="btn btn-outline-secondary"><i class="fa fa-print me-1"></i> Cetak</a>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <ul class="nav nav-tabs mb-3" id="tabPembelianDetail" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detailPesanan" type="button" role="tab">Detail Pesanan</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="item-tab" data-bs-toggle="tab" data-bs-target="#itemPembelian" type="button" role="tab">Item Pembelian</button>
      </li>
    </ul>
    <div class="tab-content" id="tabPembelianDetailContent">
      <div class="tab-pane fade show active" id="detailPesanan" role="tabpanel">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-2"><span class="text-muted">Nomor PO</span><br><span class="fw-semibold">{{ $pembelian->kode_pembelian }}</span></div>
                <div class="mb-2"><span class="text-muted">Tanggal Pesanan</span><br><span class="fw-semibold">{{ tanggal_indo($pembelian->tanggal) }}</span></div>
                <div class="mb-2"><span class="text-muted">Tanggal Diharapkan</span><br><span class="fw-semibold">{{ tanggal_indo($pembelian->jatuh_tempo) }}</span></div>
              </div>
              <div class="col-md-6">
                <div class="mb-2"><span class="text-muted">Status</span><br><span class="fw-semibold">-</span></div>
                <div class="mb-2"><span class="text-muted">Total Pembelian</span><br><span class="fw-semibold text-success">Rp{{ number_format($pembelian->items->sum('subtotal'),0,',','.') }}</span></div>
                <div class="mb-2"><span class="text-muted">Progress Penerimaan</span><br>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                  </div>
                  <span class="small">100%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="itemPembelian" role="tabpanel">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="fa fa-cube me-1"></i> Item Pembelian</h5>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Material</th>
                    <th>Qty Pesan</th>
                    <th>Harga Satuan</th>
                    <th>Diskon (%)</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pembelian->items as $item)
                  <tr>
                    <td>{{ $item->bahanBaku->nama ?? '-' }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp{{ number_format($item->harga,0,',','.') }}</td>
                    <td>{{ $item->diskon_persen ?? 0 }}%</td>
                    <td>Rp{{ number_format($item->subtotal,0,',','.') }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada item pembelian</td>
                  </tr>
                  @endforelse
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="4" class="text-end">Total Pembelian:</th>
                    <th class="text-success">Rp{{ number_format($pembelian->items->sum('subtotal'),0,',','.') }}</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fa fa-dollar-sign me-1"></i> Ringkasan</h5>
        <div class="d-flex justify-content-between mb-1"><span>Jumlah Item</span><span>{{ $pembelian->items->count() }} jenis</span></div>
        <div class="d-flex justify-content-between mb-1"><span>Total Quantity</span><span>{{ $pembelian->items->sum('jumlah') }}</span></div>
        <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>Rp{{ number_format($pembelian->items->sum('subtotal'),0,',','.') }}</span></div>
        <div class="d-flex justify-content-between mb-1"><span>PPN ({{ $pembelian->tarif_pajak ?? 11 }}%)</span><span>Rp{{ number_format(($pembelian->items->sum('subtotal') - ($pembelian->items->sum('subtotal') * ($pembelian->diskon_persen ?? 0)/100)) * (($pembelian->tarif_pajak ?? 11)/100),0,',','.') }}</span></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="text-success">Rp{{ number_format(($pembelian->items->sum('subtotal') - ($pembelian->items->sum('subtotal') * ($pembelian->diskon_persen ?? 0)/100)) + (($pembelian->items->sum('subtotal') - ($pembelian->items->sum('subtotal') * ($pembelian->diskon_persen ?? 0)/100)) * (($pembelian->tarif_pajak ?? 11)/100)),0,',','.') }}</span></div>
      </div>
    </div>
  </div>
</div>
@endsection 