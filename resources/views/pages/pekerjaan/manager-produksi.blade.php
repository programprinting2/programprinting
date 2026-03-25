@extends('layout.master')

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Pekerjaan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Manager Produksi</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
    @php
      $activeTab = old('tab', $activeTab ?? request('tab', 'data-pekerjaan'));
      $selectedUserId = (int) old('user_id', $selectedUserId ?? 0);
      $selectedUserMesinIds = collect(old('mesin_ids', $selectedUserMesinIds ?? []))
        ->map(fn ($id) => (int) $id)
        ->all();
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="row">
            <h6 class="card-title mb-0">Data Pekerjaan Manager Produksi</h6>
            <p class="text-muted mb-3">Daftar semua SPK yang perlu diproses oleh Manager Produksi.</p>
    </div>
          </div>

          <ul class="nav nav-tabs mb-3" id="managerProduksiTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link {{ $activeTab === 'data-pekerjaan' ? 'active' : '' }}"
                type="button"
                data-target-pane="data-pekerjaan-pane"
              >
                Data Pekerjaan
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link {{ $activeTab === 'assignment-role-mesin' ? 'active' : '' }}"
                type="button"
                data-target-pane="assignment-role-mesin-pane"
              >
                Assignment Role Mesin
              </button>
            </li>
          </ul>

          <div id="data-pekerjaan-pane" class="{{ $activeTab === 'assignment-role-mesin' ? 'd-none' : '' }}">

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
            <!-- <div class="col-md-4">
              <label class="form-label small">&nbsp;</label>
              <select class="form-select" name="customer_id">
                <option value="">Semua Pelanggan</option>
                @foreach($customers ?? [] as $customer)
                  <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->nama }}
                  </option>
                @endforeach
              </select>
            </div> -->
            <div class="col-md-4">
              <label class="form-label small">Status</label>
              <select class="form-select" name="status">
                  <option value="">Semua Status</option>
                  <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                  <option value="proses_bayar" {{ request('status') == 'proses_bayar' ? 'selected' : '' }}>Proses Pembayaran</option>
                  <option value="manager_approval_order" {{ request('status') == 'manager_approval_order' ? 'selected' : '' }}>Manager Approval Order</option>
                  <option value="operator_cetak" {{ request('status') == 'operator_cetak' ? 'selected' : '' }}>Operator Cetak</option>
                  <option value="finishing_qc" {{ request('status') == 'finishing_qc' ? 'selected' : '' }}>Finishing / QC</option>
                  <option value="siap_diambil" {{ request('status') == 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                  <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                  <th>Aksi</th>
      </tr>
      </thead>
      <tbody id="accordionSpk">
      @forelse($spk as $item)
        @php
            $collapseId = 'detail-spk-'.$item->id;
        @endphp
      <tr>
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
                                'draft'                     => 1,
                                'proses_bayar'              => 2,
                                'manager_approval_order'    => 3,
                                'operator_cetak'            => 4,
                                'finishing_qc'              => 5,
                                'siap_diambil'              => 6,
                                'selesai'                   => 7,
                            ];

                            $statusLabels = [
                                'draft'                     => 'Draft',
                                'proses_bayar'              => 'Proses Pembayaran',
                                'manager_approval_order'    => 'Manager Approval Order',
                                'operator_cetak'            => 'Operator Cetak',
                                'finishing_qc'              => 'Finishing / QC',
                                'siap_diambil'              => 'Siap Diambil',
                                'selesai'                   => 'Selesai',
                            ];

                            $statusIcons = [
                                'draft'                     => 'fa-file-alt',
                                'proses_bayar'              => 'fas fa-cash-register',
                                'manager_approval_order'    => 'fas fa-chalkboard-teacher',
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
                                          $colorClass = 'text-secondary';  
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
      </tr>
      <!-- Collapse Row -->
      <tr>
                <td colspan="8" class="p-0 border-0">
                    <div id="{{ $collapseId }}" class="collapse spk-collapse" data-bs-parent="#accordionSpk">
                        <div class="border-2 px-4 py-3">
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
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>File</th>
                                            <th>Keterangan</th>
                                            <th>Nama Item</th>
                                            <th>Ukuran / Luas</th>
                                            <th class="text-end">Jumlah</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody style="background-color: #ffffff;">
                                      @php
                                        $sortedItems = $item->items->sortBy(function ($it) {
                                            return optional($it->produk)->kode_produk ?? $it->nama_produk ?? '';
                                        });
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
                                                $filePath = $defaultFile['path'];
                                                $isPdf = strtolower((string) ($defaultFile['type'] ?? '')) === 'pdf';
                                                $previewUrl = route('backend.preview-file', ['path' => $filePath]);
                                              @endphp

                                              @if($isPdf)
                                                <a href="{{ $previewUrl }}" target="_blank" title="Preview PDF">
                                                  <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                                </a>
                                              @else
                                                <a href="{{ $previewUrl }}" target="_blank" title="{{ $defaultFile['name'] ?? 'Preview' }}">
                                                  <img src="{{ $previewUrl }}"
                                                    alt="{{ $defaultFile['name'] ?? 'Preview' }}"
                                                    class="img-thumbnail"
                                                    style="max-width: 50px; max-height: 50px; object-fit: cover; border-radius:4px;"
                                                    loading="lazy">
                                                </a>
                                            @endif
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
                              $grouped = $item->items->groupBy(fn($it) => $it->produk_id ?: $it->nama_produk)->sortBy(fn($group) => optional($group->first()->produk)->kode_produk ?? $group->first()->nama_produk ?? '');
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
                    <td colspan="7" class="text-center py-4">
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

  <div id="assignment-role-mesin-pane" class="{{ $activeTab === 'assignment-role-mesin' ? '' : 'd-none' }}">

    <hr class="my-4">

    <div class="card border-0 mb-3">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

          <div>
            <h5 class="mb-1 fw-semibold">List User & Assignment Role Mesin</h5>
            <div class="text-muted small">
              Role mesin adalah daftar mesin yang boleh diakses oleh masing-masing user.
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card border">
      <div class="card-body p-0">

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
              <tr>
                <th style="width: 220px;">User</th>
                <th>Email</th>
                <th>Role Mesin</th>
                <th class="text-center" style="width: 160px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse(($users ?? collect()) as $u)
              <tr class="row-user-assignment" data-user-id="{{ $u->id }}" style="cursor:pointer;">
                
                <td class="fw-semibold">
                  {{ $u->name }}
                </td>

                <td class="text-muted">
                  {{ $u->email }}
                </td>

                <td>
                  @if(($u->mesins ?? collect())->isEmpty())
                    <span class="badge bg-light text-muted border">Belum ada mesin</span>
                  @else
                    <div class="d-flex flex-wrap gap-1">
                      @foreach($u->mesins as $mesin)
                        <span class="badge bg-primary-subtle text-primary border">
                          {{ $mesin->nama_mesin }}
                        </span>
                      @endforeach
                    </div>
                  @endif
                </td>

                <td class="text-center text-muted small">
                  Klik untuk atur mesin
                </td>

              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                  Tidak ada user
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
      </div>
    </div>
  </div>


<div class="modal fade" id="assignmentRoleModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Assignment Role Mesin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body position-relative">

        <div id="modalLoading"
          class="position-absolute top-0 start-0 w-100 h-100 bg-white d-flex flex-column justify-content-center align-items-center d-none"
          style="z-index:10;">
          <div class="spinner-border text-primary"></div>
          <div class="mt-2 text-muted">Memuat data...</div>
        </div>

        <form method="POST"
          action="{{ route('pekerjaan.manager-produksi.mesin-roles.save') }}"
          id="formAssignment">

          @csrf
          <input type="hidden" name="user_id" id="modalUserId">
          <div class="card border mb-3">
            <div class="card-header bg-light">
              <span class="fw-semibold">Daftar Mesin</span>
            </div>

            <div class="card-body">
              <div class="row g-2">
                @foreach(($allMesin ?? []) as $mesin)
                <div class="col-md-4">
                  <label class="border rounded p-2 w-100 d-flex gap-2">
                    <input class="form-check-input"
                      type="checkbox"
                      name="mesin_ids[]"
                      value="{{ $mesin->id }}">
                    <span>
                      <span class="fw-semibold d-block">{{ $mesin->nama_mesin }}</span>
                      <small class="text-muted">{{ $mesin->tipe_mesin ?: '-' }}</small>
                    </span>
                  </label>
                </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary" id="btnSubmitAssignment">
              <span class="default-text">Simpan</span>
              <span class="loading-text d-none">
                <span class="spinner-border spinner-border-sm"></span> Menyimpan...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
  <script>
    // Initialize Feather Icons
    feather.replace();

    const paneDataPekerjaan = document.getElementById('data-pekerjaan-pane');
    const paneAssignmentRole = document.getElementById('assignment-role-mesin-pane');
    const tabButtons = document.querySelectorAll('#managerProduksiTabs .nav-link');
    tabButtons.forEach((btn) => {
      btn.addEventListener('click', function () {
        tabButtons.forEach((b) => b.classList.remove('active'));
        this.classList.add('active');

        const targetPane = this.getAttribute('data-target-pane');
        if (paneDataPekerjaan) {
          paneDataPekerjaan.classList.toggle('d-none', targetPane !== 'data-pekerjaan-pane');
        }
        if (paneAssignmentRole) {
          paneAssignmentRole.classList.toggle('d-none', targetPane !== 'assignment-role-mesin-pane');
        }
      });
    });

    // @if(session('success') && request('tab') === 'assignment-role-mesin')
    //   const modal = new bootstrap.Modal(document.getElementById('assignmentRoleModal'));
    //   modal.show();
    // @endif

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
        window.location.href = '{{ route("pekerjaan.manager-produksi") }}';
    });

    // Clear search
    $('#clearSearch').click(function() {
        window.location.href = '{{ route("pekerjaan.manager-produksi") }}';
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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modalEl = document.getElementById('assignmentRoleModal');
      const modal = new bootstrap.Modal(modalEl);

      const modalLoading = document.getElementById('modalLoading');
      const userIdInput = document.getElementById('modalUserId');

      function setLoading(state) {
        modalLoading.classList.toggle('d-none', !state);
      }

      function resetCheckbox() {
        document.querySelectorAll('input[name="mesin_ids[]"]').forEach(cb => {
          cb.checked = false;
        });
      }

      document.querySelectorAll('.row-user-assignment').forEach(row => {
        row.addEventListener('click', async () => {
          const userId = row.dataset.userId;
          if (!userId) return;

          userIdInput.value = userId;
          resetCheckbox();
          setLoading(true);
          modal.show();

          try {
            const url = `{{ route('pekerjaan.manager-produksi.mesin-ids', ['userId' => '__ID__']) }}`
              .replace('__ID__', userId);

            const res = await fetch(url, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();

            if (!data.success) throw new Error();

            const assigned = new Set(data.mesin_ids.map(String));

            document.querySelectorAll('input[name="mesin_ids[]"]').forEach(cb => {
              cb.checked = assigned.has(cb.value);
            });

          } catch (err) {
            Swal.fire('Error', 'Gagal load data mesin', 'error');
          } finally {
            setLoading(false);
          }
        });
      });

      document.getElementById('formAssignment').addEventListener('submit', function () {
        const btn = document.getElementById('btnSubmitAssignment');
        btn.disabled = true;
        btn.querySelector('.default-text').classList.add('d-none');
        btn.querySelector('.loading-text').classList.remove('d-none');
      });
    });
  </script>
@endpush