@extends('layout.master')

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Pekerjaan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Operator Cetak</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="row">
            <h6 class="card-title mb-0">Data Pekerjaan Operator Cetak</h6>
            <p class="text-muted mb-3">Daftar semua SPK yang perlu diproses oleh Operator Cetak.</p>
    </div>
          </div>
          <div class="row g-3 mb-4" id="operatorTabs" role="tablist">
            <!-- <div class="col-md-3">
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
                  <div>
                    <h6 class="mb-1 fw-semibold">Daftar SPK</h6>
                    <small class="text-muted">List semua SPK</small>
                  </div>
                </div>
              </button>
            </div> -->

            @foreach($tipeMesinGroups as $tipe => $group)
                @php
                    $slug = \Illuminate\Support\Str::slug($tipe, '-');
                    $label = $group['label'] ?? $tipe;
                @endphp
                <div class="col-md-3">
                    <button class="card tab-card w-100 text-start {{ $loop->first ? 'active' : '' }}"
                            id="tab-tipe-{{ $slug }}"
                            data-bs-toggle="tab"
                            data-bs-target="#tabTipe{{ $slug }}"
                            type="button"
                            role="tab">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="tab-icon bg-success-subtle text-success">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-semibold">{{ $label }}</h6>
                                    <span class="badge bg-success rounded-pill px-3">
                                        {{ count($group['spk']) }}
                                    </span>
                                </div>
                                <small class="text-muted">Rekap per bahan</small>
                            </div>
                        </div>
                    </button>
                </div>
            @endforeach
          </div>

          <!-- Divider -->
          <hr class="my-4">

          
    
    <div class="tab-content mt-3" id="operatorTabContent">
        {{-- TAB 2: Per Tipe Mesin --}}
        @foreach($tipeMesinGroups as $tipe => $group)
          @php
            $slug = \Illuminate\Support\Str::slug($tipe, '-');
            $accordionIdBase = 'accordionTipe'.$slug;
          @endphp
          <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tabTipe{{ $slug }}"
              role="tabpanel"
              aria-labelledby="tab-tipe-{{ $slug }}">
            <div class="accordion" id="{{ $accordionIdBase }}">
              @php
                  /** @var array $group */
                  $bahanGroups = $group['bahanGroups'] ?? [];
              @endphp

              @forelse($bahanGroups as $bahan)
                @php
                  $accordionId = ($accordionIdBase ?? 'operatorTipe').'-bahan-'.$bahan['id'];
                  $items = array_values($bahan['items'] ?? []);
                @endphp

                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $accordionId }}"
                            aria-expanded="false"
                            aria-controls="collapse-{{ $accordionId }}">
                      <div class="d-flex justify-content-between align-items-center w-100 me-3">
                        <div class="flex-grow-1">
                          @php
                            // Hitung total metric untuk bahan ini (di semua item yang tercatat)
                            $totalMetric = 0;
                            $metricUnitLabel = 'm';

                            foreach ($items as $rec) {
                                /** @var \App\Models\SPK $spkRow */
                                /** @var \App\Models\SPKItem $spkItem */
                                $spkRow = $rec['spk'];
                                $spkItem = $rec['item'];

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
                                        $panjang /= 1000; $lebar /= 1000; break;
                                    case 'cm':
                                        $panjang /= 100;  $lebar /= 100;  break;
                                    case 'm':
                                    default:
                                        break;
                                }

                                $totalMetric += $panjang * $lebar * $jumlah;
                            }
                          @endphp

                          <h6 class="mb-1 text-dark fw-bold">{{ $bahan['nama'] }}</h6>
                          @if(!empty($bahan['kode']))
                            <small class="text-muted">{{ $bahan['kode'] }}</small>
                          @endif
                        </div>
                        <div class="text-end">
                          <div class="badge bg-secondary text-white mb-1">
                            {{ count($items) }} item
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

                  <div id="collapse-{{ $accordionId }}" class="accordion-collapse collapse"
                      aria-labelledby="heading-{{ $accordionId }}"
                      data-bs-parent="#{{ $accordionIdBase ?? 'operatorTipe' }}">
                    <div class="accordion-body">
                      <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                          <thead class="table-light">
                            <tr>
                              <th>Nomor SPK</th>
                              <th>Tanggal</th>
                              <th>Pelanggan</th>
                              <th class="text-center" style="width:50px;">File</th>
                              <th>Nama Item</th>
                              <th>Ukuran / Luas</th>
                              <th class="text-end">Jumlah</th>
                              <th>Satuan</th>
                            </tr>
                          </thead>
                          <tbody>
                            @php
                              $groupedBySpk = collect($items ?? [])->groupBy(function ($rec) {
                                  return $rec['spk']->id;
                              });
                            @endphp

                            @forelse($groupedBySpk as $spkId => $rows)
                              @php
                                $spkRow = $rows->first()['spk'];
                                $rowspan = $rows->count();
                                $firstRow = true;

                                $groupDefaultFiles = [];
                                foreach ($rows as $rec) {
                                    /** @var \App\Models\SPKItem $it */
                                    $it = $rec['item'];
                                    $raw = $it->file_pendukung ?? $it->file_pendukung_json ?? '[]';
                                    $files = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;
                                    $default = (is_array($files) && count($files)) ? $files[0] : null;
                                    $path = $default['path'] ?? null;
                                    if (!$path) continue;
                                    $groupDefaultFiles[$path] = $default;
                                }
                                $groupDefaultFiles = array_values($groupDefaultFiles);
                              @endphp

                              @foreach($rows as $rec)
                                @php
                                  $spkItem = $rec['item'];

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

                                  $filePendukungRaw = $spkItem->file_pendukung ?? $spkItem->file_pendukung_json ?? '[]';
                                  if (is_string($filePendukungRaw)) {
                                      $filePendukung = json_decode($filePendukungRaw, true) ?: [];
                                  } else {
                                      $filePendukung = (array) $filePendukungRaw;
                                  }
                                  $firstFile  = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;
                                  $thumbUrl   = null;
                                  $isPdfFirst = false;
                                  if ($firstFile && !empty($firstFile['path'])) {
                                      $thumbUrl   = route('backend.preview-file', ['path' => $firstFile['path']]);
                                      $isPdfFirst = strtolower((string) ($firstFile['type'] ?? '')) === 'pdf';
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
                                      {{ \Carbon\Carbon::parse($spkRow->tanggal_spk)->format('d/m/Y') }}
                                    </td>
                                    <td rowspan="{{ $rowspan }}" class="align-top">
                                      {{ optional($spkRow->pelanggan)->nama ?? '-' }}
                                    </td>
                                    @php $firstRow = false; @endphp
                                  @endif

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
                                </tr>
                              @endforeach
                            @empty
                              <tr>
                                <td colspan="8" class="text-center text-muted">
                                  Tidak ada item untuk bahan ini pada tipe mesin ini.
                                </td>
                              </tr>
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="text-center py-4 text-muted">
                  Tidak ada data bahan untuk tipe mesin ini.
                </div>
              @endforelse
            </div>
          </div>
        @endforeach
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
  </style>

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
  </script>

  <script>
  document.querySelectorAll('#operatorTabs button').forEach(btn => {
      btn.addEventListener('shown.bs.tab', function () {

          document.querySelectorAll('.tab-card').forEach(el => {
              el.classList.remove('active');
          });

          this.classList.add('active');
      });
  });
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