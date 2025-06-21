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
                                <a href="{{ route('backend.pemasok.index') }}" class="btn btn-outline-secondary w-100">
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
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Sub Kategori</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @forelse ($pemasok as $p)
                                    <tr>
                                        <td>{{ $p->kode_pemasok }}</td>
                                        <td>{{ $p->nama }}</td>
                                        <td>
                                            <div>
                                                {{ $p->no_telp }}<br>
                                                <small class="text-muted">{{ $p->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($p->alamat && is_array($p->alamat) && isset($p->alamat[$p->alamat_utama]))
                                                {{ $p->alamat[$p->alamat_utama]['alamat'] }}
                                                @if($p->alamat[$p->alamat_utama]['kota'])
                                                    <br><small class="text-muted">{{ $p->alamat[$p->alamat_utama]['kota'] }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $p->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $p->status ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group gap-1" role="group">
                                                <button type="button" class="btn btn-warning btn-xs rounded"
                                                    onclick="editPemasok({{ $p->id }})">
                                                    <i class="link-icon icon-sm" data-feather="edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs rounded btn-delete-pemasok"
                                                    data-id="{{ $p->id }}" data-nama="{{ $p->nama }}">
                                                    <i class="link-icon icon-sm" data-feather="trash"></i>
                                                </button>
                                                <form id="formDeletePemasok{{ $p->id }}"
                                                    action="{{ route('backend.pemasok.destroy', $p->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            @if(request()->has('search') || request()->has('status'))
                                                Tidak ada data pemasok yang ditemukan dengan filter yang dipilih.
                                                <br>
                                                <a href="{{ route('backend.pemasok.index') }}" class="btn btn-sm btn-primary mt-2">
                                                    <i data-feather="refresh-cw"></i> Reset Filter
                                                </a>
                                            @else
                                                Belum ada data pemasok yang tersedia.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse --}}
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            {{-- <div>
                                Menampilkan {{ $pemasok->firstItem() ?? 0 }} - {{ $pemasok->lastItem() ?? 0 }} dari {{ $pemasok->total() }} data pemasok
                            </div>
                            <div>
                                {{ $pemasok->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div> --}}
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
    {{-- <script src="{{ asset('js/pemasok/pemasok-edit-modal.js') }}"></script> --}}
    {{-- <script>
        $(document).ready(function () {
            // Initialize Feather Icons
            feather.replace();

            // Search form handling
            const searchForm = $('#searchForm');
            const loadingSpinner = $('#loadingSpinner');
            const tableContainer = $('.table-responsive');

            searchForm.on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const url = `${window.location.pathname}?${formData}`;
                
                // Show loading spinner
                loadingSpinner.removeClass('d-none');
                tableContainer.addClass('d-none');
                
                // Redirect to search URL
                window.location.href = url;
            });

            // Show loading spinner when page is loading
            $(window).on('beforeunload', function() {
                loadingSpinner.removeClass('d-none');
                tableContainer.addClass('d-none');
            });

            // Hide loading spinner when page is fully loaded
            $(window).on('load', function() {
                loadingSpinner.addClass('d-none');
                tableContainer.removeClass('d-none');
            });

            // Delete confirmation
            $('.btn-delete-pemasok').click(function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                
                Swal.fire({
                    title: 'Hapus Data Pemasok?',
                    text: `Anda akan menghapus data pemasok "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#formDeletePemasok${id}`).submit();
                    }
                });
            });
        });

        function editPemasok(id) {
            // Tampilkan loading state
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Ambil data pemasok
            $.ajax({
                url: `/backend/pemasok/${id}`,
                method: "GET",
                success: function (response) {
                    // Tutup loading state
                    Swal.close();
                    
                    if (response.success) {
                        // Isi form dengan data
                        window.fillFormData(response.data);
                        
                        // Tampilkan modal
                        $("#modalEditPemasok").modal("show");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message || "Gagal memuat data pemasok",
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Gagal memuat data pemasok. Silakan coba lagi.",
                    });
                }
            });
        }
    </script>  --}}
@endpush