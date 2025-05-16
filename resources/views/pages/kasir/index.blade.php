@extends('layout.master')
@section('content')
<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Daftar Invoice</h2>
    <a href="#" class="btn btn-primary d-flex align-items-center gap-2"><i class="fas fa-plus"></i> Buat Invoice Baru</a>
  </div>
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card p-4 mb-4">
        <div class="fw-bold mb-2 fs-5">Filter dan Pencarian</div>
        <div class="text-muted mb-3">Cari berdasarkan nomor invoice atau nama pelanggan</div>
        <div class="row g-2 align-items-center">
          <div class="col-md-9">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari invoice..." id="searchInvoice">
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="filterStatus">
              <option>Semua Status</option>
              <option value="pending">pending</option>
              <option value="partial">partial</option>
              <option value="paid">paid</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card p-4 mb-4">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>No. Invoice</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Jatuh Tempo</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="invoiceTableBody">
              <tr>
                <td><a href="#" class="fw-bold text-primary text-decoration-none">INV-2023-001</a></td>
                <td>PT Maju Bersama Indonesia</td>
                <td>15/7/2023</td>
                <td>15/8/2023</td>
                <td>Rp 450.000</td>
                <td><span class="badge bg-white border text-dark px-3">pending</span></td>
                <td>
                  <a href="{{ route('kasir.invoice.show', 'INV-2023-001') }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail"><i class="fas fa-file-alt"></i></a>
                  <a href="#" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                  <a href="{{ route('kasir.invoice.print', 'INV-2023-001') }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak"><i class="fas fa-print"></i></a>
                  <a href="{{ route('kasir.invoice.payment', 'INV-2023-001') }}" class="btn btn-sm btn-outline-secondary" title="Pembayaran"><i class="fas fa-credit-card"></i></a>
                  <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
              <tr>
                <td><a href="#" class="fw-bold text-primary text-decoration-none">INV-2023-002</a></td>
                <td>PT Global Media Indonesia</td>
                <td>20/6/2023</td>
                <td>20/7/2023</td>
                <td>Rp 850.000</td>
                <td><span class="badge bg-white border text-dark px-3">partial</span></td>
                <td>
                  <a href="{{ route('kasir.invoice.show', 'INV-2023-002') }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail"><i class="fas fa-file-alt"></i></a>
                  <a href="#" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                  <a href="{{ route('kasir.invoice.print', 'INV-2023-002') }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak"><i class="fas fa-print"></i></a>
                  <a href="{{ route('kasir.invoice.payment', 'INV-2023-002') }}" class="btn btn-sm btn-outline-secondary" title="Pembayaran"><i class="fas fa-credit-card"></i></a>
                  <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
              <tr>
                <td><a href="#" class="fw-bold text-primary text-decoration-none">INV-2023-003</a></td>
                <td>Toko Bahagia</td>
                <td>5/6/2023</td>
                <td>5/7/2023</td>
                <td>Rp 275.000</td>
                <td><span class="badge bg-white border text-dark px-3">paid</span></td>
                <td>
                  <a href="{{ route('kasir.invoice.show', 'INV-2023-003') }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail"><i class="fas fa-file-alt"></i></a>
                  <a href="#" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                  <a href="{{ route('kasir.invoice.print', 'INV-2023-003') }}" class="btn btn-sm btn-outline-secondary" title="Cetak"><i class="fas fa-print"></i></a>
                  <a href="{{ route('kasir.invoice.payment', 'INV-2023-003') }}" class="btn btn-sm btn-outline-secondary" title="Pembayaran"><i class="fas fa-credit-card"></i></a>
                  <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4">
          <button class="btn btn-outline-secondary" onclick="window.location.href='/'">Kembali ke Dashboard</button>
          <div class="text-muted">Menampilkan 3 invoice</div>
          <a href="#" class="btn btn-primary d-flex align-items-center gap-2"><i class="fas fa-plus"></i> Buat Invoice Baru</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 