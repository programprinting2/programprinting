@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Master</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pelanggan</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="card-title mb-0">Master Pelanggan</h6>
                                <p class="text-muted">Kelola data pelanggan untuk kebutuhan penjualan, pengiriman, dan pembayaran.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary float-end d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#tambahPelanggan">
                                    <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Pelanggan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- Search and Filter Form -->
                    <form id="searchForm" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i data-feather="search" class="icon-sm"></i>
                                </span>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Cari id, nama, atau kontak..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="status_filter" name="status">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
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
                                    <th>ID Pelanggan</th>
                                    <th>Nama</th>
                                    <th>Kontak</th>
                                    <th>Kategori</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggan as $p)
                                    <tr>
                                        <td>{{ $p->kode_pelanggan }}</td>
                                        <td>{{ $p->nama }}</td>
                                        <td>
                                            <div>
                                                {{ $p->no_telp }}<br>
                                                <small class="text-muted">{{ $p->email }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $p->kategori_harga }}</td>
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
                                                    onclick="editPelanggan({{ $p->id }})">
                                                    <i class="link-icon icon-sm" data-feather="edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs rounded btn-delete-pelanggan"
                                                    data-id="{{ $p->id }}" data-nama="{{ $p->nama }}">
                                                    <i class="link-icon icon-sm" data-feather="trash"></i>
                                                </button>
                                                <form id="formDeletePelanggan{{ $p->id }}"
                                                    action="{{ route('backend.pelanggan.destroy', $p->id) }}" method="POST"
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
                                            @if(request('search') || request('status'))
                                                <p class="text-muted mb-0">Tidak ada data pelanggan yang ditemukan.</p>
                                                <button type="button" class="btn btn-link btn-sm" id="clearFilter">
                                                    <i data-feather="x" class="icon-sm me-1"></i> Hapus Filter
                                                </button>
                                            @else
                                                <p class="text-muted mb-0">Belum ada data pelanggan.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between mt-4">
                        <p class="text-muted">Menampilkan {{ $pelanggan->firstItem() ?? 0 }} - {{ $pelanggan->lastItem() ?? 0 }} dari {{ $pelanggan->total() }} pelanggan.</p>
                        {{ $pelanggan->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('backend.pelanggan.modal_form')    
@include('backend.pelanggan.modal_edit')
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('js/pelanggan/pelanggan-edit-modal.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Initialize Feather Icons
            feather.replace();

            // Search and Filter Functionality
            const searchForm = $('#searchForm');
            const loadingSpinner = $('#loadingSpinner');
            const tableContainer = $('.table-responsive');

            // Handle form submission
            searchForm.on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                window.location.href = `${window.location.pathname}?${formData}`;
            });

            // Handle status filter change
            $('#status_filter').on('change', function() {
                searchForm.submit();
            });

            // Handle reset filter
            $('#resetFilter, #clearFilter').on('click', function() {
                window.location.href = window.location.pathname;
            });

            // Show loading spinner when form is submitted
            searchForm.on('submit', function() {
                loadingSpinner.removeClass('d-none');
                tableContainer.addClass('d-none');
            });

            // Hide loading spinner when page is loaded
            $(window).on('load', function() {
                loadingSpinner.addClass('d-none');
                tableContainer.removeClass('d-none');
            });

            // Delete confirmation
            $('.btn-delete-pelanggan').click(function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                
                Swal.fire({
                    title: 'Hapus Data Pelanggan?',
                    text: `Anda akan menghapus data pelanggan "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#formDeletePelanggan${id}`).submit();
                    }
                });
            });
        });

        function editPelanggan(id) {
            // Tampilkan loading state
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Ambil data pelanggan
            $.ajax({
                url: `/backend/pelanggan/${id}`,
                method: "GET",
                success: function (response) {
                    // Tutup loading state
                    Swal.close();
                    
                    if (response.success) {
                        // Isi form dengan data
                        window.fillFormData(response.data);
                        
                        // Tampilkan modal
                        $("#modalEditPelanggan").modal("show");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message || "Gagal memuat data pelanggan",
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Gagal memuat data pelanggan. Silakan coba lagi.",
                    });
                }
            });
        }
    </script>
@endpush