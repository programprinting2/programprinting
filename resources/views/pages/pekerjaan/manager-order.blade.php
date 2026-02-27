@extends('layout.master')

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Pekerjaan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Manager Order</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="row">
            <h6 class="card-title mb-0">Data Pekerjaan Manager Order</h6>
            <p class="text-muted mb-3">Daftar semua SPK yang perlu diproses oleh Manager Order.</p>
    </div>
          </div>

          <!-- Divider -->
          <hr class="my-4">

          <!-- Form Pencarian dan Filter -->
          <form id="searchForm" class="row g-3 mb-4">
            <div class="col-md-7">
              <label class="form-label small">&nbsp;</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i data-feather="search" class="icon-sm"></i>
                </span>
                <input type="text" class="form-control" name="search" placeholder="Cari nomor SPK, pelanggan, atau status..." value="{{ request('search') }}">
              </div>
            </div>
            <div class="col-md-4">
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
            <!-- <div class="col-md-4">
              <label class="form-label small">Status</label>
              <select class="form-select" name="status">
                  <option value="">Semua Status</option>
                  <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                  <option value="proses_bayar" {{ request('status') == 'proses_bayar' ? 'selected' : '' }}>Proses Pembayaran</option>
                  <option value="manager_approval_order" {{ request('status') == 'manager_approval_order' ? 'selected' : '' }}>Manager Approval Order</option>
                  <option value="manager_approval_produksi" {{ request('status') == 'manager_approval_produksi' ? 'selected' : '' }}>Manager Approval Produksi</option>
                  <option value="operator_cetak" {{ request('status') == 'operator_cetak' ? 'selected' : '' }}>Operator Cetak</option>
                  <option value="finishing_qc" {{ request('status') == 'finishing_qc' ? 'selected' : '' }}>Finishing / QC</option>
                  <option value="siap_diambil" {{ request('status') == 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                  <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
              </select>
            </div> -->
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

    <div class="d-flex gap-2 mb-2 justify-content-end">
      <button type="button" class="btn btn-success btn-sm" id="btnApproveSelected" disabled>
        Setujui SPK
      </button>
      <button type="button" class="btn btn-danger btn-sm" id="btnRejectSelected" disabled>
        Tolak SPK
      </button>
    </div>

    <div class="table-responsive">
    <table class="table align-middle">
      <thead>
      <tr>
      <th style="width: 32px;">
        <input type="checkbox" id="checkAllSpk">
      </th>
        <th>Nomor SPK</th>
                  <th>Tanggal SPK</th>
        <th>Pelanggan</th>
        <th>Status</th>
        <!-- <th>Prioritas</th> -->
                  <th>Total Biaya</th>
                  <th>Aksi</th>
                  <th>Otorisasi</th>
      </tr>
      </thead>
      <tbody id="accordionSpk">
      @forelse($spk as $item)
        @php
          $collapseId = 'detail-spk-'.$item->id;
        @endphp
      <tr>
                    <td>
                      @if($item->status === 'proses_bayar')
                        <input type="checkbox"
                              class="checkSpkRow"
                              value="{{ $item->id }}">
                      @endif
                    </td>
                    <td class="fw-semibold">
                        <button type="button"
                            class="btn btn-sm btn-link text-decoration-none p-0 toggle-collapse"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            aria-controls="{{ $collapseId }}">
                            {{ $item->nomor_spk }}
                        </button>
                    </td>
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
                        @php
                            $statusSteps = [
                                'draft'                     => 1,
                                'proses_bayar'              => 2,
                                'manager_approval_order'    => 3,
                                'manager_approval_produksi' => 4,
                                'operator_cetak'            => 5,
                                'finishing_qc'              => 6,
                                'siap_diambil'              => 7,
                                'selesai'                   => 8,
                            ];

                            $statusLabels = [
                                'draft'                     => 'Draft',
                                'proses_bayar'              => 'Proses Pembayaran',
                                'manager_approval_order'    => 'Manager Approval Order',
                                'manager_approval_produksi' => 'Manager Approval Produksi',
                                'operator_cetak'            => 'Operator Cetak',
                                'finishing_qc'              => 'Finishing / QC',
                                'siap_diambil'              => 'Siap Diambil',
                                'selesai'                   => 'Selesai',
                            ];

                            $statusIcons = [
                                'draft'                     => 'fa-file-alt',
                                'proses_bayar'              => 'fas fa-cash-register',
                                'manager_approval_order'    => 'fas fa-chalkboard-teacher',
                                'manager_approval_produksi' => 'fas fa-person-booth',
                                'operator_cetak'            => 'fa-print',
                                'finishing_qc'              => 'fas fa-people-carry',
                                'siap_diambil'              => 'fas fa-shopping-cart',
                                'selesai'                   => 'fa-check-double',
                            ];

                            $currentStep  = $statusSteps[$item->status] ?? 0;
                            $currentLabel = $statusLabels[$item->status] ?? ($item->status ?? '-');
                        @endphp

                        <div class="d-flex align-items-center gap-1">
                        @foreach($statusSteps as $status => $step)
                            @php
                                $colorClass = 'text-muted';
                                $style = '';

                                if ($step <= $currentStep) {
                                    $colorClass = 'text-primary';
                                }

                                if ($status === 'proses_bayar') {
                                    $pembayaran = $item->status_pembayaran ?? null;

                                    if ($pembayaran === 'belum_bayar') {
                                        $colorClass = 'text-danger';  
                                        $style = '';                  
                                    } elseif ($pembayaran === 'kurang_bayar') {
                                        $colorClass = 'text-warning';  
                                        $style = '';
                                    } elseif ($pembayaran === 'lunas') {
                                        $colorClass = 'text-primary';  
                                        $style = '';
                                    }
                                }
                            @endphp
                            <i class="fa {{ $statusIcons[$status] ?? 'fa-circle' }} {{ $colorClass }}"
                              style="{{ $style }} font-size: 0.8rem;"></i>
                        @endforeach
                        </div>
                        <small class="d-block text-muted mt-1">{{ $currentLabel }}</small>
                    </td>
                    <td class="fw-semibold">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>

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
      <td>
      @if($item->status === 'proses_bayar')
                          <form action="{{ route('spk.update-status', $item->id) }}" method="POST"
                                class="d-inline-block form-status-spk">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="">
                            <button type="button"
                                    class="btn btn-success btn-xs btn-icon rounded btn-status-manager"
                                    title="Setujui / Tolak SPK">
                              <i class="link-icon icon-sm" data-feather="check-circle"></i>
                            </button>
                          </form>
                        @endif
      </td>
      </tr>
      <!-- Collapse Row -->
            <tr>
                <td colspan="8" class="p-0 border-0">
                    <div id="{{ $collapseId }}" class="collapse spk-collapse" data-bs-parent="#accordionSpk">
                        <div class="bg-light border-top px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa fa-list me-1"></i> Detail Item 
                                    </span>
                                    <small class="text-muted">
                                        {{ $item->items->count() }} item • Total: Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Item</th>
                                            <th class="text-end">Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Ukuran / Luas</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($item->items as $spkItem)
                                            @php
                                                $produk = $spkItem->produk;
                                                $isMetric = $produk && ($produk->is_metric === true);
                                                $metricUnit = $produk && $produk->metric_unit ? $produk->metric_unit : 'cm';

                                                $panjang = (float) ($spkItem->panjang ?? 0);
                                                $lebar   = (float) ($spkItem->lebar ?? 0);

                                                if ($isMetric && $panjang > 0 && $lebar > 0) {
                                                    $luas = $panjang * $lebar;
                                                    $dimensiText = sprintf('%.2f × %.2f %s', $lebar, $panjang, strtolower($metricUnit));
                                                    $luasText = sprintf('Luas: %.2f %s²', $luas, strtolower($metricUnit));
                                                } elseif ($isMetric) {
                                                    $dimensiText = 'Metric ('.$metricUnit.')';
                                                    $luasText = 'Ukuran belum lengkap';
                                                } else {
                                                    $dimensiText = 'Non-metric';
                                                    $luasText = '';
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-truncate" title="{{ $spkItem->nama_produk }}">
                                                        {{ $spkItem->nama_produk }}
                                                    </div>
                                                    @if($produk && $produk->kode_produk)
                                                        <small class="text-muted">Kode: {{ $produk->kode_produk }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ $spkItem->jumlah }}</td>
                                                <td>{{ $spkItem->satuan }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <div class="fw-semibold">
                                                          {{ $dimensiText }}
                                                        </div>
                                                        @if($luasText)
                                                            <small class="text-muted">{{ $luasText }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($spkItem->keterangan)
                                                        <div class="text-wrap">{{ $spkItem->keterangan }}</div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    <i class="fa fa-info-circle me-1"></i>
                                                    Tidak ada item untuk SPK ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

    document.querySelectorAll('.btn-status-manager').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const form = btn.closest('form');
      if (!form) return;
      const actionInput = form.querySelector('input[name="action"]');

      Swal.fire({
        title: 'Proses SPK ini?',
        text: 'Pilih Setuju untuk lanjut ke Manager Produksi, atau Tolak untuk kembali ke Draft.',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: '#198754',
        denyButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Setuju',
        denyButtonText: 'Tolak',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          if (actionInput) actionInput.value = 'approve';
          form.submit();
        } else if (result.isDenied) {
          if (actionInput) actionInput.value = 'reject';
          form.submit();
        }
      });
    });
  });

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

    const checkAll = document.getElementById('checkAllSpk');
    const btnApprove = document.getElementById('btnApproveSelected');
    const btnReject = document.getElementById('btnRejectSelected');

    const rowChecks = () => Array.from(document.querySelectorAll('.checkSpkRow'));
    const getSelectedIds = () => rowChecks().filter(cb => cb.checked).map(cb => cb.value);

    function updateBulkButtons() {
      const anyChecked = getSelectedIds().length > 0;
      if (btnApprove) btnApprove.disabled = !anyChecked;
      if (btnReject) btnReject.disabled = !anyChecked;
    }

    if (checkAll) {
      checkAll.addEventListener('change', function() {
        rowChecks().forEach(cb => { cb.checked = checkAll.checked; });
        updateBulkButtons();
      });
    }

    document.addEventListener('change', function(e) {
      if (e.target.classList && e.target.classList.contains('checkSpkRow')) {
        const all = rowChecks();
        if (checkAll) {
          checkAll.checked = all.length > 0 && all.every(cb => cb.checked);
          checkAll.indeterminate = all.some(cb => cb.checked) && !checkAll.checked;
        }
        updateBulkButtons();
      }
    });

    async function bulkUpdateStatus(action) {
      const ids = getSelectedIds();
      if (ids.length === 0) return;

      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

      // Konfirmasi bulk
      const result = await Swal.fire({
        title: action === 'approve' ? 'Setujui semua terpilih?' : 'Tolak semua terpilih?',
        text: `Jumlah SPK terpilih: ${ids.length}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
      });

      if (!result.isConfirmed) return;
  
      for (const id of ids) {
        const res = await fetch(`/spk/${encodeURIComponent(id)}/status`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ action })
        });

        if (!res.ok) {
          const text = await res.text();
          console.error('Bulk update gagal untuk SPK:', id, text);
          await Swal.fire('Gagal', `Gagal update status SPK ID ${id}`, 'error');
          return;
        }
      }

      await Swal.fire('Berhasil', 'Status SPK terpilih berhasil diproses.', 'success');
      window.location.reload();
    }

    if (btnApprove) btnApprove.addEventListener('click', () => bulkUpdateStatus('approve'));
    if (btnReject) btnReject.addEventListener('click', () => bulkUpdateStatus('reject'));

    updateBulkButtons();
  </script>
@endpush