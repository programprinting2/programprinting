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
          <ul class="nav nav-tabs" id="managerOrderTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-spk-list" data-bs-toggle="tab"
                      data-bs-target="#tabSpkList" type="button" role="tab">
                Daftar SPK
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-bahan" data-bs-toggle="tab"
                      data-bs-target="#tabBahan" type="button" role="tab">
                Group Per Bahan Baku
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-mesin" data-bs-toggle="tab"
                      data-bs-target="#tabMesin" type="button" role="tab">
                Group Per Mesin
              </button>
            </li>
          </ul>

          <!-- Divider -->
          <!-- <hr class="my-4"> -->

          
    
    <div class="tab-content mt-3" id="managerOrderTabContent">
      <div class="tab-pane fade show active" id="tabSpkList" role="tabpanel" aria-labelledby="tab-spk-list">
        {{-- TAB 1: list SPK --}}
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
            <!-- Reset Button -->
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

        <!-- Action Buttons -->
        <div class="d-flex gap-2 mb-2 justify-content-end">
            <button type="button" class="btn btn-success btn-sm" id="btnApproveSelected" disabled>
                Setujui SPK
            </button>
            <button type="button" class="btn btn-danger btn-sm" id="btnRejectSelected" disabled>
                Tolak SPK
            </button>
        </div>

        <!-- Table SPK -->
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
                        <th>Total Biaya</th>
                        <th>Aksi</th>
                        <th>Otorisasi</th>
                    </tr>
                </thead>
                <tbody id="accordionSpk">
                    @forelse($spk as $item)
                        @php $collapseId = 'detail-spk-'.$item->id; @endphp
                        <tr>
                            <td>
                                @if($item->status === 'proses_bayar')
                                    <input type="checkbox" class="checkSpkRow" value="{{ $item->id }}">
                                @endif
                            </td>
                            <td class="fw-semibold">
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 toggle-collapse" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                    {{ $item->nomor_spk }}
                                </button>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_spk)->locale('id')->translatedFormat('d F Y') }}</td>
                            <td>
                                {{ $item->pelanggan->nama ?? '-' }}
                                @if($item->pelanggan && $item->pelanggan->email)
                                    <br><small class="text-muted">{{ $item->pelanggan->email ?? '-'}}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusSteps = [
                                        'draft' => 1,
                                        'proses_bayar' => 2,
                                        'manager_approval_order' => 3,
                                        'manager_approval_produksi' => 4,
                                        'operator_cetak' => 5,
                                        'finishing_qc' => 6,
                                        'siap_diambil' => 7,
                                        'selesai' => 8,
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'proses_bayar' => 'Proses Pembayaran',
                                        'manager_approval_order' => 'Manager Approval Order',
                                        'manager_approval_produksi' => 'Manager Approval Produksi',
                                        'operator_cetak' => 'Operator Cetak',
                                        'finishing_qc' => 'Finishing / QC',
                                        'siap_diambil' => 'Siap Diambil',
                                        'selesai' => 'Selesai',
                                    ];
                                    $statusIcons = [
                                        'draft' => 'fa-file-alt',
                                        'proses_bayar' => 'fas fa-cash-register',
                                        'manager_approval_order' => 'fas fa-chalkboard-teacher',
                                        'manager_approval_produksi' => 'fas fa-person-booth',
                                        'operator_cetak' => 'fa-print',
                                        'finishing_qc' => 'fas fa-people-carry',
                                        'siap_diambil' => 'fas fa-shopping-cart',
                                        'selesai' => 'fa-check-double',
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
                                                if ($pembayaran === 'belum_bayar') $colorClass = 'text-secondary';
                                                elseif ($pembayaran === 'kurang_bayar') $colorClass = 'text-warning';
                                                elseif ($pembayaran === 'lunas') $colorClass = 'text-primary';
                                            }
                                        @endphp
                                        <i class="fa {{ $statusIcons[$status] ?? 'fa-circle' }} {{ $colorClass }}" style="{{ $style }} font-size: 0.8rem;"></i>
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
                                    <form action="{{ route('spk.update-status', $item->id) }}" method="POST" class="d-inline-block form-status-spk">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="">
                                        <button type="button" class="btn btn-success btn-xs btn-icon rounded btn-status-manager" title="Setujui / Tolak SPK">
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
                                    <div class="border border-2 px-4 py-3">
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

                                        {{-- Detail Items Table --}}
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama Item</th>
                                                        <th class="text-end">Jumlah</th>
                                                        <th>Satuan</th>
                                                        <th>Ukuran / Luas</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="background-color: #ffffff;">
                                                    @php
                                                        $sortedItems = $item->items->sortBy(fn($it) => optional($it->produk)->kode_produk ?? $it->nama_produk ?? '');
                                                    @endphp
                                                    @forelse($sortedItems as $spkItem)
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
                                                            <td>{{ $spkItem->nama_produk }}</td>
                                                            <td class="text-end">{{ $spkItem->jumlah }}</td>
                                                            <td>{{ $spkItem->satuan }}</td>
                                                            <td>
                                                                <div class="fw-semibold">{{ $dimensiText }}</div>
                                                                @if($luasText)
                                                                    <div class="text-muted small">{{ $luasText }}</div>
                                                                @endif
                                                            </td>
                                                            <td>{{ $spkItem->keterangan ?? '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada item untuk SPK ini.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <hr class="my-3">

                                        {{-- Rekap Produk --}}
                                        @php
                                            $grouped = $item->items
                                                ->groupBy(fn($it) => $it->produk_id ?: $it->nama_produk)
                                                ->sortBy(fn($group) => optional($group->first()->produk)->kode_produk ?? $group->first()->nama_produk ?? '');
                                        @endphp
                                        <div class="fw-semibold mb-2">Rekap Produk</div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th>Kode</th>
                                                        <th class="text-end">Total Qty</th>
                                                        <th>Satuan</th>
                                                        <th class="text-end">Total Metric</th>
                                                        <th>Unit Metric</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="background-color: #ffffff;">
                                                    @forelse($grouped as $group)
                                                        @php
                                                            $first   = $group->first();
                                                            $produk  = $first->produk;
                                                            $nama    = $first->nama_produk;
                                                            $kode    = $produk->kode_produk ?? '-';
                                                            $satuan  = $first->satuan ?? '-';
                                                            $isMetric   = $produk && ($produk->is_metric === true);
                                                            $metricUnit = $produk && $produk->metric_unit ? $produk->metric_unit : 'cm';
                                                            $totalQty = $group->sum('jumlah');
                                                            $totalMetric = $isMetric ? $group->sum(fn($it) => ((float)($it->panjang ?? 0) * (float)($it->lebar ?? 0) * (float)($it->jumlah ?? 0))) : 0;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $nama }}</td>
                                                            <td>{{ $kode }}</td>
                                                            <td class="text-end">{{ number_format($totalQty, 2, ',', '.') }}</td>
                                                            <td>{{ $satuan }}</td>
                                                            <td class="text-end">
                                                                @if($isMetric)
                                                                    {{ number_format($totalMetric, 2, ',', '.') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($isMetric)
                                                                    {{ strtolower($metricUnit) }}²
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">Belum ada data rekap.</td>
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

            {{-- Pagination --}}
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
    {{-- TAB 2: Group per Bahan Baku --}}
    <div class="tab-pane fade" id="tabBahan" role="tabpanel" aria-labelledby="tab-bahan">
      <div class="accordion" id="bahanAccordion">
        @forelse($bahanBakuGroups as $bahan)
          <div class="accordion-item">
            <h2 class="accordion-header" id="bahan-heading{{ $bahan['id'] }}">
              <button class="accordion-button collapsed" type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#bahan-collapse{{ $bahan['id'] }}"
                      aria-expanded="false"
                      aria-controls="bahan-collapse{{ $bahan['id'] }}">
                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                  <div class="flex-grow-1">
                    <h6 class="mb-1 text-dark fw-bold">{{ $bahan['nama'] }}</h6>
                    @if(!empty($bahan['kode']))
                      <small class="text-muted">{{ $bahan['kode'] }}</small>
                    @endif
                  </div>
                  <div class="text-end">
                    <div class="badge bg-secondary text-dark mb-1">
                      {{ count($bahan['spk']) }} SPK
                    </div>
                  </div>
                </div>
              </button>
            </h2>
            <div id="bahan-collapse{{ $bahan['id'] }}" class="accordion-collapse collapse"
                aria-labelledby="bahan-heading{{ $bahan['id'] }}"
                data-bs-parent="#bahanAccordion">
              <div class="accordion-body">
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Nomor SPK</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Total Biaya</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($bahan['spk'] as $spkRow)
                        <tr>
                          <td>{{ $spkRow->nomor_spk }}</td>
                          <td>{{ \Carbon\Carbon::parse($spkRow->tanggal_spk)->format('d/m/Y') }}</td>
                          <td>{{ optional($spkRow->pelanggan)->nama ?? '-' }}</td>
                          <td>{{ $spkRow->status ?? '-' }}</td>
                          <td>Rp {{ number_format($spkRow->total_biaya, 0, ',', '.') }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            Tidak ada data SPK dengan bahan baku.
          </div>
        @endforelse
      </div>
    </div>

    {{-- TAB 3: Group per Mesin --}}
    <div class="tab-pane fade" id="tabMesin" role="tabpanel" aria-labelledby="tab-mesin">
      <div class="accordion" id="mesinAccordion">
        @forelse($mesinGroups as $mesin)
          <div class="accordion-item">
            <h2 class="accordion-header" id="mesin-heading{{ $mesin['id'] }}">
              <button class="accordion-button collapsed" type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#mesin-collapse{{ $mesin['id'] }}"
                      aria-expanded="false"
                      aria-controls="mesin-collapse{{ $mesin['id'] }}">
                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                  <div class="flex-grow-1">
                    <h6 class="mb-1 text-dark fw-bold">{{ $mesin['nama'] }}</h6>
                    @if(!empty($mesin['kode']))
                      <small class="text-muted">{{ $mesin['kode'] }}</small>
                    @endif
                  </div>
                  <div class="text-end">
                    <div class="badge bg-secondary mb-1">
                      {{ count($mesin['spk']) }} SPK
                    </div>
                  </div>
                </div>
              </button>
            </h2>
            <div id="mesin-collapse{{ $mesin['id'] }}" class="accordion-collapse collapse"
                aria-labelledby="mesin-heading{{ $mesin['id'] }}"
                data-bs-parent="#mesinAccordion">
              <div class="accordion-body">
                {{-- Tabel list SPK yang terkait dengan mesin ini --}}
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Nomor SPK</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Total Biaya</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($mesin['spk'] as $spkRow)
                        <tr>
                          <td>{{ $spkRow->nomor_spk }}</td>
                          <td>{{ \Carbon\Carbon::parse($spkRow->tanggal_spk)->format('d/m/Y') }}</td>
                          <td>{{ optional($spkRow->pelanggan)->nama ?? '-' }}</td>
                          <td>{{ $spkRow->status ?? '-' }}</td>
                          <td>Rp {{ number_format($spkRow->total_biaya, 0, ',', '.') }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            Tidak ada data SPK terkait mesin.
          </div>
        @endforelse
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