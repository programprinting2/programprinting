@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Master</a></li>
            <li class="breadcrumb-item active" aria-current="page">Produk</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="card-title mb-0">Master Produk</h6>
                                <p class="text-muted">Kelola informasi produk untuk keperluan penjualan</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary float-end d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#tambahProduk">
                                    <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Produk
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- Search and Filter Form -->
                    <form id="searchForm" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i data-feather="search" class="icon-sm"></i>
                                    </span>
                                    <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan kode, nama produk..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('backend.master-produk.index') }}" class="btn btn-outline-secondary w-100">
                                    <i data-feather="refresh-cw" class="icon-sm me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Sub Kategori</th>
                                    <th>Satuan</th>
                                    <th>Lebar (cm)</th>
                                    <th>Panjang (cm)</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produk as $p)
                                    <tr>
                                        <td>{{ $p->kode_produk }}</td>
                                        <td>{{ $p->nama_produk }}</td>
                                        <td>{{ $p->kategoriUtama->nama_detail_parameter ?? '-' }}</td>
                                        <td>{{ $p->subKategori->nama_sub_detail_parameter ?? '-' }}</td>
                                        <td>{{ $p->subSatuan->nama_sub_detail_parameter ?? '-' }}</td>
                                        <td>{{ $p->lebar }}</td>
                                        <td>{{ $p->panjang }}</td>
                                        <td>
                                            <span class="badge {{ $p->status_aktif ? 'bg-success' : 'bg-danger' }}">
                                                {{ $p->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-xs rounded btn-edit-produk" data-id="{{ $p->id }}">
                                                    <i class="link-icon icon-sm" data-feather="edit"></i>
                                                </button>
                                            <button type="button" class="btn btn-danger btn-xs rounded btn-delete-produk" data-id="{{ $p->id }}" data-nama="{{ $p->nama_produk }}">
                                                    <i class="link-icon icon-sm" data-feather="trash"></i>
                                                </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            @if(request()->has('search') || request()->has('status'))
                                                Tidak ada data produk yang ditemukan dengan filter yang dipilih.
                                                <br>
                                                <a href="{{ route('backend.master-produk.index') }}" class="btn btn-sm btn-primary mt-2">
                                                    <i data-feather="refresh-cw"></i> Reset Filter
                                                </a>
                                            @else
                                                Belum ada data produk yang tersedia.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="text-muted">
                                Menampilkan {{ $produk->firstItem() ?? 0 }} - {{ $produk->lastItem() ?? 0 }} dari {{ $produk->total() }} data produk
                            </p>
                            <div>
                                {{ $produk->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('backend.master-produk.modal_form')    
@include('backend.master-produk.modal_edit')
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
    window.subKategoriList = @json($subKategoriList ?? []);
    window.kategoriList = @json($kategoriList ?? []);
    window.masterMesinList = @json($masterMesinList ?? []);
    window.satuanDetailList = @json($satuanDetailList ?? []);
  </script>
  <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/master-produk/produk-helper.js') }}"></script>
  <script src="{{ asset('js/master-produk/edit-modal.js') }}"></script>
  <script src="{{ asset('js/master-produk/add-modal.js') }}"></script>
  <script>
    $(document).on('click', '.btn-delete-produk', function(e) {
      e.preventDefault();
      let id = $(this).data('id');
      let nama = $(this).data('nama');
      let deleteUrl = `/backend/master-produk/${id}`;
      Swal.fire({
        title: 'Hapus Data Produk?',
        text: `Anda akan menghapus data produk "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: deleteUrl,
            type: 'POST',
            data: {
              _method: 'DELETE',
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: response.message,
                  showConfirmButton: false,
                  timer: 1500
                }).then(() => {
                  $(`tr:has(button[data-id="${id}"])`).fadeOut(300, function() {
                    $(this).remove();
                  });
                });
              } else {
                Swal.fire(
                  'Gagal!',
                  response.message || 'Terjadi kesalahan saat menghapus data',
                  'error'
                );
              }
            },
            error: function(xhr) {
              Swal.fire(
                'Error!',
                xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data',
                'error'
              );
            }
          });
        }
      });
    });
  </script>
@endpush
