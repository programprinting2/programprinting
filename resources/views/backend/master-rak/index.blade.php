@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Master</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gudang</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('backend.master-gudang.index') }}" class="text-decoration-none text-dark card-link @if(request()->routeIs('backend.master-gudang.index')) active-card @endif">
                <div class="card h-100 card-hover @if(request()->routeIs('backend.master-gudang.index')) border-primary shadow active-card @endif" style="transition: box-shadow .2s, border-color .2s; cursor:pointer;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <i data-feather="archive" class="mb-2" style="width:32px;height:32px;"></i>
                        <div class="fw-bold">Gudang</div>
                        <div>{{ $totalGudang }} gudang</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('backend.master-rak.index') }}" class="text-decoration-none text-dark card-link @if(request()->routeIs('backend.master-rak.index')) active-card @endif">
                <div class="card h-100 card-hover @if(request()->routeIs('backend.master-rak.index')) border-primary shadow active-card @endif" style="transition: box-shadow .2s, border-color .2s; cursor:pointer;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <i data-feather="box" class="mb-2" style="width:32px;height:32px;"></i>
                        <div class="fw-bold">Rak</div>
                        <div>{{ $rak->total() }} rak</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="card-title mb-0">Master Rak</h6>
                    <p class="text-muted">Manajemen rak penyimpanan</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#tambahRak">
                        <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Rak
                    </button>
                </div>
            </div>
            <form class="row g-3 mb-3" method="GET" action="{{ route('backend.master-rak.index') }}">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Cari rak..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('backend.master-rak.index') }}" class="btn btn-outline-secondary w-100">
                        <i data-feather="refresh-cw" class="icon-sm me-1"></i> Reset
                    </a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Rak</th>
                            <th>Nama Rak</th>
                            <th>Gudang</th>
                            <th>Kapasitas</th>
                            <th>Level</th>
                            <th>Dimensi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rak as $item)
                            <tr>
                                <td>{{ $item->kode_rak }}</td>
                                <td>{{ $item->nama_rak }}</td>
                                <td>
                                    <div>
                                        {{ $item->gudang->nama_gudang ?? '-' }}<br>
                                        @if($item->gudang)
                                            <small class="text-muted">{{ $item->gudang->alamat }}, {{ $item->gudang->kota }}, {{ $item->gudang->provinsi }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <b>{{ $item->kapasitas }}</b><br>
                                </td>
                                <td>{{ $item->jumlah_level }}</td>
                                <td>{{ $item->lebar ?? 0 }}m × {{ $item->kedalaman ?? 0 }}m × {{ $item->tinggi ?? 0 }}m</td>
                                <td>
                                    <span class="badge {{ $item->status == 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group gap-1" role="group">
                                        <button type="button" class="btn btn-warning btn-xs rounded" title="Edit" 
                                            onclick="editRak({{ $item->id }})"
                                            data-rak='@json($item)'>
                                            <i class="link-icon icon-sm" data-feather="edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs rounded btn-delete-rak" 
                                                data-id="{{ $item->id }}" 
                                                data-nama="{{ $item->nama_rak }}" 
                                                title="Hapus">
                                            <i class="link-icon icon-sm" data-feather="trash"></i>
                                        </button>
                                        <form id="formDeleteRak{{ $item->id }}" 
                                              action="{{ route('backend.master-rak.destroy', $item->id) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data rak.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="text-muted">
                        Menampilkan {{ $rak->firstItem() ?? 0 }} - {{ $rak->lastItem() ?? 0 }} dari {{ $rak->total() }} rak
                    </p>
                    <div>
                        {{ $rak->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.master-rak.modal_form')
    @include('backend.master-rak.modal_edit')
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/master-rak/edit-modal.js') }}"></script>
    <script src="{{ asset('js/master-rak/add-modal.js') }}"></script>
    <script>
        feather.replace();
        // Delete confirmation
        $('.btn-delete-rak').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Data Rak?',
                text: `Anda akan menghapus data rak "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#formDeleteRak${id}`).submit();
                }
            });
        });
    </script>
    <style>
        .card-hover:hover {
            box-shadow: 0 0 0 2px #0d6efd33, 0 4px 24px 0 #00000022;
            border-color: #0d6efd;
        }
        .active-card {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 2px #0d6efd33, 0 4px 24px 0 #00000022 !important;
        }
        .card-link.active-card .card {
            border-color: #0d6efd !important;
        }
    </style>
@endpush 