@extends('layout.master')
@section('content')
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-lg-7 mb-3">
      <div class="card p-4 h-100">
        <div class="mb-3">
          <h2 class="fw-bold mb-0">Kasir - Pembayaran Invoice</h2>
        </div>
        <div class="mb-3 d-flex gap-2 align-items-center">
          <input type="text" class="form-control" style="max-width:320px;" placeholder="Cari invoice...">
          <select class="form-select" style="max-width:180px;">
            <option>Semua Status</option>
            <option value="pending">Pending</option>
            <option value="partial">Partial</option>
            <option value="paid">Paid</option>
          </select>
        </div>
        <div class="fw-bold fs-5 mb-1">Daftar Invoice</div>
        <div class="text-muted mb-2" style="font-size: 1rem;">Klik pada invoice untuk membuat pembayaran</div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>No. Invoice</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total</th>
                <th>Sisa</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="invoiceTableBody">
              @foreach($invoices as $inv)
              <tr class="invoice-row" data-invoice="{{ $inv['no'] }}">
                <td class="fw-bold">{{ $inv['no'] }}</td>
                <td>{{ $inv['customer'] }}</td>
                <td>
                  <span class="badge rounded-pill border border-1 text-dark bg-white text-lowercase" style="font-size:1em;">
                    {{ $inv['status'] }}
                  </span>
                </td>
                <td>Rp {{ number_format($inv['total'],0,',','.') }}</td>
                <td class="fw-bold" style="color:#d32f2f;">Rp {{ number_format($inv['sisa'],0,',','.') }}</td>
                <td>
                  <button class="btn btn-sm btn-link text-dark" title="Lihat"><i class="fas fa-search"></i></button>
                  <button class="btn btn-sm btn-link text-dark" title="Cetak"><i class="fas fa-print"></i></button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-5 mb-3">
      <div class="card p-4 h-100">
        <div class="fw-bold fs-4 mb-1">Form Pembayaran</div>
        <div class="text-muted mb-3">Pilih invoice terlebih dahulu</div>
        <div id="formPembayaranDefault" class="text-center py-5">
          <i class="fas fa-credit-card fa-4x mb-3 text-secondary"></i>
          <div class="fw-bold fs-5 mb-2">Belum ada invoice terpilih</div>
          <div class="text-muted">Pilih invoice dari daftar di sebelah kiri untuk melakukan pembayaran</div>
        </div>
        <div id="formPembayaranDetail" style="display:none;">
          <div class="border rounded p-3 mb-3 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <div class="fw-bold">Customer</div>
                <div id="detailCustomer">-</div>
              </div>
              <div>
                <div class="fw-bold">Status</div>
                <span id="detailStatus" class="badge rounded-pill border border-1 text-dark bg-white text-lowercase">-</span>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-muted small">Total Invoice</div>
                <div class="fw-bold" id="detailTotal">Rp 0</div>
              </div>
              <div class="col-6 text-end">
                <div class="text-muted small">Sudah Dibayar</div>
                <div class="fw-bold text-success" id="detailDibayar">Rp 0</div>
              </div>
            </div>
            <div class="text-muted small">Sisa Pembayaran</div>
            <div class="fw-bold fs-5 mb-0" id="detailSisa" style="color:#d32f2f;">Rp 0</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Jumlah Pembayaran</label>
            <input type="text" class="form-control" id="inputJumlahBayar" placeholder="Rp 0">
            <div class="text-muted small mt-1">Masukkan jumlah pembayaran tanpa titik atau koma</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Metode Pembayaran</label>
            <select class="form-select" id="inputMetode">
              <option>Tunai</option>
              <option>Transfer</option>
              <option>QRIS</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Nomor Referensi</label>
            <input type="text" class="form-control" id="inputRef" placeholder="Contoh: TRX123456">
            <div class="text-muted small">Nomor referensi/transaksi dari pembayaran (opsional)</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Catatan</label>
            <textarea class="form-control" id="inputCatatan" rows="2" placeholder="Catatan tambahan untuk pembayaran ini"></textarea>
          </div>
          <button class="btn btn-primary w-100" id="btnProsesBayar"><i class="fas fa-check me-1"></i> Proses Pembayaran</button>
        </div>
      </div>
    </div>
  </div>
</div>
@push('custom-scripts')
<script>
  // Data dummy invoice (harus sama urutan dengan $invoices di controller)
  const invoiceData = @json($invoices);

  function formatRupiah(num) {
    return 'Rp ' + (num ? num.toLocaleString('id-ID') : '0');
  }

  document.querySelectorAll('.invoice-row').forEach((row, idx) => {
    row.addEventListener('click', function() {
      // Highlight row
      document.querySelectorAll('.invoice-row').forEach(r => r.classList.remove('table-active'));
      row.classList.add('table-active');
      // Tampilkan form pembayaran detail
      document.getElementById('formPembayaranDefault').style.display = 'none';
      document.getElementById('formPembayaranDetail').style.display = '';
      // Ambil data
      const inv = invoiceData[idx];
      document.getElementById('detailCustomer').textContent = inv.customer;
      document.getElementById('detailStatus').textContent = inv.status;
      document.getElementById('detailTotal').textContent = formatRupiah(inv.total);
      document.getElementById('detailSisa').textContent = formatRupiah(inv.sisa);
      document.getElementById('detailDibayar').textContent = formatRupiah(inv.sudah_dibayar || (inv.total - inv.sisa));
    });
  });
</script>
@endpush
@endsection 