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
        </div>
      </div>
      <div class="card p-4 mb-4">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>Nomor SPK</th>
                <th>Pelanggan</th>
                <th>Tanggal SPK</th>
                <th>Total Biaya</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="spkTableBody">
              @forelse($spkList as $spk)
                <tr>
                  <td>
                    <a href="{{ route('spk.show', $spk) }}" class="fw-bold text-primary text-decoration-none">{{ $spk->nomor_spk }}</a>
                  </td>
                  <td>{{ $spk->pelanggan?->nama ?? '-' }}</td>
                  <td>{{ $spk->tanggal_spk?->format('d/m/Y') ?? '-' }}</td>
                  <td>Rp {{ number_format($spk->total_biaya ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge bg-warning text-dark px-3">{{ $statusList[$spk->status] ?? $spk->status }}</span>
                  </td>
                  <td>
                    <a href="{{ route('spk.show', $spk) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail"><i class="fas fa-file-alt"></i></a>
                    <a href="{{ route('kasir.invoice.payment', $spk->id) }}" class="btn btn-sm btn-outline-secondary" title="Pembayaran"><i class="fas fa-credit-card"></i></a>
                    <!-- <a href="#" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a> -->
                    <!-- <a href="{{ route('kasir.invoice.print', 'INV-2023-002') }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak"><i class="fas fa-print"></i></a> -->
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">Tidak ada SPK dengan status proses bayar</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4">
          <a href="{{ url('/') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
          <div class="text-muted">Menampilkan {{ $spkList->count() }} dari {{ $spkList->total() }} SPK</div>
        </div>
        @if($spkList->hasPages())
          <div class="d-flex justify-content-center mt-3">
            {{ $spkList->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection