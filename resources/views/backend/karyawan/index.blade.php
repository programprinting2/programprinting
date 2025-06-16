@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Master</a></li>
    <li class="breadcrumb-item active" aria-current="page">Karyawan</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div>
          <div class="row">
            <div class="col-md-6">
              <h6 class="card-title mb-0">Master Karyawan</h6>
              <p class="text-muted">Kelola data karyawan untuk kebutuhan penggajian, jadwal kerja, dan kehadiran.</p>
            </div>
            <div class="col-md-6 text-right">
              <button type="button" class="btn btn-primary float-end d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalKaryawan">
                <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Karyawan
              </button>
            </div>
          </div>
        </div>

        <!-- Divider -->
        <hr class="my-4">

        <!-- Form Pencarian dan Filter -->
        <form id="searchForm" class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i data-feather="search" class="icon-sm"></i>
              </span>
              <input type="text" class="form-control" id="search" name="search" placeholder="Cari id, nama, posisi, atau departemen..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="status_filter" name="status">
              <option value="">Semua Status</option>
              <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
              <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-outline-secondary w-100" id="resetFilter">
              <i data-feather="refresh-cw" class="icon-sm me-1"></i> Reset
            </button>
          </div>
        </form>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center d-none">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID Karyawan</th>
                <th>Nama Lengkap</th>
                <th>Posisi</th>
                <th>Departemen</th>
                <th>Tanggal Masuk</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($karyawan as $k)
              <tr>
                <td>{{ $k->id_karyawan }}</td>
                <td>{{ $k->nama_lengkap }}</td>
                <td>{{ $k->posisi }}</td>
                <td>{{ $k->departemen }}</td>
                <td>{{ $k->tanggal_masuk->format('d/m/Y') }}</td>
                <td>
                  <span class="badge {{ $k->status == 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                    {{ $k->status }}
                  </span>
                </td>
                <td>
                  <div class="btn-group gap-1" role="group">
                    <button type="button" class="btn btn-warning btn-xs rounded" onclick="loadKaryawanData({{ $k->id }})">
                      <i class="link-icon icon-sm" data-feather="edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs rounded btn-delete-karyawan" 
                            data-id="{{ $k->id }}" 
                            data-nama="{{ $k->nama_lengkap }}">
                      <i class="link-icon icon-sm" data-feather="trash"></i>
                    </button>
                    <form id="formDeleteKaryawan{{ $k->id }}" 
                          action="{{ route('backend.karyawan.destroy', $k->id) }}" 
                          method="POST" style="display: none;">
                      @csrf
                      @method('DELETE')
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-4">
                  @if(request('search') || request('status'))
                    <div class="text-muted">
                      <i data-feather="search" class="icon-sm mb-2"></i>
                      <p class="mb-0">Tidak ditemukan data karyawan yang sesuai dengan kriteria pencarian.</p>
                      <button type="button" class="btn btn-link btn-sm p-0 mt-2" id="clearSearch">
                        <i data-feather="x" class="icon-sm"></i> Hapus filter
                      </button>
                    </div>
                  @else
                    <div class="text-muted">
                      <i data-feather="users" class="icon-sm mb-2"></i>
                      <p class="mb-0">Belum ada data karyawan.</p>
                    </div>
                  @endif
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between mt-4">
          <p class="text-muted">
            @if($karyawan->total() > 0)
              Menampilkan {{ $karyawan->firstItem() ?? 0 }} - {{ $karyawan->lastItem() ?? 0 }} dari {{ $karyawan->total() }} karyawan
            @else
              Tidak ada data karyawan
            @endif
          </p>
          {{ $karyawan->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

@include('backend.karyawan.modal_form')
@include('backend.karyawan.modal_edit')

@push('custom-scripts')
<script>
  // Event listener untuk form pencarian
  $('#searchForm').on('submit', function(e) {
    e.preventDefault();
    showLoadingSpinner();
    window.location.href = '{{ route("backend.karyawan.index") }}?' + $(this).serialize();
  });

  // Event listener untuk filter status
  $('#status_filter').on('change', function() {
    showLoadingSpinner();
    window.location.href = '{{ route("backend.karyawan.index") }}?' + $('#searchForm').serialize();
  });

  // Event listener untuk reset filter
  $('#resetFilter, #clearSearch').on('click', function() {
    showLoadingSpinner();
    window.location.href = '{{ route("backend.karyawan.index") }}';
  });

  // Fungsi untuk menampilkan loading spinner
  function showLoadingSpinner() {
    $('#loadingSpinner').removeClass('d-none');
    $('.table-responsive').addClass('d-none');
  }

  // Fungsi untuk menyembunyikan loading spinner
  function hideLoadingSpinner() {
    $('#loadingSpinner').addClass('d-none');
    $('.table-responsive').removeClass('d-none');
  }

  // Sembunyikan loading spinner saat halaman selesai dimuat
  $(window).on('load', function() {
    hideLoadingSpinner();
  });

  // Inisialisasi Feather Icons
  feather.replace();
</script>
@endpush

@endsection 