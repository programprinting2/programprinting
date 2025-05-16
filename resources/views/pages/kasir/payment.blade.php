@extends('layout.master')
@section('content')
<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Pembayaran Invoice</h2>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
  </div>
  <div class="row">
    <div class="col-lg-7 mb-3">
      <div class="card p-4">
        <h5 class="fw-bold mb-1">Form Pembayaran</h5>
        <p class="text-muted mb-4">Masukkan detail pembayaran untuk invoice {{ $invoice['no'] }}</p>
        
        <form action="{{ route('kasir.invoice.payment.store', $invoice['no']) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold">Jumlah Pembayaran</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" class="form-control" name="jumlah" required placeholder="0" value="{{ $invoice['sisa'] }}">
            </div>
            <div class="form-text">Masukkan jumlah pembayaran tanpa titik atau koma</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Metode Pembayaran</label>
            <select class="form-select" name="metode" required>
              <option value="">Pilih metode pembayaran...</option>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="Tunai">Tunai</option>
              <option value="QRIS">QRIS</option>
              <option value="Debit">Debit</option>
              <option value="Credit Card">Credit Card</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Pembayaran</label>
            <input type="date" class="form-control" name="tanggal" required value="{{ date('Y-m-d') }}">
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Referensi</label>
            <input type="text" class="form-control" name="referensi" placeholder="Contoh: TRX123456">
            <div class="form-text">Nomor referensi/transaksi dari pembayaran (opsional)</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Catatan</label>
            <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tambahan untuk pembayaran ini"></textarea>
          </div>
          
          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('kasir.invoice.show', $invoice['no']) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
          </div>
        </form>
      </div>
    </div>
    
    <div class="col-lg-5 mb-3">
      <div class="card p-4">
        <h5 class="fw-bold mb-3">Informasi Invoice</h5>
        
        <div class="mb-3">
          <div class="text-muted mb-1">Nomor Invoice</div>
          <div class="fw-bold fs-5">{{ $invoice['no'] }}</div>
        </div>
        
        <div class="mb-3">
          <div class="text-muted mb-1">Status</div>
          <span class="badge bg-white border-1 text-dark px-3">{{ $invoice['status'] }}</span>
        </div>
        
        <div class="mb-3">
          <div class="text-muted mb-1">Customer</div>
          <div class="fw-bold">{{ $invoice['customer']['nama'] }}</div>
        </div>
        
        <hr class="my-3">
        
        <div class="d-flex justify-content-between mb-2">
          <div class="text-muted">Total Invoice</div>
          <div class="fw-bold">Rp {{ number_format($invoice['total'],0,',','.') }}</div>
        </div>
        
        <div class="d-flex justify-content-between mb-2">
          <div class="text-muted">Sudah Dibayar</div>
          <div class="fw-bold text-success">Rp {{ number_format($invoice['dibayar'],0,',','.') }}</div>
        </div>
        
        <div class="d-flex justify-content-between mb-4">
          <div class="text-muted">Sisa Pembayaran</div>
          <div class="fw-bold text-danger">Rp {{ number_format($invoice['sisa'],0,',','.') }}</div>
        </div>
        
        <button class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2" 
                onclick="document.querySelector('input[name=jumlah]').value='{{ $invoice['sisa'] }}'">
          <i class="fas fa-credit-card"></i> Bayar Sisa
        </button>
      </div>
    </div>
  </div>
</div>
@endsection 