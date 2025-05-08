@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
@endpush

@section('content')

<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Tables</a></li>
    <li class="breadcrumb-item active" aria-current="page">Grid View</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Data Master Mesin - Grid View</h6>
        <p class="text-muted mb-3">Kelola inventaris mesin dan jadwal pemeliharaan</p>
        <div class="d-flex flex-wrap gap-3">
          @foreach ($data_mesin as $mesin)
          <div class="card border" style="width: 300px;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">{{ $mesin->nama_mesin }}</h5>
                <span class="badge bg-{{ $mesin->aktif ? 'success' : 'danger' }}">
                  {{ $mesin->aktif ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <p class="mb-1"><strong>Jenis Mesin:</strong> {{ $mesin->jenis_mesin }}</p>
              <p class="mb-1"><strong>Jenis Produksi:</strong> {{ $mesin->non_produksi ? 'Produksi' : 'Non Produksi' }}</p>
              <p class="mb-1"><strong>Tanggal Beli:</strong> {{ \Carbon\Carbon::parse($mesin->tanggal_beli)->format('d M Y') }}</p>
              <p class="mb-3"><strong>Keterangan:</strong> {{ $mesin->keterangan ?? 'Tidak ada keterangan' }}</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editDataModal{{ $mesin->id }}">
                  <i class="fa fa-edit"></i> Edit
                </button>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDataModal{{ $mesin->id }}">
                  <i class="fa fa-trash"></i> Delete
                </button>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
