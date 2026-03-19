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
          <!-- <ul class="nav nav-tabs" id="managerOrderTabs" role="tablist">
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
          </ul> -->
          <div class="row g-3 mb-4" id="managerOrderTabs" role="tablist">
            <div class="col">
              <button class="card tab-card active w-100 text-start"
                      id="tab-spk-list"
                      data-bs-toggle="tab"
                      data-bs-target="#tabSpkList"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">

                  <div class="tab-icon bg-primary-subtle text-primary">
                    <i class="fa fa-file-alt"></i>
                  </div>

                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Daftar SPK</h6>
                      <span class="badge bg-primary rounded-pill px-3">
                        {{ $spk->count() }}
                      </span>
                    </div>
                    <small class="text-muted">List semua SPK</small>
                  </div>

                </div>
              </button>
            </div>

            <div class="col">
              <button class="card tab-card w-100 text-start"
                      id="tab-bahan"
                      data-bs-toggle="tab"
                      data-bs-target="#tabBahan"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">

                  <div class="tab-icon bg-warning-subtle text-warning">
                    <i class="fa fa-box"></i>
                  </div>

                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Group Per Bahan</h6>
                      <span class="badge bg-warning text-dark rounded-pill px-3">
                        {{ count($bahanBakuGroups) }}
                      </span>
                    </div>
                    <small class="text-muted">Rekap bahan baku</small>
                  </div>

                </div>
              </button>
            </div>

            <div class="col">
              <button class="card tab-card w-100 text-start"
                      id="tab-mesin"
                      data-bs-toggle="tab"
                      data-bs-target="#tabMesin"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">

                  <div class="tab-icon bg-success-subtle text-success">
                    <i class="fa fa-cogs"></i>
                  </div>

                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Group Per Mesin</h6>
                      <span class="badge bg-success rounded-pill px-3">
                        {{ count($mesinGroups) }}
                      </span>
                    </div>
                    <small class="text-muted">Rekap per mesin</small>
                  </div>
                </div>
              </button>
            </div>

            <div class="col">
              <button class="card tab-card w-100 text-start"
                      id="tab-pelanggan"
                      data-bs-toggle="tab"
                      data-bs-target="#tabPelanggan"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">

                  <div class="tab-icon bg-info-subtle text-info">
                    <i class="fa fa-users"></i>
                  </div>

                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Group Per Pelanggan</h6>
                      <span class="badge bg-info text-dark rounded-pill px-3">
                        {{ count($pelangganGroups) }}
                      </span>
                    </div>
                    <small class="text-muted">Rekap pelanggan</small>
                  </div>

                </div>
              </button>
            </div>

            <div class="col">
              <button class="card tab-card w-100 text-start"
                      id="tab-produk"
                      data-bs-toggle="tab"
                      data-bs-target="#tabProduk"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">

                  <div class="tab-icon bg-danger-subtle text-danger">
                    <i class="fa fa-tags"></i>
                  </div>

                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Group Per Produk</h6>
                      <span class="badge bg-danger rounded-pill px-3">
                        {{ count($produkGroups) }}
                      </span>
                    </div>
                    <small class="text-muted">Rekap per produk</small>
                  </div>

                </div>
              </button>
            </div>
          </div>

          <!-- Divider -->
          <hr class="my-4">

          
    
    <div class="tab-content mt-3" id="managerOrderTabContent">
      <div class="tab-pane fade show active" id="tabSpkList" role="tabpanel" aria-labelledby="tab-spk-list">
        {{-- TAB 1: list SPK --}}
        <!-- Form Pencarian dan Filter -->
        <form id="searchForm" class="row g-3 mb-4">
            <div class="col-md-11">
                <label class="form-label small">&nbsp;</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i data-feather="search" class="icon-sm"></i>
                    </span>
                    <input type="text" class="form-control" name="search" placeholder="Cari nomor SPK, pelanggan, atau status..." value="{{ request('search') }}">
                </div>
            </div>
            <!-- Reset Button -->
            <div class="col-md-1">
                <label class="form-label small">&nbsp;</label>
                <button type="button" class="btn btn-outline-secondary w-100" id="resetFilter" title="Reset Filter">
                    <i data-feather="refresh-cw" class="icon-sm"></i> Reset
                </button>
            </div>
            @php
                $statusOptions = [
                    ''                       => 'Semua Status',
                    'draft'                  => 'Draft',
                    'proses_bayar'           => 'Proses Pembayaran',
                    'manager_approval_order' => 'Manager Approval Order',
                    'operator_cetak'         => 'Operator Cetak',
                    'finishing_qc'           => 'Finishing / QC',
                    'siap_diambil'           => 'Siap Diambil',
                    'selesai'                => 'Selesai',
                ];

                $currentStatus = request('status', '');
            @endphp

            <input type="hidden" name="status" id="statusFilterInput" value="{{ $currentStatus }}">

            <div class="col-md-12">
              <label class="form-label small d-block mb-1">Filter Status</label>
              <div class="d-flex flex-nowrap gap-2 overflow-auto">
                @foreach($statusOptions as $value => $label)
                  @php
                    $isActive = ($currentStatus === $value) || ($value === '' && ($currentStatus === '' || $currentStatus === null));
                  @endphp

                  <button type="button"
                          class="card status-card text-start flex-fill filter-status-card {{ $isActive ? 'status-active' : '' }}"
                          data-status-value="{{ $value }}">
                    <div class="card-body py-2 px-3">
                      <h6 class="mb-0 fw-semibold" style="font-size:0.8rem;">
                        {{ $label }}
                      </h6>
                    </div>
                  </button>
                @endforeach
              </div>
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
                        <th>Progress</th>
                        <th>Aksi</th>
                        <th>Otorisasi</th>
                    </tr>
                </thead>
                <tbody id="accordionSpk">
                    @forelse($spk as $item)
                        @php $collapseId = 'detail-spk-'.$item->id; @endphp
                        <tr class="spk-row" data-status="{{ $item->status }}" data-spk-id="{{ $item->id }}">
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
                            <td>
                              {{ \Carbon\Carbon::parse($item->tanggal_spk)->locale('id')->translatedFormat('d F Y') }}
                                @php
                                    $spkDate = \Carbon\Carbon::parse($item->tanggal_spk);
                                    $now = \Carbon\Carbon::now();
                                    $diff = $spkDate->diff($now);
                                @endphp

                                @if($spkDate->isPast()) 
                                    <br>
                                    <small class="text-muted">
                                        {{ $diff->days }} hari 
                                        @if($diff->h > 0) 
                                            {{ $diff->h }} jam
                                        @endif
                                    </small>
                                @endif
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
                                        'draft' => 1,
                                        'proses_bayar' => 2,
                                        'manager_approval_order' => 3,
                                        'operator_cetak' => 4,
                                        'finishing_qc' => 5,
                                        'siap_diambil' => 6,
                                        'selesai' => 7,
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'proses_bayar' => 'Proses Pembayaran',
                                        'manager_approval_order' => 'Manager Approval Order',
                                        'operator_cetak' => 'Operator Cetak',
                                        'finishing_qc' => 'Finishing / QC',
                                        'siap_diambil' => 'Siap Diambil',
                                        'selesai' => 'Selesai',
                                    ];
                                    $statusIcons = [
                                        'draft' => 'fa-file-alt',
                                        'proses_bayar' => 'fas fa-cash-register',
                                        'manager_approval_order' => 'fas fa-chalkboard-teacher',
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
                            @php
                                $items = $item->items ?? collect(); 

                                $totalQty = $items->sum(function($it) {
                                    return (float) ($it->jumlah ?? 0);
                                });

                                $weightedProgressSum = $items->sum(function($it) {
                                    $qty   = (float) ($it->jumlah ?? 0);
                                    $pct   = (float) ($it->progress_cetak_persen ?? 0);
                                    return $qty * $pct;
                                });

                                $spkProgressPct = $totalQty > 0
                                    ? round($weightedProgressSum / $totalQty)
                                    : 0;

                                $spkProgressColor = $spkProgressPct >= 100
                                    ? 'bg-success'
                                    : ($spkProgressPct >= 50 ? 'bg-warning' : 'bg-primary');
                            @endphp

                            <td class="text-end align-middle">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar {{ $spkProgressColor }}"
                                        data-field="spk-progress-bar"
                                        data-spk-id="{{ $item->id }}"
                                        role="progressbar"
                                        style="width: {{ $spkProgressPct }}%;"
                                        aria-valuenow="{{ $spkProgressPct }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="small mt-1" data-field="spk-progress-pct"
                                data-spk-id="{{ $item->id }}">{{ $spkProgressPct }}%</div>
                            </td>
                            <td>
                                <div class="btn-group gap-1" role="group">
                                    <a href="{{ route('spk.show', $item) }}" class="btn btn-primary btn-xs btn-icon rounded" title="Detail">
                                        <i class="link-icon icon-sm" data-feather="eye"></i>
                                    </a>
                                    <a href="{{ route('spk.edit', $item) }}" class="btn btn-warning btn-xs btn-icon rounded" title="Edit">
                                        <i class="link-icon icon-sm" data-feather="edit"></i>
                                    </a>
                                    <form action="{{ route('spk.destroy', $item) }}" method="POST" class="d-inline-block form-hapus-spk">
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
                                    <form action="{{ route('spk.update-status', $item) }}" method="POST" class="d-inline-block form-status-spk">
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
                        <tr class="spk-row" data-status="{{ $item->status }}" data-spk-id="{{ $item->id }}">
                            <td colspan="9" class="p-0 border-0">
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
                                                      @php
                                                          $allFiles = [];
                                                          foreach ($item->items as $it) {
                                                              $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                                              $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

                                                              foreach ($files as $f) {
                                                                  $path = $f['path'] ?? null;
                                                                  if (!$path) continue;
                                                                  $allFiles[$path] = $f; 
                                                              }
                                                          }
                                                          $allFiles = array_values($allFiles);
                                                      @endphp

                                                      <th class="text-center align-middle p-1" style="width: 50px;">
                                                        <button type="button"
                                                                class="btn btn-sm btn-light p-1 btn-preview-spk-files"
                                                                data-spk-id="{{ $item->id }}"
                                                                data-spk-nomor="{{ $item->nomor_spk }}"
                                                                data-files='@json($allFiles)'
                                                                title="Lihat semua file SPK">
                                                          <i class="fa fa-eye"></i>
                                                        </button>
                                                      </th>
                                                      <th class="align-middle">Keterangan</th>
                                                      <th class="align-middle">Nama Item</th>
                                                      <th class="align-middle">Ukuran / Luas</th>
                                                      <th class="text-end align-middle">Jumlah</th>
                                                      <th class="align-middle">Satuan</th>
                                                      <th class="text-end align-middle">Progress</th>
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
                                                                $dimensiText = '-';
                                                                $luasText = '';
                                                            }
                                                        @endphp
                                                        @php
                                                            $filePendukungRaw = $spkItem->file_pendukung ?? $spkItem->file_pendukung_json ?? '[]';
                                                            if (is_string($filePendukungRaw)) {
                                                                $filePendukung = json_decode($filePendukungRaw, true) ?: [];
                                                            } else {
                                                                $filePendukung = (array) $filePendukungRaw;
                                                            }
                                                            $defaultFile = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;

                                                            $defaultFileType = $defaultFile['type'] ?? null;
                                                            $isPdf = strtolower((string) $defaultFileType) === 'pdf';
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center" style="width: 60px;">
                                                                @if($defaultFile && !empty($defaultFile['path']))
                                                                    @php
                                                                        $filePath   = $defaultFile['path'];
                                                                        $isPdf      = strtolower((string) ($defaultFile['type'] ?? '')) === 'pdf';
                                                                        $previewUrl = route('backend.preview-file', ['path' => $filePath]);
                                                                    @endphp

                                                                    <button type="button"
                                                                            class="btn btn-sm btn-light p-1 btn-preview-item-file"
                                                                            data-file-path="{{ $filePath }}"
                                                                            data-file-name="{{ $defaultFile['name'] ?? '' }}"
                                                                            data-file-type="{{ $defaultFile['type'] ?? '' }}"
                                                                            title="Preview file">
                                                                        @if($isPdf)
                                                                            <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                                                        @else
                                                                            <img src="{{ $previewUrl }}"
                                                                                alt="{{ $defaultFile['name'] ?? 'Preview' }}"
                                                                                class="img-thumbnail"
                                                                                style="max-width: 40px; max-height: 40px; object-fit: cover; border-radius:4px;"
                                                                                loading="lazy">
                                                                        @endif
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ !empty(trim($spkItem->keterangan)) ? $spkItem->keterangan : '-' }}</td>
                                                            <td>{{ $spkItem->nama_produk }}</td>
                                                            <td>
                                                                <div class="fw-semibold">{{ $dimensiText }}</div>
                                                                @if($luasText)
                                                                    <div class="text-muted small">{{ $luasText }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">{{ $spkItem->jumlah }}</td>
                                                            <td>{{ $spkItem->satuan }}</td>
                                                            @php
                                                                $progressPct = (float) ($spkItem->progress_cetak_persen ?? 0);
                                                                $sisa        = (int)  ($spkItem->sisa_belum_cetak ?? 0);
                                                                $progressColor = $progressPct >= 100
                                                                    ? 'bg-success'
                                                                    : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');
                                                            @endphp

                                                            <td class="text-end align-middle" data-field="spk-item-progress-cell" data-spk-item-id="{{ $spkItem->id }}" data-spk-id="{{ $item->id }}">
                                                                @if($sisa <= 0)
                                                                    <span class="badge bg-success mb-2" data-field="spk-item-done-badge" data-spk-item-id="{{ $spkItem->id }}">
                                                                      Selesai
                                                                    </span>
                                                                @else
                                                                    <div class="small text-danger fw-semibold mb-1"
                                                                    data-field="spk-item-remaining" data-spk-item-id="{{ $spkItem->id }}">
                                                                        Sisa: {{ number_format($sisa, 0, ',', '.') }} {{ $spkItem->satuan }}
                                                                    </div>
                                                                @endif
                                                                <div class="progress" style="height:6px;">
                                                                    <div class="progress-bar {{ $progressColor }}"
                                                                        role="progressbar"
                                                                        data-field="spk-item-progress-bar"
                                                                        data-spk-item-id="{{ $spkItem->id }}"
                                                                        style="width: {{ $progressPct }}%;"
                                                                        aria-valuenow="{{ $progressPct }}"
                                                                        aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="small" data-field="spk-item-progress-pct"
                                                                data-spk-item-id="{{ $spkItem->id }}">{{ $progressPct }}%</div>
                                                            </td>
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

                                        <!-- <div class="modal fade" id="modalPreviewFilesSpk{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <h5 class="modal-title">Preview File SPK {{ $item->nomor_spk }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>

                                              @php
                                                $allFiles = [];
                                                foreach ($item->items as $it) {
                                                    $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                                    $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array)$raw;

                                                    foreach ($files as $f) {
                                                        $path = $f['path'] ?? null;
                                                        if (!$path) continue;
                                                        $allFiles[$path] = $f; 
                                                    }
                                                }
                                                $allFiles = array_values($allFiles);
                                              @endphp

                                              <div class="modal-body">
                                                @if(empty($allFiles))
                                                  <div class="text-center text-muted py-4">Tidak ada file pendukung.</div>
                                                @else
                                                  <div class="row g-3">
                                                    @foreach($allFiles as $f)
                                                      @php
                                                        $path = $f['path'];
                                                        $name = $f['name'] ?? basename($path);
                                                        $type = strtolower((string)($f['type'] ?? ''));
                                                        $isPdf = ($type === 'pdf');
                                                        $previewUrl = route('backend.preview-file', ['path' => $path]);
                                                      @endphp

                                                      <div class="col-md-4">
                                                        <div class="border rounded p-2 h-100">
                                                          <div class="small fw-semibold text-truncate" title="{{ $name }}">{{ $name }}</div>
                                                          <div class="mt-2">
                                                            @if($isPdf)
                                                              <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-outline-danger w-100">
                                                                <i class="fa fa-file-pdf"></i> Buka PDF
                                                              </a>
                                                            @else
                                                              <a href="{{ $previewUrl }}" target="_blank" class="d-block">
                                                                <img src="{{ $previewUrl }}"
                                                                    alt="{{ $name }}"
                                                                    class="img-fluid rounded"
                                                                    loading="lazy"
                                                                    style="max-height: 180px; object-fit: cover; width:100%;">
                                                              </a>
                                                            @endif
                                                          </div>
                                                        </div>
                                                      </div>
                                                    @endforeach
                                                  </div>
                                                @endif
                                              </div>
                                            </div>
                                          </div>
                                        </div> -->

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
                                                        <th class="text-end">Total</th>
                                                        <th>Unit</th>
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
                                @if(request('search'))
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
                    <tr id="clientFilterEmptyRow" class="d-none" aria-live="polite">
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i data-feather="filter" class="icon-sm mb-2"></i>
                            <p class="mb-0">Tidak ada SPK dengan status yang dipilih.</p>
                            <button type="button" class="btn btn-link btn-sm p-0 mt-2" id="clearStatusFilter">
                                <i data-feather="x" class="icon-sm"></i> Tampilkan semua status
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
          </div> 
        </div>
    {{-- TAB 2: Group per Bahan Baku --}}
    <div class="tab-pane fade" id="tabBahan" role="tabpanel" aria-labelledby="tab-bahan">
      <div class="accordion" id="bahanAccordion">
        @forelse($bahanBakuGroups as $bahan)
          @php
            $totalQty = 0;
            $weightedProgress = 0;

            foreach ($bahan['spk'] as $spkRow) {
                foreach ($spkRow->items as $spkItem) {

                    $produk = $spkItem->produk;
                    if (!$produk) continue;

                    if (!$produk->bahanBakus->contains('id', $bahan['id'])) continue;

                    $qty = (float) ($spkItem->jumlah ?? 0);
                    $pct = (float) ($spkItem->progress_cetak_persen ?? 0);

                    $totalQty += $qty;
                    $weightedProgress += ($qty * $pct);
                }
            }

            $bahanProgressPct = $totalQty > 0
                ? round($weightedProgress / $totalQty)
                : 0;

            $bahanProgressColor = $bahanProgressPct >= 100
                ? 'bg-success'
                : ($bahanProgressPct >= 50 ? 'bg-warning' : 'bg-primary');
          @endphp

          <div class="accordion-item">
            <h2 class="accordion-header" id="bahan-heading{{ $bahan['id'] }}">
              <button class="accordion-button collapsed"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#bahan-collapse{{ $bahan['id'] }}"
                  aria-expanded="false"
                  aria-controls="bahan-collapse{{ $bahan['id'] }}">
                  <div class="d-flex justify-content-between align-items-center w-100 me-3">
                    {{-- LEFT SECTION --}}
                    <div>
                        @php
                        $totalMetric = 0;
                        $metricUnitLabel = 'm';
                        foreach ($bahan['spk'] as $spkRow) {
                        foreach ($spkRow->items as $spkItem) {
                        $produk = $spkItem->produk;
                        if (!$produk) continue;
                        if (!$produk->bahanBakus->contains('id', $bahan['id'])) continue;
                        if ($produk->is_metric !== true) continue;
                        $metricUnit = $produk->metric_unit ?: 'cm';
                        $panjang = (float) ($spkItem->panjang ?? 0);
                        $lebar   = (float) ($spkItem->lebar ?? 0);
                        $jumlah  = (float) ($spkItem->jumlah ?? 0);
                        if ($panjang <= 0 || $lebar <= 0 || $jumlah <= 0) continue;
                        switch ($metricUnit) {
                        case 'cm':
                        $panjang /= 100;
                        $lebar   /= 100;
                        break;
                        case 'mm':
                        $panjang /= 1000;
                        $lebar   /= 1000;
                        break;
                        }
                        $totalMetric += $panjang * $lebar * $jumlah;
                        }
                        }
                        @endphp
                        <h6 class="mb-1 fw-bold text-dark">
                          {{ $bahan['nama'] }}
                        </h6>
                        @if(!empty($bahan['kode']))
                        <small class="text-muted d-block">
                        {{ $bahan['kode'] }}
                        </small>
                        @endif
                        {{-- PROGRESS --}}
                        <div class="d-flex align-items-center gap-2 mt-2" style="width:160px;">
                          <div class="progress w-100" style="height:6px;">
                              <div class="progress-bar {{ $bahanProgressColor }}"
                                role="progressbar"
                                style="width: {{ $bahanProgressPct }}%;">
                              </div>
                          </div>
                          <span class="small fw-semibold text-muted">
                          {{ $bahanProgressPct }}%
                          </span>
                        </div>
                    </div>
                    {{-- RIGHT SECTION --}}
                    <div class="text-end">
                        <div class="badge bg-secondary mb-1">
                          {{ count($bahan['spk']) }} SPK
                        </div>
                        @if($totalMetric > 0)
                        <div class="small text-muted">
                          Total: {{ number_format($totalMetric, 2, ',', '.') }} {{ strtolower($metricUnitLabel) }}²
                        </div>
                        @endif
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
                        <th>Pelanggan</th>
                        <th class="text-center" style="width:50px;">File</th>
                        <th>Nama Item</th>
                        <th>Ukuran / Luas</th>
                        <th class="text-end">Jumlah</th>
                        <th>Satuan</th>
                        <th>Progress</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($bahan['spk'] as $spkRow)
                        @php
                        $itemsForBahan = $spkRow->items
                          ->filter(function($spkItem) use ($bahan) {
                              $produk = $spkItem->produk;
                              if (!$produk) return false;

                              return $produk->bahanBakus->contains('id', $bahan['id']);
                          })
                          ->sortBy(function($spkItem) {
                              return strtolower($spkItem->nama_produk ?? '');
                          })
                          ->values();
                          $rowspan = $itemsForBahan->count();
                          $firstRow = true;

                          $allFiles = [];
                          foreach ($spkRow->items as $it) {
                              $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                              $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

                              foreach ($files as $f) {
                                  $path = $f['path'] ?? null;
                                  if (!$path) continue;
                                  $allFiles[$path] = $f;
                              }
                          }
                          $allFiles = array_values($allFiles);
                        @endphp

                        @forelse($itemsForBahan as $spkItem)
                          @php
                              $produk = $spkItem->produk;
                              $isMetric   = $produk && ($produk->is_metric === true);
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
                                  $dimensiText = '-';
                                  $luasText = '';
                              }

                          $progressPct = (float) ($spkItem->progress_cetak_persen ?? 0);
                          $sisa = (int) ($spkItem->sisa_belum_cetak ?? 0);

                          $progressColor = $progressPct >= 100
                          ? 'bg-success'
                          : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');

                          @endphp

                          <tr>
                            @if($firstRow)
                                <td rowspan="{{ $rowspan }}" class="fw-bold align-top">
                                    <div class="d-flex align-items-start gap-2">
                                        <div>
                                            {{ $spkRow->nomor_spk }}
                                            @php
                                                $spkDate = \Carbon\Carbon::parse($spkRow->tanggal_spk);
                                                $now = \Carbon\Carbon::now();
                                                $diff = $spkDate->diff($now);
                                            @endphp
                                            <small class="text-muted d-block">
                                                {{ $spkDate->format('d/m/Y') }}
                                            </small>

                                            @if($spkDate->isPast())
                                                <small class="text-muted">
                                                    {{ $diff->days }} hari 
                                                    @if($diff->h > 0) 
                                                        {{ $diff->h }} jam
                                                    @endif
                                                </small>
                                            @endif
                                        </div>

                                        @php
                                            $groupDefaultFiles = [];
                                            foreach ($itemsForBahan as $it) {
                                                $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                                $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;
                                                $default = (is_array($files) && count($files)) ? $files[0] : null;
                                                $path = $default['path'] ?? null;
                                                if (!$path) {
                                                    continue;
                                                }
                                                $groupDefaultFiles[$path] = $default;
                                            }
                                            $groupDefaultFiles = array_values($groupDefaultFiles);
                                        @endphp
                                        
                                        @if(count($groupDefaultFiles))
                                          <button type="button"
                                                  class="btn btn-sm btn-light p-1 ms-1 btn-preview-spk-files"
                                                  data-spk-id="{{ $spkRow->id }}"
                                                  data-spk-nomor="{{ $spkRow->nomor_spk }}"
                                                  data-files='@json($groupDefaultFiles)'
                                                  title="Lihat file default item (group ini)">
                                              <i class="fa fa-eye"></i>
                                          </button>
                                        @endif
                                    </div>
                                </td>
                                <td rowspan="{{ $rowspan }}" class="align-top">
                                    {{ optional($spkRow->pelanggan)->nama ?? '-' }}
                                </td>

                                @php $firstRow = false; @endphp
                            @endif

                            @php
                                $filePendukungRaw = $spkItem->file_pendukung ?? $spkItem->file_pendukung_json ?? '[]';
                                if (is_string($filePendukungRaw)) {
                                    $filePendukung = json_decode($filePendukungRaw, true) ?: [];
                                } else {
                                    $filePendukung = (array) $filePendukungRaw;
                                }

                                $firstFile   = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;
                                $thumbUrl    = null;
                                $isPdfFirst  = false;

                                if ($firstFile && !empty($firstFile['path'])) {
                                    $thumbUrl   = route('backend.preview-file', ['path' => $firstFile['path']]);
                                    $isPdfFirst = strtolower((string) ($firstFile['type'] ?? '')) === 'pdf';
                                }
                            @endphp

                            <td class="text-center align-middle" style="width: 60px;">
                                @if($firstFile && $thumbUrl)
                                    <button type="button"
                                            class="btn btn-sm btn-light p-1 btn-preview-item-file"
                                            data-file-path="{{ $firstFile['path'] }}"
                                            data-file-name="{{ $firstFile['name'] ?? '' }}"
                                            data-file-type="{{ $firstFile['type'] ?? '' }}"
                                            title="Preview file item">
                                        @if($isPdfFirst)
                                            <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                        @else
                                            <img src="{{ $thumbUrl }}"
                                                alt="{{ $firstFile['name'] ?? 'Preview' }}"
                                                class="img-thumbnail"
                                                style="max-width: 40px; max-height: 40px; object-fit: cover; border-radius:4px;"
                                                loading="lazy">
                                        @endif
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $spkItem->nama_produk }}</td>
                            <td>
                                <div class="fw-semibold">{{ $dimensiText }}</div>
                                @if($luasText)
                                    <div class="text-muted small">{{ $luasText }}</div>
                                @endif
                            </td>
                            <td class="text-end">{{ $spkItem->jumlah }}</td>
                            <td>{{ $spkItem->satuan }}</td>
                            <td class="text-end align-middle">
                              @if($sisa <= 0)
                                <span class="badge bg-success mb-2">
                                Selesai
                                </span>
                              @else
                              <div class="small text-danger fw-semibold mb-1">
                                Sisa: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                              </div>
                              @endif

                              <div class="progress" style="height:6px;">
                                <div class="progress-bar {{ $progressColor }}"
                                    role="progressbar"
                                    style="width: {{ $progressPct }}%;">
                                </div>
                              </div>
                              <div class="small">
                              {{ $progressPct }}%
                              </div>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="7" class="text-center text-muted">
                              Tidak ada item untuk bahan ini pada SPK ini.
                            </td>
                          </tr>
                        @endforelse
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
          @php
            $totalQty = 0;
            $weightedProgress = 0;

            $targetId = $mesin['id'] ?? null;

            foreach ($mesin['spk'] as $spkRow) {
                foreach ($spkRow->items as $spkItem) {

                    $produk = $spkItem->produk;
                    if (!$produk || !$targetId) continue;

                    $alur = $produk->alur_produksi_json ?? [];
                    if (!is_array($alur) || empty($alur)) continue;

                    $matched = false;

                    foreach ($alur as $step) {
                        if (!is_array($step)) continue;

                        if ((int)($step['divisi_mesin_id'] ?? 0) === (int)$targetId) {
                            $matched = true;
                            break;
                        }
                    }

                    if (!$matched) continue;

                    $qty = (float) ($spkItem->jumlah ?? 0);
                    $pct = (float) ($spkItem->progress_cetak_persen ?? 0);

                    $totalQty += $qty;
                    $weightedProgress += ($qty * $pct);
                }
            }

            $mesinProgressPct = $totalQty > 0
                ? round($weightedProgress / $totalQty)
                : 0;

            $mesinProgressColor = $mesinProgressPct >= 100
                ? 'bg-success'
                : ($mesinProgressPct >= 50 ? 'bg-warning' : 'bg-primary');
            @endphp
          <div class="accordion-item">
            <h2 class="accordion-header" id="mesin-heading{{ $mesin['id'] }}">
              <button class="accordion-button collapsed" type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#mesin-collapse{{ $mesin['id'] }}"
                      aria-expanded="false"
                      aria-controls="mesin-collapse{{ $mesin['id'] }}">
                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                  <div>
                    @php
                      $totalMetric = 0;
                      $metricUnitLabel = 'm';

                      $targetId = $mesin['id'] ?? null;

                      foreach ($mesin['spk'] as $spkRow) {
                          foreach ($spkRow->items as $spkItem) {
                              $produk = $spkItem->produk;
                              if (!$produk || $produk->is_metric !== true || !$targetId) {
                                  continue;
                              }

                              $alur = $produk->alur_produksi_json ?? [];
                              if (!is_array($alur) || empty($alur)) {
                                  continue;
                              }

                              $matched = false;
                              foreach ($alur as $step) {
                                  if (!is_array($step)) continue;
                                  if ((int)($step['divisi_mesin_id'] ?? 0) === (int)$targetId) {
                                      $matched = true;
                                      break;
                                  }
                              }
                              if (!$matched) {
                                  continue;
                              }

                              $metricUnit = $produk->metric_unit ?: 'cm';
                              $panjang = (float) ($spkItem->panjang ?? 0);
                              $lebar   = (float) ($spkItem->lebar ?? 0);
                              $jumlah  = (float) ($spkItem->jumlah ?? 0);

                              if ($panjang <= 0 || $lebar <= 0 || $jumlah <= 0) {
                                  continue;
                              }

                              switch (strtolower($metricUnit)) {
                                  case 'mm':
                                      $panjang /= 1000; $lebar /= 1000; break;
                                  case 'cm':
                                      $panjang /= 100;  $lebar /= 100;  break;
                                  case 'm':
                                  default:
                                      break;
                              }

                              $totalMetric += $panjang * $lebar * $jumlah;
                          }
                      }
                    @endphp
                    <h6 class="mb-1 text-dark fw-bold">{{ $mesin['nama'] }}</h6>
                    @if(!empty($mesin['kode']))
                      <small class="text-muted">{{ $mesin['kode'] }}</small>
                    @endif
                    <div class="d-flex align-items-center gap-2 mt-2" style="width:160px;">
                      <div class="progress w-100" style="height:6px;">
                          <div class="progress-bar {{ $mesinProgressColor }}"
                            role="progressbar"
                            style="width: {{ $mesinProgressPct }}%;">
                          </div>
                      </div>
                      <span class="small fw-semibold text-muted">
                      {{ $mesinProgressPct }}%
                      </span>
                    </div>
                  </div>
                  <div class="text-end">
                    <div class="badge bg-secondary mb-1">
                      {{ count($mesin['spk']) }} SPK
                    </div>
                    @if($totalMetric > 0)
                      <div class="small text-muted">
                        Total: {{ number_format($totalMetric, 2, ',', '.') }} {{ strtolower($metricUnitLabel ?? 'cm') }}²
                      </div>
                    @endif
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
                        <th>Pelanggan</th>
                        <th class="text-center" style="width:50px;">File</th>
                        <th>Nama Item</th>
                        <th>Ukuran / Luas</th>
                        <th class="text-end">Jumlah</th>
                        <th>Satuan</th>
                        <th>Progress</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($mesin['spk'] as $spkRow)
                        @php
                          $targetId = $mesin['id'] ?? null;
                          $targetNama = trim((string)($mesin['nama'] ?? ''));

                          $itemsForMesin = $spkRow->items->filter(function ($spkItem) use ($targetId, $targetNama) {
                              $produk = $spkItem->produk;
                              if (!$produk) {
                                  return false;
                              }

                              $alur = $produk->alur_produksi_json ?? [];
                              if (!is_array($alur) || empty($alur)) {
                                  return false;
                              }

                              foreach ($alur as $step) {
                                  if (!is_array($step)) {
                                      continue;
                                  }

                                  $stepId = $step['divisi_mesin_id'] ?? null;
                                  $stepNama = trim((string)($step['divisi_mesin'] ?? ''));

                                  if ($targetId && (int)$stepId === (int)$targetId) {
                                      return true;
                                  }

                                  if (!$targetId && $targetNama !== '' && strcasecmp($stepNama, $targetNama) === 0) {
                                      return true;
                                  }
                              }

                              return false;
                          })->values();

                          $rowspan = $itemsForMesin->count();
                          $firstRow = true;
                        @endphp

                        @forelse($itemsForMesin as $spkItem)
                          @php
                              $produk = $spkItem->produk;
                              $isMetric   = $produk && ($produk->is_metric === true);
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
                                  $dimensiText = '-';
                                  $luasText = '';
                              }
                              $progressPct = (float) ($spkItem->progress_cetak_persen ?? 0);
                              $sisa = (int) ($spkItem->sisa_belum_cetak ?? 0);

                              $progressColor = $progressPct >= 100
                              ? 'bg-success'
                              : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');
                          @endphp

                          <tr>
                              @if($firstRow)
                                  <td rowspan="{{ $rowspan }}" class="fw-bold align-top">
                                    <div class="d-flex align-items-start gap-2">
                                      <div>
                                        {{ $spkRow->nomor_spk }}
                                        @php
                                            $spkDate = \Carbon\Carbon::parse($spkRow->tanggal_spk);
                                            $now = \Carbon\Carbon::now();
                                            $diff = $spkDate->diff($now);
                                            $isPast = $spkDate->isPast();
                                        @endphp
                                        <small class="text-muted d-block">
                                            {{ $spkDate->format('d/m/Y') }}
                                        </small>

                                        @if($spkDate->isPast()) 
                                            <small class="text-muted">
                                                {{ $diff->days }} hari 
                                                @if($diff->h > 0) 
                                                    {{ $diff->h }} jam
                                                @endif
                                            </small>
                                        @endif
                                      </div>

                                      @php
                                          $groupDefaultFiles = [];

                                          foreach ($itemsForMesin as $it) {
                                              $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                              $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

                                              $default = (is_array($files) && count($files)) ? $files[0] : null;
                                              $path = $default['path'] ?? null;

                                              if (!$path) {
                                                  continue;
                                              }

                                              $groupDefaultFiles[$path] = $default;
                                          }

                                          $groupDefaultFiles = array_values($groupDefaultFiles);
                                      @endphp
                                      
                                      @if(count($groupDefaultFiles))
                                        <button type="button"
                                                class="btn btn-sm btn-light p-1 ms-1 btn-preview-spk-files"
                                                data-spk-id="{{ $spkRow->id }}"
                                                data-spk-nomor="{{ $spkRow->nomor_spk }}"
                                                data-files='@json($groupDefaultFiles)'
                                                title="Lihat file default item (group ini)">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                      @endif
                                    </div>
                                  </td>
                                  <td rowspan="{{ $rowspan }}" class="align-top">
                                      {{ optional($spkRow->pelanggan)->nama ?? '-' }}
                                  </td>

                                  @php $firstRow = false; @endphp
                              @endif

                                @php
                                  $filePendukungRaw = $spkItem->file_pendukung ?? $spkItem->file_pendukung_json ?? '[]';
                                  if (is_string($filePendukungRaw)) {
                                      $filePendukung = json_decode($filePendukungRaw, true) ?: [];
                                  } else {
                                      $filePendukung = (array) $filePendukungRaw;
                                  }

                                  $firstFile   = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;
                                  $thumbUrl    = null;
                                  $isPdfFirst  = false;

                                  if ($firstFile && !empty($firstFile['path'])) {
                                      $thumbUrl   = route('backend.preview-file', ['path' => $firstFile['path']]);
                                      $isPdfFirst = strtolower((string) ($firstFile['type'] ?? '')) === 'pdf';
                                  }
                              @endphp

                              <td class="text-center align-middle" style="width: 60px;">
                                  @if($firstFile && $thumbUrl)
                                      <button type="button"
                                              class="btn btn-sm btn-light p-1 btn-preview-item-file"
                                              data-file-path="{{ $firstFile['path'] }}"
                                              data-file-name="{{ $firstFile['name'] ?? '' }}"
                                              data-file-type="{{ $firstFile['type'] ?? '' }}"
                                              title="Preview file item">
                                          @if($isPdfFirst)
                                              <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                          @else
                                              <img src="{{ $thumbUrl }}"
                                                  alt="{{ $firstFile['name'] ?? 'Preview' }}"
                                                  class="img-thumbnail"
                                                  style="max-width: 40px; max-height: 40px; object-fit: cover; border-radius:4px;"
                                                  loading="lazy">
                                          @endif
                                      </button>
                                  @else
                                      <span class="text-muted">-</span>
                                  @endif
                              </td>
                              <td>{{ $spkItem->nama_produk }}</td>
                              <td>
                                  <div class="fw-semibold">{{ $dimensiText }}</div>
                                  @if($luasText)
                                      <div class="text-muted small">{{ $luasText }}</div>
                                  @endif
                              </td>

                              <td class="text-end">{{ $spkItem->jumlah }}</td>
                              <td>{{ $spkItem->satuan }}</td>
                              <td class="text-end align-middle">
                                @if($sisa <= 0)
                                  <span class="badge bg-success mb-2">
                                  Selesai
                                  </span>
                                @else
                                <div class="small text-danger fw-semibold mb-1">
                                  Sisa: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                                </div>
                                @endif

                                <div class="progress" style="height:6px;">
                                  <div class="progress-bar {{ $progressColor }}"
                                      role="progressbar"
                                      style="width: {{ $progressPct }}%;">
                                  </div>
                                </div>
                                <div class="small">
                                {{ $progressPct }}%
                                </div>
                              </td>

                          </tr>
                        @empty
                          <tr>
                            <td colspan="7" class="text-center text-muted">
                              Tidak ada item untuk mesin ini pada SPK ini.
                            </td>
                          </tr>
                        @endforelse
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

    {{-- TAB 4: Group per Pelanggan --}}
    <div class="tab-pane fade" id="tabPelanggan" role="tabpanel" aria-labelledby="tab-pelanggan">
      <div class="accordion" id="pelangganAccordion">
        @forelse($pelangganGroups as $pel)
          @php
            $pelId = $pel['id'] ?? 'none';
          @endphp
          <div class="accordion-item">
            <h2 class="accordion-header" id="pelanggan-heading{{ $pelId }}">
              <button class="accordion-button collapsed" type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#pelanggan-collapse{{ $pelId }}"
                      aria-expanded="false"
                      aria-controls="pelanggan-collapse{{ $pelId }}">
                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                  <div>
                    @php
                      $totalQty = 0;
                      $weightedProgress = 0;

                      foreach ($pel['spk'] as $spkRow) {
                          foreach ($spkRow->items as $spkItem) {

                              $qty = (float) ($spkItem->jumlah ?? 0);
                              $pct = (float) ($spkItem->progress_cetak_persen ?? 0);

                              $totalQty += $qty;
                              $weightedProgress += ($qty * $pct);
                          }
                      }

                      $pelProgressPct = $totalQty > 0
                          ? round($weightedProgress / $totalQty)
                          : 0;

                      $pelProgressColor = $pelProgressPct >= 100
                          ? 'bg-success'
                          : ($pelProgressPct >= 50 ? 'bg-warning' : 'bg-primary');
                      $totalMetric = 0;
                      $metricUnitLabel = 'm';

                      foreach ($pel['spk'] as $spkRow) {
                          foreach ($spkRow->items as $spkItem) {
                              $produk = $spkItem->produk;
                              if (!$produk || $produk->is_metric !== true) {
                                  continue;
                              }

                              $metricUnit = $produk->metric_unit ?: 'cm';
                              $panjang = (float) ($spkItem->panjang ?? 0);
                              $lebar   = (float) ($spkItem->lebar ?? 0);
                              $jumlah  = (float) ($spkItem->jumlah ?? 0);
                              if ($panjang <= 0 || $lebar <= 0 || $jumlah <= 0) {
                                  continue;
                              }

                              switch (strtolower($metricUnit)) {
                                  case 'mm':
                                      $panjang /= 1000;
                                      $lebar   /= 1000;
                                      break;
                                  case 'cm':
                                      $panjang /= 100;
                                      $lebar   /= 100;
                                      break;
                                  case 'm':
                                  default:
                                      break;
                              }

                              $totalMetric += $panjang * $lebar * $jumlah;
                          }
                      }
                    @endphp

                    <h6 class="mb-1 text-dark fw-bold">
                      {{ $pel['nama'] }}
                    </h6>
                    @if(!empty($pel['email']))
                      <small class="text-muted">{{ $pel['email'] }}</small>
                    @endif
                    <div class="d-flex align-items-center gap-2 mt-2" style="width:160px;">
                      <div class="progress w-100" style="height:6px;">
                        <div class="progress-bar {{ $pelProgressColor }}"
                            role="progressbar"
                            style="width: {{ $pelProgressPct }}%;">
                        </div>
                      </div>
                      <span class="small fw-semibold text-muted">
                        {{ $pelProgressPct }}%
                      </span>
                    </div>
                  </div>
                  <div class="text-end">
                    <div class="badge bg-secondary mb-1">
                      {{ count($pel['spk']) }} SPK
                    </div>
                    @if($totalMetric > 0)
                      <div class="small text-muted">
                        Total: {{ number_format($totalMetric, 2, ',', '.') }} {{ strtolower($metricUnitLabel ?? 'cm') }}²
                      </div>
                    @endif
                  </div>
                </div>
              </button>
            </h2>

            <div id="pelanggan-collapse{{ $pelId }}" class="accordion-collapse collapse"
                aria-labelledby="pelanggan-heading{{ $pelId }}"
                data-bs-parent="#pelangganAccordion">
              <div class="accordion-body">
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Nomor SPK</th>
                        <th>Pelanggan</th>
                        <th class="text-center" style="width:50px;">File</th>
                        <th>Nama Item</th>
                        <th>Ukuran / Luas</th>
                        <th class="text-end">Jumlah</th>
                        <th>Satuan</th>
                        <th>Progress</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($pel['spk'] as $spkRow)
                        @php
                          $itemsForPelanggan = $spkRow->items
                            ->sortBy(function($spkItem) {
                                return strtolower($spkItem->nama_produk ?? '');
                            })
                            ->values();
                          $rowspan = $itemsForPelanggan->count();
                          $firstRow = true;
                        @endphp

                        @forelse($itemsForPelanggan as $spkItem)
                          @php
                            $produk = $spkItem->produk;
                            $isMetric   = $produk && ($produk->is_metric === true);
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
                                $dimensiText = '-';
                                $luasText = '';
                            }

                            $progressPct = (float) ($spkItem->progress_cetak_persen ?? 0);
                            $sisa = (int) ($spkItem->sisa_belum_cetak ?? 0);

                            $progressColor = $progressPct >= 100
                            ? 'bg-success'
                            : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');
                            @endphp

                          <tr>
                            @if($firstRow)
                              <td rowspan="{{ $rowspan }}" class="fw-bold align-top">
                                <div class="d-flex align-items-start gap-2">
                                  <div>
                                    {{ $spkRow->nomor_spk }}
                                    @php
                                        $spkDate = \Carbon\Carbon::parse($spkRow->tanggal_spk);
                                        $now = \Carbon\Carbon::now();
                                        $diff = $spkDate->diff($now);
                                        $isPast = $spkDate->isPast();
                                    @endphp
                                    <small class="text-muted d-block">
                                        {{ $spkDate->format('d/m/Y') }}
                                    </small>
    
                                    @if($isPast)
                                        <small class="text-muted d-block">
                                            {{ $diff->days }} hari
                                            @if($diff->h > 0)
                                                {{ $diff->h }} jam
                                            @endif
                                        </small>
                                    @endif
                                  </div>
                                  @php
                                      $groupDefaultFiles = [];

                                      foreach ($itemsForPelanggan as $it) {
                                          $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                          $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

                                          $default = (is_array($files) && count($files)) ? $files[0] : null;
                                          $path = $default['path'] ?? null;

                                          if (!$path) {
                                              continue;
                                          }
                                          $groupDefaultFiles[$path] = $default;
                                      }

                                      $groupDefaultFiles = array_values($groupDefaultFiles);
                                  @endphp
                                  
                                  @if(count($groupDefaultFiles))
                                    <button type="button"
                                            class="btn btn-sm btn-light p-1 ms-1 btn-preview-spk-files"
                                            data-spk-id="{{ $spkRow->id }}"
                                            data-spk-nomor="{{ $spkRow->nomor_spk }}"
                                            data-files='@json($groupDefaultFiles)'
                                            title="Lihat file default item (group ini)">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                  @endif
                                </div>
                              </td>
                              <td rowspan="{{ $rowspan }}" class="align-top">
                                {{ optional($spkRow->pelanggan)->nama ?? '-' }}
                              </td>
                              @php $firstRow = false; @endphp
                            @endif

                            @php
                                $filePendukungRaw = $spkItem->file_pendukung ?? $spkItem->file_pendukung_json ?? '[]';
                                if (is_string($filePendukungRaw)) {
                                    $filePendukung = json_decode($filePendukungRaw, true) ?: [];
                                } else {
                                    $filePendukung = (array) $filePendukungRaw;
                                }

                                $firstFile   = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;
                                $thumbUrl    = null;
                                $isPdfFirst  = false;

                                if ($firstFile && !empty($firstFile['path'])) {
                                    $thumbUrl   = route('backend.preview-file', ['path' => $firstFile['path']]);
                                    $isPdfFirst = strtolower((string) ($firstFile['type'] ?? '')) === 'pdf';
                                }
                            @endphp

                            <td class="text-center align-middle" style="width: 60px;">
                                @if($firstFile && $thumbUrl)
                                    <button type="button"
                                            class="btn btn-sm btn-light p-1 btn-preview-item-file"
                                            data-file-path="{{ $firstFile['path'] }}"
                                            data-file-name="{{ $firstFile['name'] ?? '' }}"
                                            data-file-type="{{ $firstFile['type'] ?? '' }}"
                                            title="Preview file item">
                                        @if($isPdfFirst)
                                            <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                        @else
                                            <img src="{{ $thumbUrl }}"
                                                alt="{{ $firstFile['name'] ?? 'Preview' }}"
                                                class="img-thumbnail"
                                                style="max-width: 40px; max-height: 40px; object-fit: cover; border-radius:4px;"
                                                loading="lazy">
                                        @endif
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $spkItem->nama_produk }}</td>
                            <td>
                              <div class="fw-semibold">{{ $dimensiText }}</div>
                              @if($luasText)
                                <div class="text-muted small">{{ $luasText }}</div>
                              @endif
                            </td>
                            <td class="text-end">{{ $spkItem->jumlah }}</td>
                            <td>{{ $spkItem->satuan }}</td>
                            <td class="text-end align-middle">
                              @if($sisa <= 0)
                                <span class="badge bg-success mb-2">
                                Selesai
                                </span>
                              @else
                              <div class="small text-danger fw-semibold mb-1">
                                Sisa: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                              </div>
                              @endif
                              <div class="progress" style="height:6px;">
                                <div class="progress-bar {{ $progressColor }}"
                                    role="progressbar"
                                    style="width: {{ $progressPct }}%;">
                                </div>
                              </div>
                              <div class="small">
                                {{ $progressPct }}%
                              </div>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="7" class="text-center text-muted">
                              Tidak ada item untuk pelanggan ini pada SPK ini.
                            </td>
                          </tr>
                        @endforelse
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            Tidak ada data SPK terkait pelanggan.
          </div>
        @endforelse
      </div>
    </div>

    {{-- TAB 5: Group per Produk --}}
    <div class="tab-pane fade" id="tabProduk" role="tabpanel" aria-labelledby="tab-produk">
      <div class="accordion" id="produkAccordion">
        @forelse($produkGroups as $produk)
          @php
            $produkIdSafe = $produk['id'] ?? md5(($produk['nama'] ?? 'produk').($produk['kode'] ?? ''));

            $totalQty = 0;
            $weightedProgress = 0;

            foreach ($produk['spk'] as $spkRow) {
                foreach ($spkRow->items as $spkItem) {
                    $match = false;
                    if (!empty($produk['id'])) {
                        $match = ((int)($spkItem->produk_id ?? 0) === (int)$produk['id']);
                    } else {
                        $match = (($spkItem->nama_produk ?? '') === ($produk['nama'] ?? ''));
                    }
                    if (!$match) continue;

                    $qty = (float) ($spkItem->jumlah ?? 0);
                    $pct = (float) ($spkItem->progress_cetak_persen ?? 0);

                    $totalQty += $qty;
                    $weightedProgress += ($qty * $pct);
                }
            }

            $produkProgressPct = $totalQty > 0
            ? round($weightedProgress / $totalQty)
            : 0;

            $produkProgressColor = $produkProgressPct >= 100
            ? 'bg-success'
            : ($produkProgressPct >= 50 ? 'bg-warning' : 'bg-primary');
          @endphp

          <div class="accordion-item">
            <h2 class="accordion-header" id="produk-heading{{ $produkIdSafe }}">
              <button class="accordion-button collapsed" type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#produk-collapse{{ $produkIdSafe }}"
                      aria-expanded="false"
                      aria-controls="produk-collapse{{ $produkIdSafe }}">

                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                  <div>
                    @php
                      // Total metric agregat untuk produk ini (mengikuti rumus tab lain: panjang x lebar x jumlah)
                      $totalMetric = 0;
                      $metricUnitLabel = 'm';

                      foreach ($produk['spk'] as $spkRow) {
                          foreach ($spkRow->items as $spkItem) {
                              $p = $spkItem->produk;
                              if (!$p) continue;

                              // filter item milik produk ini
                              $match = false;
                              if (!empty($produk['id'])) {
                                  $match = ((int) ($spkItem->produk_id ?? 0) === (int) $produk['id']);
                              } else {
                                  $match = (($spkItem->nama_produk ?? '') === ($produk['nama'] ?? ''));
                              }
                              if (!$match) continue;

                              if ($p->is_metric !== true) continue;

                              $metricUnit = $p->metric_unit ?: 'cm';
                              $panjang = (float) ($spkItem->panjang ?? 0);
                              $lebar   = (float) ($spkItem->lebar ?? 0);
                              $jumlah  = (float) ($spkItem->jumlah ?? 0);

                              if ($panjang <= 0 || $lebar <= 0 || $jumlah <= 0) continue;

                              switch (strtolower($metricUnit)) {
                                  case 'cm':
                                      $panjang /= 100;
                                      $lebar   /= 100;
                                      break;
                                  case 'mm':
                                      $panjang /= 1000;
                                      $lebar   /= 1000;
                                      break;
                                  case 'm':
                                  default:
                                      break;
                              }

                              $totalMetric += $panjang * $lebar * $jumlah;
                          }
                      }
                    @endphp

                    <h6 class="mb-1 text-dark fw-bold">
                      {{ $produk['nama'] }}
                    </h6>
                    @if(!empty($produk['kode']))
                      <small class="text-muted">{{ $produk['kode'] }}</small>
                    @endif
                    <div class="d-flex align-items-center gap-2 mt-2" style="width:160px;">
                      <div class="progress w-100" style="height:6px;">
                        <div class="progress-bar {{ $produkProgressColor }}"
                        role="progressbar"
                        style="width: {{ $produkProgressPct }}%;">
                        </div>
                      </div>

                      <span class="small fw-semibold text-muted">
                      {{ $produkProgressPct }}%
                      </span>
                      </div>
                    </div>

                  <div class="text-end">
                    <div class="badge bg-secondary mb-1">
                      {{ count($produk['spk']) }} SPK
                    </div>
                    @if($totalMetric > 0)
                      <div class="small text-muted">
                        Total: {{ number_format($totalMetric, 2, ',', '.') }} {{ strtolower($metricUnitLabel ?? 'cm') }}²
                      </div>
                    @endif
                  </div>
                </div>

              </button>
            </h2>

            <div id="produk-collapse{{ $produkIdSafe }}" class="accordion-collapse collapse"
                aria-labelledby="produk-heading{{ $produkIdSafe }}"
                data-bs-parent="#produkAccordion">
              <div class="accordion-body">
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Nomor SPK</th>
                        <th>Pelanggan</th>
                        <th class="text-center" style="width:50px;">File</th>
                        <th>Nama Item</th>
                        <th>Ukuran / Luas</th>
                        <th class="text-end">Jumlah</th>
                        <th>Satuan</th>
                        <th>Progress</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($produk['spk'] as $spkRow)
                        @php
                          $itemsForProduk = $spkRow->items->filter(function($spkItem) use ($produk) {
                              if (!empty($produk['id'])) {
                                  return ((int) ($spkItem->produk_id ?? 0) === (int) $produk['id']);
                              }
                              return (($spkItem->nama_produk ?? '') === ($produk['nama'] ?? ''));
                          });

                          $rowspan = $itemsForProduk->count();
                          $firstRow = true;
                        @endphp

                        @forelse($itemsForProduk as $spkItem)
                          @php
                            $p = $spkItem->produk;
                            $isMetric   = $p && ($p->is_metric === true);
                            $metricUnit = $p && $p->metric_unit ? $p->metric_unit : 'cm';

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
                                $dimensiText = '-';
                                $luasText = '';
                            }
                          @endphp

                          <tr>
                            @if($firstRow)
                              <td rowspan="{{ $rowspan }}" class="fw-bold align-top">
                                <div class="d-flex align-items-start gap-2">
                                  <div>
                                    {{ $spkRow->nomor_spk }}
                                    @php
                                        $spkDate = \Carbon\Carbon::parse($spkRow->tanggal_spk);
                                        $now = \Carbon\Carbon::now();
                                        $diff = $spkDate->diff($now);
                                        $isPast = $spkDate->isPast();
                                    @endphp
                                    <small class="text-muted d-block">
                                        {{ $spkDate->format('d/m/Y') }}
                                    </small>
    
                                    @if($isPast)
                                        <small class="text-muted d-block">
                                            {{ $diff->days }} hari
                                            @if($diff->h > 0)
                                                {{ $diff->h }} jam
                                            @endif
                                        </small>
                                    @endif
                                  </div>
                                  @php
                                      $groupDefaultFiles = [];

                                      foreach ($itemsForProduk as $it) {
                                          $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                          $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

                                          $default = (is_array($files) && count($files)) ? $files[0] : null;
                                          $path = $default['path'] ?? null;

                                          if (!$path) {
                                              continue;
                                          }

                                          $groupDefaultFiles[$path] = $default;
                                      }

                                      $groupDefaultFiles = array_values($groupDefaultFiles);
                                  @endphp

                                  @if(count($groupDefaultFiles))
                                    <button type="button"
                                            class="btn btn-sm btn-light p-1 ms-1 btn-preview-spk-files"
                                            data-spk-id="{{ $spkRow->id }}"
                                            data-spk-nomor="{{ $spkRow->nomor_spk }}"
                                            data-files='@json($groupDefaultFiles)'
                                            title="Lihat file default item (group ini)">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                  @endif
                                </div>
                              </td>
                              <td rowspan="{{ $rowspan }}" class="align-top">
                                {{ optional($spkRow->pelanggan)->nama ?? '-' }}
                              </td>
                              @php $firstRow = false; @endphp
                            @endif

                            @php
                                $filePendukungRaw = $spkItem->file_pendukung ?? $spkItem->file_pendukung_json ?? '[]';
                                if (is_string($filePendukungRaw)) {
                                    $filePendukung = json_decode($filePendukungRaw, true) ?: [];
                                } else {
                                    $filePendukung = (array) $filePendukungRaw;
                                }

                                $firstFile   = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;
                                $thumbUrl    = null;
                                $isPdfFirst  = false;

                                if ($firstFile && !empty($firstFile['path'])) {
                                    $thumbUrl   = route('backend.preview-file', ['path' => $firstFile['path']]);
                                    $isPdfFirst = strtolower((string) ($firstFile['type'] ?? '')) === 'pdf';
                                }

                                $progressPct = (float) ($spkItem->progress_cetak_persen ?? 0);
                                $sisa = (int) ($spkItem->sisa_belum_cetak ?? 0);

                                $progressColor = $progressPct >= 100
                                ? 'bg-success'
                                : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');
                            @endphp

                            <td class="text-center align-middle" style="width: 60px;">
                                @if($firstFile && $thumbUrl)
                                    <button type="button"
                                            class="btn btn-sm btn-light p-1 btn-preview-item-file"
                                            data-file-path="{{ $firstFile['path'] }}"
                                            data-file-name="{{ $firstFile['name'] ?? '' }}"
                                            data-file-type="{{ $firstFile['type'] ?? '' }}"
                                            title="Preview file item">
                                        @if($isPdfFirst)
                                            <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                        @else
                                            <img src="{{ $thumbUrl }}"
                                                alt="{{ $firstFile['name'] ?? 'Preview' }}"
                                                class="img-thumbnail"
                                                style="max-width: 40px; max-height: 40px; object-fit: cover; border-radius:4px;"
                                                loading="lazy">
                                        @endif
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $spkItem->nama_produk }}</td>
                            <td>
                              <div class="fw-semibold">{{ $dimensiText }}</div>
                              @if($luasText)
                                <div class="text-muted small">{{ $luasText }}</div>
                              @endif
                            </td>
                            <td class="text-end">{{ $spkItem->jumlah }}</td>
                            <td>{{ $spkItem->satuan }}</td>
                            <td class="text-end align-middle">
                              @if($sisa <= 0)
                                <span class="badge bg-success mb-2">
                                Selesai
                                </span>
                              @else
                              <div class="small text-danger fw-semibold mb-1">
                                Sisa: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                              </div>
                              @endif

                              <div class="progress" style="height:6px;">
                                <div class="progress-bar {{ $progressColor }}"
                                    role="progressbar"
                                    style="width: {{ $progressPct }}%;">
                                </div>
                              </div>
                              <div class="small">
                              {{ $progressPct }}%
                              </div>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="7" class="text-center text-muted">
                              Tidak ada item untuk produk ini pada SPK ini.
                            </td>
                          </tr>
                        @endforelse
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            Tidak ada data SPK terkait produk.
          </div>
        @endforelse
      </div>
    </div>
  </div>
@endsection

@push('custom-scripts')
  <style>
  .tab-card {
    transition: all 0.25s ease;
    border-radius: 14px;
    background: #fff;
  }

  .tab-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  }

  .tab-card.active {
      border: 2px solid #0d6efd;
      background: linear-gradient(145deg, #f8fbff, #eef5ff);
  }
  
  .tab-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .status-card.status-active {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.08); 
    box-shadow: 0 0 0 1px rgba(13, 110, 253, 0.4);
  }
  </style>

  <script>
    // Initialize Feather Icons
    feather.replace();

    // Search form handling
    const searchForm = $('#searchForm');
    const loadingSpinner = $('#loadingSpinner');
    const tableContainer = $('.table-responsive');

    // Manual submit for search input
    $('input[name="search"]').on('keypress', function(e) {
      if (e.which === 13) {
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
        window.location.href = '{{ route("pekerjaan.manager-order") }}';
    });

    // Clear search
    $('#clearSearch').click(function() {
        window.location.href = '{{ route("pekerjaan.manager-order") }}';
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
        text: 'Pilih Setuju untuk lanjut ke Operator Cetak, atau Tolak untuk kembali ke Draft.',
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

  <script>
  document.querySelectorAll('#managerOrderTabs button').forEach(btn => {
      btn.addEventListener('shown.bs.tab', function () {

          document.querySelectorAll('.tab-card').forEach(el => {
              el.classList.remove('active');
          });

          this.classList.add('active');
      });
  });

  (function () {
    function setProgressBar(el, pct, colorClass) {
      if (!el) return;
      const n = Math.max(0, Math.min(100, Number(pct || 0)));
      el.style.width = `${n}%`;
      el.setAttribute('aria-valuenow', String(n));

      if (colorClass) {
        el.classList.remove('bg-success', 'bg-warning', 'bg-primary', 'bg-danger', 'bg-secondary');
        el.classList.add(colorClass);
      }
    }

    function setText(el, text) {
      if (!el) return;
      el.textContent = text;
    }

    function handleSpkUpdated(e) {
      const spkId = e.spk_id;
      if (!spkId) return;

      const pct = e.spk_progress_pct ?? 0;
      const color = e.spk_progress_color;

      const bar = document.querySelector(`[data-field="spk-progress-bar"][data-spk-id="${spkId}"]`);
      const pctEl = document.querySelector(`[data-field="spk-progress-pct"][data-spk-id="${spkId}"]`);

      setProgressBar(bar, pct, color);
      setText(pctEl, `${Math.round(Number(pct || 0))}%`);
    }


    function handleSpkItemUpdated(e) {
      const itemId = e.spk_item_id;
      if (!itemId) return;

      const pct = e.progress_pct ?? 0;
      const color = e.progress_color;

      const bar = document.querySelector(`[data-field="spk-item-progress-bar"][data-spk-item-id="${itemId}"]`);
      const pctEl = document.querySelector(`[data-field="spk-item-progress-pct"][data-spk-item-id="${itemId}"]`);

      setProgressBar(bar, pct, color);
      setText(pctEl, `${Number(pct || 0)}%`);

      const remainingEl = document.querySelector(`[data-field="spk-item-remaining"][data-spk-item-id="${itemId}"]`);
      const doneBadgeEl = document.querySelector(`[data-field="spk-item-done-badge"][data-spk-item-id="${itemId}"]`);

      const isDone = !!e.is_done;
      if (isDone) {
        if (remainingEl) remainingEl.remove();          
        if (!doneBadgeEl) {
          const cell = document.querySelector(`[data-field="spk-item-progress-cell"][data-spk-item-id="${itemId}"]`);
          if (cell) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-success mb-2';
            badge.setAttribute('data-field', 'spk-item-done-badge');
            badge.setAttribute('data-spk-item-id', String(itemId));
            badge.textContent = 'Selesai';
            cell.prepend(badge);
          }
        }
      } else {
        const remaining = e.remaining ?? 0;
        const satuan = e.satuan ?? '';
        if (doneBadgeEl) doneBadgeEl.remove();

        if (remainingEl) {
          remainingEl.textContent = `Sisa: ${Number(remaining).toLocaleString('id-ID')} ${satuan}`.trim();
        } else {
          const cell = document.querySelector(`[data-field="spk-item-progress-cell"][data-spk-item-id="${itemId}"]`);
          if (cell) {
            const div = document.createElement('div');
            div.className = 'small text-danger fw-semibold mb-1';
            div.setAttribute('data-field', 'spk-item-remaining');
            div.setAttribute('data-spk-item-id', String(itemId));
            div.textContent = `Sisa: ${Number(remaining).toLocaleString('id-ID')} ${satuan}`.trim();
            cell.prepend(div);
          }
        }
      }
    }

    if (!window.Echo) return;

    window.Echo.channel('manager-order')
      .listen('.spk.updated', handleSpkUpdated)
      .listen('.spk.item.updated', handleSpkItemUpdated);
  })();
  </script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
      const modalEl   = document.getElementById('globalPreviewModal');
      const modalTitle = document.getElementById('globalPreviewModalTitle');
      const modalBody  = document.getElementById('globalPreviewModalBody');
      const globalModal = new bootstrap.Modal(modalEl);

      document.querySelectorAll('.btn-preview-spk-files').forEach(function (btn) {
          btn.addEventListener('click', function () {
              const spkNomor = btn.getAttribute('data-spk-nomor') || '';
              const filesJson = btn.getAttribute('data-files') || '[]';
              let files = [];

              try {
                  files = JSON.parse(filesJson);
              } catch (e) {
                  files = [];
              }

              modalTitle.textContent = 'Preview File SPK ' + spkNomor;

              if (!files.length) {
                  modalBody.innerHTML = '<div class="text-center text-muted py-4">Tidak ada file pendukung.</div>';
                  globalModal.show();
                  return;
              }

              let html = '<div class="row g-3">';
              files.forEach(function (f) {
                  const path = f.path || '';
                  if (!path) return;
                  const name = f.name || path.split(/[\\/]/).pop();
                  const type = (f.type || '').toLowerCase();
                  const isPdf = (type === 'pdf');
                  const url = "{{ route('backend.preview-file') }}" + '?path=' + encodeURIComponent(path);

                  html += '<div class="col-md-4">';
                  html += '  <div class="border rounded p-2 h-100">';
                  html += '    <div class="small fw-semibold text-truncate" title="' + name.replace(/"/g, '&quot;') + '">' + name + '</div>';
                  html += '    <div class="mt-2">';
                  if (isPdf) {
                      html += '      <a href="' + url + '" target="_blank" class="btn btn-sm btn-outline-danger w-100">';
                      html += '        <i class="fa fa-file-pdf"></i> Buka PDF';
                      html += '      </a>';
                  } else {
                      html += '      <a href="' + url + '" target="_blank" class="d-block">';
                      html += '        <img src="' + url + '" alt="' + name.replace(/"/g, '&quot;') + '"';
                      html += '             class="img-fluid rounded" loading="lazy"';
                      html += '             style="max-height:180px;object-fit:cover;width:100%;">';
                      html += '      </a>';
                  }
                  html += '    </div>';
                  html += '  </div>';
                  html += '</div>';
              });
              html += '</div>';

              modalBody.innerHTML = html;
              globalModal.show();
          });
      });

      document.querySelectorAll('.btn-preview-item-file').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const path = btn.getAttribute('data-file-path') || '';
            if (!path) return;

            const name = btn.getAttribute('data-file-name') || 'Preview';
            const type = (btn.getAttribute('data-file-type') || '').toLowerCase();
            const url  = "{{ route('backend.preview-file') }}" + '?path=' + encodeURIComponent(path);

            modalTitle.textContent = 'Preview File Item: ' + name;

            if (type === 'pdf') {
                modalBody.innerHTML = `<iframe src="${url}#toolbar=0" style="width:100%;height:400px;border:0;" title="Preview PDF"></iframe>`;
            } else {
                modalBody.innerHTML = `<img src="${url}" alt="${name.replace(/"/g, '&quot;')}" class="img-fluid rounded" style="max-height:400px;object-fit:contain;" loading="lazy">`;
            }

            globalModal.show();
        });
      });

      let isInitialLoadStatus = true;
      function applyStatusFilter(statusValue) {
        document.querySelectorAll('.filter-status-card').forEach(function (c) {
          c.classList.toggle(
            'status-active',
            (c.getAttribute('data-status-value') || '') === statusValue
          );
        });
        document.querySelectorAll('#accordionSpk .spk-row').forEach(function (tr) {
          var rowStatus = tr.getAttribute('data-status') || '';
          tr.style.display = (statusValue === '' || rowStatus === statusValue) ? '' : 'none';
        });
        var visibleCount = 0;
        document.querySelectorAll('#accordionSpk .spk-row').forEach(function (tr) {
          if (tr.style.display !== 'none') visibleCount++;
        });
        var emptyRow = document.getElementById('clientFilterEmptyRow');
        if (emptyRow) emptyRow.classList.toggle('d-none', visibleCount > 0);
        var clearStatusBtn = document.getElementById('clearStatusFilter');
        if (clearStatusBtn) clearStatusBtn.addEventListener('click', function () {
          applyStatusFilter('');
        });
        var statusInput = document.getElementById('statusFilterInput');
        if (statusInput) statusInput.value = statusValue;
        var params = new URLSearchParams(window.location.search);
        if (statusValue) params.set('status', statusValue);
        else params.delete('status');
        
        if (!isInitialLoadStatus) {
          params.delete('spk_id');
        }
        window.history.replaceState(
          {},
          '',
          window.location.pathname + (params.toString() ? '?' + params.toString() : '')
        );
      }

      document.querySelectorAll('.filter-status-card').forEach(function (card) {
        card.addEventListener('click', function () {
          var statusValue = this.getAttribute('data-status-value') || '';
          applyStatusFilter(statusValue);
        });
      });
      var initialStatus = (new URLSearchParams(window.location.search)).get('status') || '';
      applyStatusFilter(initialStatus);
     
      isInitialLoadStatus = false;
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const accordionParams = ['spk_id', 'produk_id', 'bahan_id', 'mesin_id', 'pelanggan_id'];

    let isInitialLoad = true;

    function updateUrl() {
      const qs = params.toString();
      const url = window.location.pathname + (qs ? '?' + qs : '');
      window.history.replaceState({}, '', url);
    }

    const tabButtons = document.querySelectorAll('#managerOrderTabs button[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
      btn.addEventListener('shown.bs.tab', function () {
        const rawId = this.id || '';
        const tabKey = rawId.replace('tab-', '');
        if (!tabKey) return;

        params.set('tab', tabKey);

        if (tabKey !== 'spk-list') {
          params.delete('status');
        }

        if (!isInitialLoad) {
          accordionParams.forEach(p => params.delete(p));
        }

        updateUrl();
        document.querySelectorAll('.tab-card').forEach(el => el.classList.remove('active'));
        this.classList.add('active');
      });
    });

    (function activateInitialTab() {
    const tabFromUrl = params.get('tab') || 'spk-list';
    const btn = document.querySelector(`#managerOrderTabs button#tab-${tabFromUrl}`);
    if (!btn) return;

    if (tabFromUrl !== 'spk-list' && params.has('status')) {
      params.delete('status');
    }

    params.set('tab', tabFromUrl);
    updateUrl();

    if (window.bootstrap && bootstrap.Tab) {
      new bootstrap.Tab(btn).show();
    } else {
      btn.classList.add('active');
      document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
      });

      const target = btn.getAttribute('data-bs-target');
      if (target) {
        const pane = document.querySelector(target);
        if (pane) {
          pane.classList.add('show', 'active');
        }
      }
    }

    document.querySelectorAll('.tab-card').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    })();

    function rememberAccordionState(containerSelector, paramName, idPrefix) {
      const container = document.querySelector(containerSelector);
      if (!container) return;

      container.querySelectorAll('.accordion-collapse, .spk-collapse').forEach(el => {
        el.addEventListener('show.bs.collapse', function () {
          const fullId = this.id || '';
          if (!fullId.startsWith(idPrefix)) return;

          const rawId = fullId.substring(idPrefix.length);
          if (!rawId) return;

          // SELALU baca query terkini dari URL, supaya tab/status tidak hilang
          const currentParams = new URLSearchParams(window.location.search);

          const accordionParams = ['spk_id', 'produk_id', 'bahan_id', 'mesin_id', 'pelanggan_id'];

          // hapus param accordion lain
          accordionParams.forEach(p => {
            if (p !== paramName) {
              currentParams.delete(p);
            }
          });

          // set hanya satu (mis. spk_id=39)
          currentParams.set(paramName, rawId);

          const qs = currentParams.toString();
          const url = window.location.pathname + (qs ? '?' + qs : '');
          window.history.replaceState({}, '', url);
        });
      });

      const savedId = params.get(paramName);
      if (!savedId) return;

      const targetId = idPrefix + savedId;
      const collapseEl = document.getElementById(targetId);
      if (!collapseEl) return;

      if (window.bootstrap && bootstrap.Collapse) {
        new bootstrap.Collapse(collapseEl, { toggle: true });
      } else {
        collapseEl.classList.add('show');
      }
    }

    setTimeout(() => {
      rememberAccordionState('#accordionSpk', 'spk_id', 'detail-spk-');
      rememberAccordionState('#produkAccordion', 'produk_id', 'produk-collapse');
      rememberAccordionState('#bahanAccordion', 'bahan_id', 'bahan-collapse');
      rememberAccordionState('#mesinAccordion', 'mesin_id', 'mesin-collapse');
      rememberAccordionState('#pelangganAccordion', 'pelanggan_id', 'pelanggan-collapse');

      isInitialLoad = false;
    }, 100);
  });
</script>



<div class="modal fade" id="globalPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title mb-0" id="globalPreviewModalTitle">Preview File</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="globalPreviewModalBody">
        {{-- diisi via JS --}}
      </div>
    </div>
  </div>
</div>
@endpush