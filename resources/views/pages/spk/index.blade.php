@extends('layout.master')

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
      <li class="breadcrumb-item active" aria-current="page">SPK</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="row">
              <h6 class="card-title mb-0">Data SPK</h6>
              <p class="text-muted mb-3">Kelola Surat Perintah Kerja untuk pelanggan</p>
    </div>
            <div class="d-flex align-items-center gap-3">
              <a href="{{ route('spk.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="fa fa-plus"></i> Buat SPK Baru
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
                <input type="text" class="form-control" name="search" placeholder="Cari nomor SPK, pelanggan, atau status..." value="{{ request('search') }}">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label small">&nbsp;</label>
              <select class="form-select" name="customer_id">
                <option value="">Semua Pelanggan</option>
                @foreach($customers ?? [] as $customer)
                  <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->nama }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Status</label>
              <select class="form-select" name="status">
                  <option value="">Semua Status</option>
                  <option value="verifikasi" {{ request('status') == 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                  <option value="sudah_bayar" {{ request('status') == 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                  <option value="proses_produksi" {{ request('status') == 'proses_produksi' ? 'selected' : '' }}>Proses Produksi</option>
                  <option value="sudah_cetak" {{ request('status') == 'sudah_cetak' ? 'selected' : '' }}>Sudah Cetak</option>
                  <option value="siap_antar" {{ request('status') == 'siap_antar' ? 'selected' : '' }}>Siap Antar</option>
              </select>
            </div>
            <!-- <div class="col-md-2">
              <label class="form-label small">Prioritas</label>
              <select class="form-select" name="prioritas">
                <option value="">Semua Prioritas</option>
                <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                <option value="normal" {{ request('prioritas') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="mendesak" {{ request('prioritas') == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
              </select>
            </div> -->
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
        <th>Nomor SPK</th>
                  <th>Tanggal SPK</th>
        <th>Pelanggan</th>
        <th>Status</th>
        <!-- <th>Prioritas</th> -->
                  <th>Total Biaya</th>
        <!-- <th>Item Pekerjaan</th> -->
                  <th>Aksi</th>
      </tr>
      </thead>
      <tbody>
      @forelse($spk as $item)
      <tr>
                    <td class="fw-semibold">{{ $item->nomor_spk }}</td>
                    <td>
                      {{ \Carbon\Carbon::parse($item->tanggal_spk)->locale('id')->translatedFormat('d F Y') }}
                    </td>
                    <td>
                      {{ $item->pelanggan->nama ?? '-' }}
                      @if($item->pelanggan && $item->pelanggan->email)
                        <br><small class="text-muted">{{ $item->pelanggan->email ?? '-'}}</small>
                      @endif
                    </td>
                    <td>
                        @if($item->status == 'verifikasi')
                            <span class="badge bg-secondary">Verifikasi</span>
                        @elseif($item->status == 'sudah_bayar')
                            <span class="badge bg-info">Sudah Bayar</span>
                        @elseif($item->status == 'proses_produksi')
                            <span class="badge bg-primary">Proses Produksi</span>
                        @elseif($item->status == 'sudah_cetak')
                            <span class="badge bg-warning text-dark">Sudah Cetak</span>
                        @elseif($item->status == 'siap_antar')
                            <span class="badge bg-success">Siap Antar</span>
                        @else
                            <span class="badge bg-light text-dark">{{ $item->status ?? '-' }}</span>
                        @endif
                    </td>
                    <!-- <td>
                      @if($item->prioritas == 'rendah')
                        <span class="badge bg-info">Rendah</span>
                      @elseif($item->prioritas == 'normal')
                        <span class="badge bg-primary">Normal</span>
                      @elseif($item->prioritas == 'tinggi')
                        <span class="badge bg-warning">Tinggi</span>
                      @elseif($item->prioritas == 'mendesak')
                        <span class="badge bg-danger">Mendesak</span>
                      @endif
                    </td> -->
                    <td class="fw-semibold">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                    <!-- <td>
                      <ul class="mb-0 list-unstyled">
                        @foreach($item->items as $pekerjaan)
                          <li><b>{{ $pekerjaan->nama_produk }}</b> ({{ $pekerjaan->jumlah }} {{ $pekerjaan->satuan }})</li>
      @endforeach
      </ul>
      </td> -->
      <td>
                      <div class="btn-group gap-1" role="group">
                        <a href="{{ route('spk.show', $item->id) }}" class="btn btn-primary btn-xs btn-icon rounded" title="Detail">
                          <i class="link-icon icon-sm" data-feather="eye"></i>
                        </a>
                        <a href="{{ route('spk.edit', $item->id) }}" class="btn btn-warning btn-xs btn-icon rounded" title="Edit">
                          <i class="link-icon icon-sm" data-feather="edit"></i>
                        </a>
                        <form action="{{ route('spk.destroy', $item->id) }}" method="POST" class="d-inline-block form-hapus-spk">
                          @csrf
                          @method('DELETE')
                          <button type="button" class="btn btn-danger btn-xs btn-icon rounded btn-hapus-spk" title="Hapus">
                            <i class="link-icon icon-sm" data-feather="trash"></i>
                          </button>
                        </form>
                      </div>
      </td>
      </tr>
    @empty
      <tr>
                    <td colspan="8" class="text-center py-4">
                      @if(request('search') || request('customer_id') || request('status') || request('prioritas'))
                        <div class="text-muted">
                          <i data-feather="search" class="icon-sm mb-2"></i>
                          <p class="mb-0">Tidak ditemukan data SPK yang sesuai dengan kriteria pencarian.</p>
                          <button type="button" class="btn btn-link btn-sm p-0 mt-2" id="clearSearch">
                            <i data-feather="x" class="icon-sm"></i> Hapus filter
                          </button>
                        </div>
                      @else
                        <div class="text-muted">
                          <i data-feather="file-text" class="icon-sm mb-2"></i>
                          <p class="mb-0">Belum ada data SPK.</p>
                        </div>
                      @endif
                    </td>
      </tr>
    @endforelse
      </tbody>
    </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted">
                @if($spk->total() > 0)
                  Menampilkan {{ $spk->firstItem() ?? 0 }} - {{ $spk->lastItem() ?? 0 }} dari {{ $spk->total() }} data SPK
                @else
                  Tidak ada data SPK
                @endif
              </div>
              <div>
                {{ $spk->appends(request()->query())->links('pagination::bootstrap-4') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('custom-scripts')
  <script>
    // Initialize Feather Icons
    feather.replace();

    // Search form handling
    const searchForm = $('#searchForm');
    const loadingSpinner = $('#loadingSpinner');
    const tableContainer = $('.table-responsive');

    // Auto-submit form when dropdown changes
    $('select[name="customer_id"], select[name="status"], select[name="prioritas"]').on('change', function() {
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
      window.location.href = '{{ route("spk.index") }}';
    });

    // Clear search
    $('#clearSearch').click(function() {
      window.location.href = '{{ route("spk.index") }}';
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

    document.querySelectorAll('.btn-hapus-spk').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Hapus SPK?',
          text: 'Data SPK yang dihapus tidak dapat dikembalikan!',
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