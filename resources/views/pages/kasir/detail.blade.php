@extends('layout.master')
@section('content')
<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">Detail Invoice {{ $invoice['no'] }}</h2>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
      <a href="#" class="btn btn-outline-secondary"><i class="fas fa-edit me-1"></i> Edit</a>
      <a href="{{ route('kasir.invoice.print', $invoice['no']) }}" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-print me-1"></i> Cetak</a>
      <a href="{{ route('kasir.invoice.payment', $invoice['no']) }}" class="btn btn-primary"><i class="fas fa-credit-card me-1"></i> Pembayaran</a>
    </div>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="fw-bold mb-2">Informasi Invoice</div>
        <div class="mb-2">Status: <span class="badge bg-white border text-dark px-3">{{ $invoice['status'] }}</span></div>
        <div class="mb-2">Nomor Invoice: <span class="fw-bold">{{ $invoice['no'] }}</span></div>
        <div class="mb-2">Tanggal Invoice: <span>{{ $invoice['tanggal'] }}</span></div>
        <div class="mb-2">Jatuh Tempo: <span>{{ $invoice['jatuh_tempo'] }}</span></div>
        <div class="mb-2">SPK No: <a href="#" class="text-primary text-decoration-none">Lihat SPK</a></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="fw-bold mb-2">Informasi Pelanggan</div>
        <div class="mb-1"><b>{{ $invoice['customer']['nama'] }}</b></div>
        <div class="mb-1 text-muted small">{{ $invoice['customer']['email'] }}</div>
        <div class="mb-1 text-muted small">{{ $invoice['customer']['telp'] }}</div>
        <div class="mb-1 text-muted small"><i class="fas fa-info-circle me-1"></i> {{ $invoice['customer']['catatan'] }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="fw-bold mb-2">Ringkasan Biaya</div>
        <div class="d-flex justify-content-between"><span>Subtotal:</span><span>Rp {{ number_format($invoice['ringkasan']['subtotal'],0,',','.') }}</span></div>
        <div class="d-flex justify-content-between"><span>Pajak:</span><span>Rp {{ number_format($invoice['ringkasan']['pajak'],0,',','.') }}</span></div>
        <div class="d-flex justify-content-between"><span>Diskon:</span><span>-Rp {{ number_format($invoice['ringkasan']['diskon'],0,',','.') }}</span></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span>Rp {{ number_format($invoice['ringkasan']['total'],0,',','.') }}</span></div>
        <div class="d-flex justify-content-between"><span>Dibayar:</span><span class="text-success">Rp {{ number_format($invoice['ringkasan']['dibayar'],0,',','.') }}</span></div>
        <div class="d-flex justify-content-between"><span>Sisa:</span><span class="text-danger">Rp {{ number_format($invoice['ringkasan']['sisa'],0,',','.') }}</span></div>
      </div>
    </div>
  </div>
  <div class="card p-4 mb-4">
    <div class="fw-bold mb-3 fs-5">Detail Item</div>
    <div class="text-muted mb-2">Daftar item yang tercantum dalam invoice ini</div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Deskripsi</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Pajak</th>
            <th>Diskon</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoice['items'] as $item)
          <tr>
            <td>{{ $item['deskripsi'] }}</td>
            <td>{{ $item['jumlah'] }}</td>
            <td>Rp {{ number_format($item['harga'],0,',','.') }}</td>
            <td>{{ $item['pajak'] }}</td>
            <td>{{ $item['diskon'] }}</td>
            <td>Rp {{ number_format($item['total'],0,',','.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="card p-4 mb-4">
    <div class="fw-bold mb-3 fs-5">Riwayat Pembayaran</div>
    <div class="text-muted mb-2">Daftar pembayaran yang telah dilakukan</div>
    <div class="table-responsive mb-2">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Metode Pembayaran</th>
            <th>Referensi</th>
            <th>Catatan</th>
            <th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="5" class="text-center text-muted">Belum ada pembayaran yang tercatat</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
      <div class="text-warning d-flex align-items-center gap-2"><i class="fas fa-exclamation-circle"></i> Invoice ini belum dibayar lunas</div>
      <a href="{{ route('kasir.invoice.payment', $invoice['no']) }}" class="btn btn-primary d-flex align-items-center gap-2"><i class="fas fa-credit-card"></i> Tambah Pembayaran</a>
    </div>
  </div>
</div>
@endsection 