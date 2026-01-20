@extends('layout.master')

@push('plugin-styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
@endpush

@section('content')

<style>
  .modal-body-scrollable {
    max-height: 70vh; /* Adjust height to fit content */
    overflow-y: auto; /* Enable scrolling if content overflows */
  }
  .tab-content > .tab-pane {
    height: 100%; /* Ensure consistent height for tab content */
  }
  .modal-footer-sticky {
    position: sticky;
    bottom: 0;
    background-color: #fff;
    z-index: 10;
    padding: 15px;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
  }
</style>

<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Master</a></li>
    <li class="breadcrumb-item active" aria-current="page">Bahan Baku</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div>
          <div class="row">
            <div class="col-md-6">
              <h6 class="card-title mb-0">Master Bahan Baku</h6>
              <p class="text-muted">Kelola data bahan baku untuk kebutuhan produksi dan persediaan.</p>
            </div>
            <div class="col-md-6 text-right">
              <button type="button" class="btn btn-primary float-end d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Bahan Baku
              </button>
            </div>
          </div>
        </div>

        <!-- Divider -->
        <hr class="my-4">

        <!-- Form Pencarian dan Filter -->
        <form id="searchForm" class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i data-feather="search" class="icon-sm"></i>
              </span>
              <input type="text" class="form-control" id="search" name="search" placeholder="Cari kode, nama, atau kategori..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="kategori_filter" name="kategori">
              <option value="">Semua Kategori</option>
              @foreach($kategoriList as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_detail_parameter }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="sub_kategori_filter" name="sub_kategori" disabled>
              <option value="">Semua Sub-Kategori</option>
              <!-- Opsi akan diisi dinamis oleh JS, value=id -->
            </select>
          </div>
          <div class="col-md-2">
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
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Kategori</th>
                <th>Sub-Kategori</th>
                <th>Satuan Utama</th>
                <th>Stok Saat Ini</th>
                <th>Harga Terakhir</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($bahanbaku as $b)
              <tr>
                <td>{{ $b->kode_bahan }}</td>
                <td>
                    <div>
                        {{ $b->nama_bahan }}<br>
                        <small class="text-muted">{{ $b->keterangan }}</small>
                    </div>
                </td>
                <td>{{ $b->kategoriDetail ? $b->kategoriDetail->nama_detail_parameter : '-' }}</td>
                <td>{{ $b->subKategoriDetail ? $b->subKategoriDetail->nama_sub_detail_parameter : '-' }}</td>
                <td>{{ $b->subSatuanDetail ? $b->subSatuanDetail->nama_sub_detail_parameter : '-' }}</td>
                <td>{{ $b->stok_saat_ini }}</td>
                <td>
                    <div>
                        Rp {{ number_format($b->harga_terakhir, 0, ',', '.') }}<br>
                        <small class="text-muted">{{ $b->pemasokUtama ? $b->pemasokUtama->nama : 'Tidak Ada Pemasok' }}</small>
                    </div>
                </td>
                <td>
                  <span class="badge {{ $b->status_aktif ? 'bg-success' : 'bg-danger' }}">
                    {{ $b->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group gap-1" role="group">
                    <button type="button" class="btn btn-warning btn-xs rounded" onclick="loadBahanBakuData({{ $b->id }})">
                      <i class="link-icon icon-sm" data-feather="edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs rounded btn-delete-bahanbaku" 
                            data-id="{{ $b->id }}" 
                            data-nama="{{ $b->nama_bahan }}">
                      <i class="link-icon icon-sm" data-feather="trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-4">
                  @if(request('search') || request('kategori'))
                    <div class="text-muted">
                      <i data-feather="search" class="icon-sm mb-2"></i>
                      <p class="mb-0">Tidak ditemukan data bahan baku yang sesuai dengan kriteria pencarian.</p>
                      <button type="button" class="btn btn-link btn-sm p-0 mt-2" id="clearSearch">
                        <i data-feather="x" class="icon-sm"></i> Hapus filter
                      </button>
                    </div>
                  @else
                    <div class="text-muted">
                      <i data-feather="package" class="icon-sm mb-2"></i>
                      <p class="mb-0">Belum ada data bahan baku.</p>
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
            @if($bahanbaku->total() > 0)
              Menampilkan {{ $bahanbaku->firstItem() ?? 0 }} - {{ $bahanbaku->lastItem() ?? 0 }} dari {{ $bahanbaku->total() }} bahan baku
            @else
              Tidak ada data bahan baku
            @endif
          </p>
          {{ $bahanbaku->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

@include('backend.master-bahanbaku.form_modal')
@include('backend.master-bahanbaku.edit_modal')

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/bahanbaku/bahanbaku-helper.js') }}"></script>
  <script>
    // window.kategoriList = @json($kategoriList);
    window.subKategoriList = @json($subKategoriList);
    window.satuanList = @json($satuanList);
    window.subSatuanList = @json($subSatuanList ?? []);
    function updateSubKategoriFilterOptions(selectedKategoriId, selectedSubKategori = null) {
      const subKategoriSelect = $('#sub_kategori_filter');
      subKategoriSelect.empty();
      subKategoriSelect.append('<option value="">Semua Sub-Kategori</option>');
      subKategoriSelect.prop('disabled', true);
      if (selectedKategoriId && window.subKategoriList) {
        const filtered = window.subKategoriList.filter(sub => sub.detail_parameter_id == selectedKategoriId);
        filtered.forEach(sub => {
          subKategoriSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
        });
        if (filtered.length > 0) subKategoriSelect.prop('disabled', false);
      }
      // Set nilai terpilih jika ada
      if (selectedSubKategori) {
        subKategoriSelect.val(selectedSubKategori);
      }
    }
    // Fungsi untuk menampilkan produk yang menggunakan bahan baku
    function showProdukInfo(bahanBakuId, modalType = 'add') {
        $.ajax({
            url: `{{ url('backend/master-bahanbaku') }}/${bahanBakuId}/produk`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let produkHtml = '';
                    
                    if (response.produk.length === 0) {
                        produkHtml = '<p class="text-muted mb-0">Belum ada produk yang menggunakan bahan baku ini.</p>';
                    } else {
                        produkHtml = `
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        response.produk.forEach(function(produk) {
                            produkHtml += `
                                <tr>
                                    <td>${produk.kode}</td>
                                    <td>${produk.nama}</td>
                                    <td>${produk.kategori}</td>
                                    <td>${produk.jumlah}</td>
                                    <td>${produk.satuan}</td>
                                </tr>
                            `;
                        });
                        
                        produkHtml += `
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2 text-muted small">
                                Total produk: ${response.total}
                            </div>
                        `;
                    }
                    
                    Swal.fire({
                        title: `Produk Menggunakan ${response.nama_bahan_baku}`,
                        html: produkHtml,
                        width: '800px',
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat data produk'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data produk'
                });
            }
        });
    }

    // Event handler untuk modal tambah bahan baku
    $(document).on('click', '#info-harga-terakhir', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Untuk modal tambah, kita perlu mendapatkan ID dari input hidden atau logic lain
        // Karena ini modal tambah, mungkin perlu handle berbeda atau disable sementara
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: 'Fitur ini hanya tersedia untuk bahan baku yang sudah tersimpan'
        });
    });

    // Event handler untuk modal edit bahan baku
    $(document).on('click', '#edit-info-harga-terakhir', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const bahanBakuId = $('#edit_id').val(); // Pastikan ada input hidden dengan ID
        if (bahanBakuId) {
            showProdukInfo(bahanBakuId, 'edit');
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'ID bahan baku tidak ditemukan'
            });
        }
    });

    // Initialize tooltips
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
    $(document).ready(function() {
      feather.replace();
      // Inisialisasi format mata uang
      BahanBakuHelper.initMoneyFormat();
      // Persiapan form submit
      BahanBakuHelper.prepareFormSubmit('#formTambahBahanBaku');
      BahanBakuHelper.prepareFormSubmit('#formEditBahanBaku');
      // Event listener untuk tombol reset filter
      $('#resetFilter').on('click', function() {
        window.location.href = '{{ route("backend.master-bahanbaku.index") }}';
      });
      // Event listener untuk tombol clearSearch (jika ada hasil kosong)
      $('#clearSearch').on('click', function() {
        window.location.href = '{{ route("backend.master-bahanbaku.index") }}';
      });
      // Submit form filter saat ada perubahan input search atau kategori_filter
      $('#search').on('keypress', function(e) {
        if (e.which == 13) { // Enter key pressed
          $('#searchForm').submit();
        }
      });
      $('#kategori_filter').on('change', function() {
        const selectedKategori = $(this).val();
        updateSubKategoriFilterOptions(selectedKategori, null);
        // Reset sub-kategori saat kategori berubah
        $('#sub_kategori_filter').val('');
        $('#searchForm').submit();
      });
      // Inisialisasi sub-kategori filter saat halaman dimuat
      updateSubKategoriFilterOptions($('#kategori_filter').val(), '{{ request('sub_kategori') }}');
      // Submit form saat sub-kategori filter berubah
      $('#sub_kategori_filter').on('change', function() {
        $('#searchForm').submit();
      });
      // Delete confirmation
      $('.btn-delete-bahanbaku').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let deleteUrl = `{{ url('backend/master-bahanbaku') }}/${id}`;
        
        Swal.fire({
          title: 'Hapus Data Bahan Baku?',
          text: `Anda akan menghapus data bahan baku "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
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
                    // Hapus baris dari tabel
                    $(`tr:has(button[data-id="${id}"])`).fadeOut(300, function() {
                      $(this).remove();
                      // Update informasi pagination jika perlu
                      updatePaginationInfo();
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

      // Fungsi untuk memperbarui informasi pagination
      function updatePaginationInfo() {
        const totalItems = parseInt($('.pagination-info').text().match(/\d+/)[0]) - 1;
        $('.pagination-info').text(`Menampilkan ${totalItems} bahan baku`);
      }
    });
  </script>
@endpush 