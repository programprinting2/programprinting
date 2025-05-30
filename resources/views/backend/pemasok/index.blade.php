@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Master</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pemasok</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="card-title">Data Pemasok</h6>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary float-end d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#tambahPemasok">
                                    <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Pemasok
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Pemasok</th>
                                    <th>Nama</th>
                                    <th>Kontak</th>
                                    <th>Kategori</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pemasok as $p)
                                    <tr>
                                        <td>{{ $p->kode_pemasok }}</td>
                                        <td>{{ $p->nama }}</td>
                                        <td>
                                            <div>
                                                {{ $p->no_telp }}<br>
                                                <small class="text-muted">{{ $p->email }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $p->kategori }}</td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between mt-4">
                        <p class="text-muted">Menampilkan {{ count($pemasok) }} pemasok.</p>
                        {{ $pemasok->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('backend.pemasok.modal_form')    
@include('backend.pemasok.modal_edit')
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('js/pemasok/pemasok-edit-modal.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Initialize Feather Icons
            feather.replace();

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
    </script>
@endpush