@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" /> -->
@endpush

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pembelian</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="row">
          <h6 class="card-title mb-0">Data Pembelian</h6>
          <p class="text-muted mb-3">Kelola data pembelian bahan baku dan barang lainnya</p>
        </div>
        <div class="d-flex align-items-center gap-3">
        <a href="{{ route('pembelian.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
          <i class="fa fa-plus"></i> Tambah Pembelian
        </a>
        </div>
      </div>

      <!-- Divider -->
      <hr class="my-4">

      <!-- Form Pencarian dan Filter -->
      <form id="searchForm" class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label small">&nbsp;</label>
        <div class="input-group">
            <span class="input-group-text bg-light">
              <i data-feather="search" class="icon-sm"></i>
            </span>
            <input type="text" class="form-control" name="search" placeholder="Cari kode pembelian, pemasok, atau nomor form..." value="{{ request('search') }}">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label small">&nbsp;</label>
          <select class="form-select" name="pemasok_id">
            <option value="">Semua Pemasok</option>
            @foreach($pemasok_list as $pemasok)
              <option value="{{ $pemasok->id }}" {{ request('pemasok_id') == $pemasok->id ? 'selected' : '' }}>
                {{ $pemasok->nama }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Tanggal Dari</label>
          <input type="date" class="form-control" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Tanggal Sampai</label>
          <input type="date" class="form-control" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
        </div>
        <div class="col-md-1">
          <label class="form-label small">&nbsp;</label>
          <button type="button" class="btn btn-outline-secondary w-100" id="resetFilter" title="Reset Filter">
            <i data-feather="refresh-cw" class="icon-sm"></i> Reset
        </button>
        </div>
      </form>

      <!-- Loading Spinner -->
      <div id="loadingSpinner" class="text-center py-4 d-none">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
        <thead>
          <tr>
          <th>Kode Pembelian</th>
          <th>Tanggal Pembelian</th>
          <th>Pemasok</th>
          <th>Total</th>
          <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($data_pembelian as $i => $item)
            <tr>
              <td>{{ $item->kode_pembelian }}</td>
              <td>
                {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->locale('id')->translatedFormat('d F Y') }}
                @if($item->jatuh_tempo)
                  <br><span class="text-muted small">Jatuh Tempo: {{ \Carbon\Carbon::parse($item->jatuh_tempo)->locale('id')->translatedFormat('d F Y') }}</span>
                @else
                  <br><span class="text-muted small">Jatuh Tempo: -</span>
                @endif
              </td>
              <td>
                {{ $item->pemasok->nama ?? '-' }}
                @if($item->pemasok && $item->pemasok->alamat && is_array($item->pemasok->alamat) && isset($item->pemasok->alamat[$item->pemasok->alamat_utama]))
                  <br><small class="text-muted">{{ $item->pemasok->alamat[$item->pemasok->alamat_utama]['alamat'] }}
                  @if($item->pemasok->alamat[$item->pemasok->alamat_utama]['kota'])
                    , {{ $item->pemasok->alamat[$item->pemasok->alamat_utama]['kota'] }}
                  @endif
                  </small>
                @endif
              </td>
              <td class="fw-semibold">Rp {{ number_format($item->total,0,',','.') }}</td>
              <td>
                <div class="btn-group gap-1" role="group">
                  <a href="{{ route('pembelian.show', $item->kode_pembelian) }}" class="btn btn-primary btn-xs btn-icon rounded" title="Detail"><i class="link-icon icon-sm" data-feather="eye"></i></a>
                  <a href="{{ route('pembelian.edit', $item->kode_pembelian) }}" class="btn btn-warning btn-xs btn-icon rounded" title="Edit"><i class="link-icon icon-sm" data-feather="edit"></i></a>
                  <form action="{{ route('pembelian.destroy', $item->kode_pembelian) }}" method="POST" class="d-inline-block form-hapus-pembelian">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-xs btn-icon rounded btn-hapus-pembelian" title="Hapus"><i class="link-icon icon-sm" data-feather="trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">
                @if(request('search') || request('pemasok_id') || request('tanggal_dari') || request('tanggal_sampai'))
                  <div class="text-muted">
                    <i data-feather="search" class="icon-sm mb-2"></i>
                    <p class="mb-0">Tidak ditemukan data pembelian yang sesuai dengan kriteria pencarian.</p>
                    <button type="button" class="btn btn-link btn-sm p-0 mt-2" id="clearSearch">
                      <i data-feather="x" class="icon-sm"></i> Hapus filter
                    </button>
                  </div>
                @else
                  <div class="text-muted">
                    <i data-feather="shopping-cart" class="icon-sm mb-2"></i>
                    <p class="mb-0">Belum ada data pembelian.</p>
                  </div>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>
            @if($data_pembelian->total() > 0)
              Menampilkan {{ $data_pembelian->firstItem() ?? 0 }} - {{ $data_pembelian->lastItem() ?? 0 }} dari {{ $data_pembelian->total() }} data pembelian
            @else
              Tidak ada data pembelian
            @endif
          </div>
          <div>
            {{ $data_pembelian->appends(request()->query())->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
  // Initialize Feather Icons
  feather.replace();

  // Search form handling
  const searchForm = $('#searchForm');
  const loadingSpinner = $('#loadingSpinner');
  const tableContainer = $('.table-responsive');

  // Auto-submit form when dropdown or date changes
  $('select[name="pemasok_id"], input[name="tanggal_dari"], input[name="tanggal_sampai"]').on('change', function() {
    searchForm.submit();
  });

  // Manual submit for search input
  $('input[name="search"]').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
      e.preventDefault();
      searchForm.submit();
    }
  });

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

  // Reset filter
  $('#resetFilter').click(function() {
    window.location.href = '{{ route("pembelian.index") }}';
  });

  // Clear search
  $('#clearSearch').click(function() {
    window.location.href = '{{ route("pembelian.index") }}';
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

  @if(session('success'))
    Swal.fire({
      title: 'Berhasil!',
      text: '{{ session('success') }}',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    });
  @endif

  document.querySelectorAll('.btn-hapus-pembelian').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Hapus Data?',
        text: 'Data pembelian yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          btn.closest('form').submit();
        }
      });
    });
    });
  </script>
@endpush