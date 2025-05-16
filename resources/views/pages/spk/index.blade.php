@extends('layout.master')
@section('content')
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold">Daftar SPK</h2>
      <div class="text-muted">Kelola semua SPK dalam satu tampilan</div>
    </div>
    <a href="{{ route('spk.create') }}" class="btn btn-primary">
      <i class="fa fa-plus me-1"></i> Buat SPK Baru
    </a>
    </div>
    <div class="mb-3">
    <input type="text" class="form-control" placeholder="Cari SPK berdasarkan nomor, pelanggan, atau status...">
    </div>
    <div class="table-responsive">
    <table class="table align-middle">
      <thead>
      <tr>
        <th>Nomor SPK</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>Status</th>
        <th>Prioritas</th>
        <th>Item Pekerjaan</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @forelse($spk as $item)
      <tr>
      <td>{{ $item['nomor'] }}</td>
      <td>{{ $item['tanggal'] }}</td>
      <td>{{ $item['pelanggan'] }}</td>
      <td><span class="badge bg-light text-dark">{{ $item['status'] }}</span></td>
      <td>{{ $item['prioritas'] }}</td>
      <td>
      <ul class="mb-0">
        @foreach($item['item'] as $pekerjaan)
      <li><b>{{ $pekerjaan['nama'] }}</b> ({{ $pekerjaan['jumlah'] }} {{ $pekerjaan['satuan'] }})</li>
      @endforeach
      </ul>
      </td>
      <td>
      <button class="btn btn-sm btn-light"><i class="bi bi-three-dots-vertical"></i></button>
      </td>
      </tr>
    @empty
      <tr>
      <td colspan="7" class="text-center text-muted">Tidak ada SPK ditemukan</td>
      </tr>
    @endforelse
      </tbody>
    </table>
    </div>
  </div>
@endsection