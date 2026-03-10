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

          
    <div class="d-flex justify-content-end mb-3">
      <form method="POST" action="{{ route('pekerjaan.operator-cetak.bulk-complete') }}" id="bulkCetakFormGlobal">
        @csrf
        <div id="bulkCetakInputs"></div>
        <button type="submit" class="btn btn-sm btn-success" id="btnBulkCetak" disabled>
          Multi Cetak
        </button>
      </form>
    </div>
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="form-check">
                            @php
                              $remainingAccordionItems = collect($items)->filter(fn($rec) => ($rec['item']->sisa_belum_cetak ?? 0) > 0)->count();
                            @endphp
                            <input class="form-check-input cek-all-accordion" type="checkbox" data-accordion-id="{{ $accordionId }}" {{ $remainingAccordionItems === 0 ? 'disabled' : '' }}>
                            <label class="form-check-label" for="cekAll-{{ $accordionId }}">
                              Pilih semua item pada tabel ini
                            </label>
                          </div>
                        </div>
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
                              <!-- <th class="text-end">Sudah cetak</th> -->
                              <th class="text-end">Progress</th>
                              <th class="text-center" style="width:40px;">Pilih</th>
                              <th class="text-center" style="width:140px;">Action</th>
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

                                  $sudahCetak = (int) ($spkItem->jumlah_sudah_cetak ?? 0);
                                  $progressPct = (float) ($spkItem->progress_cetak_persen ?? 0);
                                  $sisa = (int) ($spkItem->sisa_belum_cetak ?? 0);
                                @endphp

                                <tr class="{{ $sisa <= 0 ? 'item-selesai' : '' }}">
                                  @if($firstRow)
                                    <td rowspan="{{ $rowspan }}" class="fw-bold align-top">
                                      <div class="d-flex align-items-start gap-2">
                                        <div class="form-check mt-1">
                                          @php
                                            $remainingItems = collect($rows)->filter(fn($rec) => ($rec['item']->sisa_belum_cetak ?? 0) > 0)->count();
                                          @endphp
                                          <input type="checkbox"
                                                class="form-check-input cek-spk"
                                                data-spk-id="{{ $spkRow->id }}"
                                                data-accordion-id="{{ $accordionId }}"
                                                {{ $remainingItems === 0 ? 'disabled' : '' }}
                                                title="Pilih semua item SPK ini">
                                        </div>
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
                                  @php
                                    $progressColor = $progressPct >= 100 ? 'bg-success' : ($progressPct >= 50 ? 'bg-warning' : 'bg-primary');

                                    $firstFileForCetak = $firstFile;
                                    $filePath = $firstFileForCetak['path'] ?? null;
                                    $fileName = $firstFileForCetak['name'] ?? '';
                                    $fileType = $firstFileForCetak['type'] ?? '';
                                  @endphp

                                  <!-- <td class="text-end fw-semibold">{{ number_format($sudahCetak, 0, ',', '.') }}</td> -->

                                  <td class="text-end">
                                    @if($sisa <= 0)
                                      <span class="badge bg-success mb-2">Selesai</span>
                                    @else
                                      <div class="small text-danger fw-semibold mb-1">
                                        Sisa: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                                      </div>
                                    @endif
                                    <div class="progress" style="height:6px;">
                                      <div class="progress-bar {{ $progressColor }}"
                                          role="progressbar"
                                          style="width: {{ $progressPct }}%;"
                                          aria-valuenow="{{ $progressPct }}"
                                          aria-valuemin="0"
                                          aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center gap-2 small mt-1">
                                      <span>{{ $progressPct }}%</span>
                                      <button type="button"
                                              class="btn btn-sm btn-outline-info p-1 btn-cetak-history"
                                              title="Lihat riwayat cetak"
                                              data-spk-item-id="{{ $spkItem->id }}"
                                              data-item-nama="{{ $spkItem->nama_produk }}"
                                              data-nomor-spk="{{ $spkRow->nomor_spk }}">
                                        <i class="fa fa-info-circle"></i>
                                      </button>
                                    </div>
                                  </td>
                                  <td class="text-center">
                                    <input type="checkbox"
                                          class="form-check-input cetak-item-checkbox {{ $sisa <= 0 ? 'checkbox-done' : '' }}"
                                          value="{{ $spkItem->id }}"
                                          data-spk-id="{{ $spkRow->id }}"
                                          data-accordion-id="{{ $accordionId }}"
                                          data-nomor-spk="{{ $spkRow->nomor_spk }}"
                                          data-pelanggan="{{ optional($spkRow->pelanggan)->nama ?? '-' }}"
                                          data-item="{{ $spkItem->nama_produk }}"
                                          data-qty="{{ $spkItem->jumlah }}"
                                          data-sisa="{{ $sisa }}"
                                          data-satuan="{{ $spkItem->satuan }}"
                                          data-locked="{{ $sisa <= 0 ? '1' : '0' }}"
                                          @disabled($sisa <= 0)>
                                  </td>
                                  <td class="text-center">
                                      <button type="button"
                                              class="btn btn-sm {{ $sisa <= 0 ? 'btn-secondary disabled' : 'btn-primary' }} btn-primary btn-open-cetak-modal"
                                              {{ $sisa <= 0 ? 'disabled' : '' }}
                                              data-spk-item-id="{{ $spkItem->id }}"
                                              data-nomor-spk="{{ $spkRow->nomor_spk }}"
                                              data-pelanggan="{{ optional($spkRow->pelanggan)->nama ?? '-' }}"
                                              data-nama-item="{{ $spkItem->nama_produk }}"
                                              data-qty="{{ (int) $spkItem->jumlah }}"
                                              data-sudah="{{ $sudahCetak }}"
                                              data-sisa="{{ $sisa }}"
                                              data-file-path="{{ $filePath ?? '' }}"
                                              data-file-name="{{ $fileName }}"
                                              data-file-type="{{ $fileType }}">
                                          <i class="fa fa-print"></i>
                                      </button>
                                  </td>
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

  .checkbox-done:disabled{
    background-color: #dee2e6;  
    border-color: #adb5bd;
    cursor: not-allowed;
  }

  tr.item-selesai {
    background: #f5f5f5;
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

    @if($errors->any())
      <script>
        Swal.fire({
          title: 'Gagal!',
          html: `{!! implode('<br>', $errors->all()) !!}`,
          icon: 'error'
        });
      </script>
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modalEl = document.getElementById('cetakProgressModal');
      const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

      const bulkForm = document.getElementById('bulkCetakFormGlobal');
      if(!bulkForm) return;
      bulkForm.addEventListener('submit', function(e){
        e.preventDefault();
        const checked = document.querySelectorAll('.cetak-item-checkbox:checked');
        if(!checked.length){
          Swal.fire({
            icon: 'warning',
            title: 'Tidak ada item',
            text: 'Silakan pilih item yang ingin dicetak.'
          });
          return;
        }

        let html = `
          <div style="max-height:350px;overflow:auto">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>SPK</th>
                <th>Pelanggan</th>
                <th>Item</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Sisa</th>
              </tr>
            </thead>
            <tbody>
        `;

        let totalItem = 0;

        checked.forEach(cb => {

          const spk       = cb.dataset.nomorSpk || '-';
          const pelanggan = cb.dataset.pelanggan || '-';
          const item      = cb.dataset.item || '-';
          const qty       = cb.dataset.qty || '0';
          const sisa      = cb.dataset.sisa || '0';

          html += `
            <tr>
              <td>${spk}</td>
              <td>${pelanggan}</td>
              <td>${item}</td>
              <td class="text-end">${Number(qty).toLocaleString('id-ID')}</td>
              <td class="text-end">${Number(sisa).toLocaleString('id-ID')}</td>
            </tr>
          `;

          totalItem++;

        });

        html += `
            </tbody>
          </table>
          </div>

          <div class="mt-3 text-start">
            <strong>Total Item Dipilih : ${totalItem}</strong>
          </div>
        `;

        Swal.fire({
          title: 'Konfirmasi Multi Cetak',
          html: html,
          icon: 'info',
          width: 800,
          showCancelButton: true,
          confirmButtonText: 'Ya, Proses Cetak',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#198754'
        }).then((result) => {

          if(result.isConfirmed){

            Swal.fire({
              title: 'Memproses...',
              text: 'Sedang memproses multi cetak',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });

            bulkForm.submit();
          }

        });

      });

      const bulkBtn = document.getElementById("btnBulkCetak");
      const bulkInputs = document.getElementById("bulkCetakInputs");

      function syncBulk() {
        const checked = document.querySelectorAll('.cetak-item-checkbox:checked');
        bulkBtn.disabled = checked.length === 0;
        bulkInputs.innerHTML = "";
        checked.forEach(cb => {
          const input = document.createElement("input");
          input.type = "hidden";
          input.name = "spk_item_ids[]";
          input.value = cb.value;
          bulkInputs.appendChild(input);
        });

        if (checked.length === 0) {
          bulkBtn.disabled = true;
          bulkBtn.classList.remove("btn-success");
          bulkBtn.classList.add("btn-secondary");
        } else {
          bulkBtn.disabled = false;
          bulkBtn.classList.remove("btn-secondary");
          bulkBtn.classList.add("btn-success");
        }
      }


      document.addEventListener("click", function(e){
        const el = e.target;
        // =====================
        // LOCKED ITEM
        // =====================
        if(el.classList.contains("cetak-item-checkbox")){
          if(el.dataset.locked === "1"){
            e.preventDefault();
            return;
          }

          const spkId = el.dataset.spkId;
          const accordionId = el.dataset.accordionId;

          const spkItems = document.querySelectorAll(
            `.cetak-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]:not([data-locked="1"])`
          );

          const spkChecked = document.querySelectorAll(
            `.cetak-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]:checked`
          );

          const spkMaster = document.querySelector(
            `.cek-spk[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]`
          );

          if(spkMaster){
            spkMaster.checked = spkItems.length === spkChecked.length;
          }

          const accItems = document.querySelectorAll(
            `.cetak-item-checkbox[data-accordion-id="${accordionId}"]:not([data-locked="1"])`
          );

          const accChecked = document.querySelectorAll(
            `.cetak-item-checkbox[data-accordion-id="${accordionId}"]:checked`
          );

          const accMaster = document.querySelector(
            `.cek-all-accordion[data-accordion-id="${accordionId}"]`
          );

          if(accMaster){
            accMaster.checked = accItems.length === accChecked.length;
          }

          syncBulk();
        }


        // =====================
        // SPK SELECT
        // =====================
        if(el.classList.contains("cek-spk")){
          const spkId = el.dataset.spkId;
          const accordionId = el.dataset.accordionId;

          document
            .querySelectorAll(`.cetak-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]:not([data-locked="1"])`)
            .forEach(cb => cb.checked = el.checked);

          const accItems = document.querySelectorAll(
            `.cetak-item-checkbox[data-accordion-id="${accordionId}"]:not([data-locked="1"])`
          );

          const accChecked = document.querySelectorAll(
            `.cetak-item-checkbox[data-accordion-id="${accordionId}"]:checked`
          );

          const accMaster = document.querySelector(
            `.cek-all-accordion[data-accordion-id="${accordionId}"]`
          );

          if(accMaster){
            accMaster.checked = accItems.length === accChecked.length;
          }

          syncBulk();
        }


        // =====================
        // ACCORDION SELECT
        // =====================
        if(el.classList.contains("cek-all-accordion")){

          const accordionId = el.dataset.accordionId;

          document
            .querySelectorAll(`.cetak-item-checkbox[data-accordion-id="${accordionId}"]:not([data-locked="1"])`)
            .forEach(cb => cb.checked = el.checked);

          document
            .querySelectorAll(`.cek-spk[data-accordion-id="${accordionId}"]`)
            .forEach(spk => spk.checked = el.checked);

          syncBulk();
        }

      });

      syncBulk();


      document.querySelectorAll('.btn-open-cetak-modal').forEach(btn => {
        btn.addEventListener('click', function () {
          const spkItemId = btn.getAttribute('data-spk-item-id');
          const nomorSpk = btn.getAttribute('data-nomor-spk') || '';
          const pelanggan = btn.getAttribute('data-pelanggan') || '';
          const namaItem = btn.getAttribute('data-nama-item') || '';
          const qty = parseInt(btn.getAttribute('data-qty') || '0', 10);
          const sudah = parseInt(btn.getAttribute('data-sudah') || '0', 10);
          const sisa = parseInt(btn.getAttribute('data-sisa') || '0', 10);

          const filePath = btn.getAttribute('data-file-path') || '';
          const fileType = (btn.getAttribute('data-file-type') || '').toLowerCase();
          const fileName = btn.getAttribute('data-file-name') || '';

          let riwayat = [];
          try { riwayat = JSON.parse(btn.getAttribute('data-riwayat') || '[]'); } catch (e) { riwayat = []; }

          document.getElementById('cetak_spk_item_id').value = spkItemId;
          document.getElementById('cetak_nomor_spk').textContent = nomorSpk;
          document.getElementById('cetak_pelanggan').textContent = pelanggan;
          document.getElementById('cetak_nama_item').textContent = namaItem;
          document.getElementById('cetak_qty').textContent = qty.toLocaleString('id-ID');
          document.getElementById('cetak_sudah').textContent = sudah.toLocaleString('id-ID');
          document.getElementById('cetak_sisa').textContent = sisa.toLocaleString('id-ID');
          document.getElementById('cetak_sisa_label').textContent = sisa.toLocaleString('id-ID');
          document.getElementById('cetak_sisa_setelah').innerHTML = "Sisa setelah cetak: -";

          const jumlahInput = document.getElementById('cetak_jumlah');
          jumlahInput.value = '';
          jumlahInput.max = String(Math.max(0, sisa));
          if (sisa <= 0) {
            jumlahInput.value = '0';
            jumlahInput.disabled = true;
          } else {
            jumlahInput.disabled = false;
          }

          const preview = document.getElementById('cetak_preview_container');
          if (!filePath) {
            preview.innerHTML = '<div class="text-muted small">Tidak ada file.</div>';
          } else {
            const url = "{{ route('backend.preview-file') }}" + '?path=' + encodeURIComponent(filePath);
            if (fileType === 'pdf') {
              preview.innerHTML = `<a class="btn btn-sm btn-outline-danger" target="_blank" href="${url}">
                <i class="fa fa-file-pdf"></i> Buka PDF ${fileName ? '('+fileName+')' : ''}
              </a>`;
            } else {
              preview.innerHTML = `<a target="_blank" href="${url}">
                <img src="${url}" class="img-fluid rounded" style="max-height:280px;object-fit:contain;" loading="lazy">
              </a>`;
            }
          }

          /*
    ===============================
    LOAD RIWAYAT PROGRESS
    ===============================
    */

    const riwayatEl = document.getElementById('cetak_riwayat_container');

    riwayatEl.innerHTML =
      '<div class="text-muted small">Memuat riwayat...</div>';

    const url = "{{ route('pekerjaan.operator-cetak.item-progress', ['spkItem' => '__ID__']) }}"
      .replace('__ID__', spkItemId);

    fetch(url, {
      headers: {
        'Accept': 'application/json'
      }
    })

    .then(async (r) => {

      if (!r.ok) {
        const msg = await r.text();
        throw new Error(msg || 'Gagal memuat riwayat progress.');
      }

      return r.json();

    })

    .then((data) => {

      const logs = data.logs || [];

      if (!logs.length) {

        riwayatEl.innerHTML = `
        <div class="text-center text-muted py-3">
          <i class="fa fa-info-circle"></i><br>
          Belum ada riwayat progress
        </div>`;

        return;
      }

      let html = `
      <div style="max-height:220px;overflow-y:auto;">
      <table class="table table-sm table-bordered align-middle mb-0">

      <thead class="table-light">
      <tr>
        <th style="width:40px;">No</th>
        <th>Operator</th>
        <th class="text-end">Jumlah</th>
        <th>Waktu</th>
      </tr>
      </thead>

      <tbody>
      `;

      logs.forEach((r, i) => {
        const jumlah = (r.jumlah ?? 0).toLocaleString('id-ID');
        const operator = r.operator ?? '-';
        const waktu = r.waktu ?? '';
        const tanggal = r.tanggal ?? '';
        const isBatalkan = r.is_batalkan === true;
        const rowClass = isBatalkan ? 'table-secondary text-decoration-line-through' : '';
        const jumlahText = isBatalkan ? 'Batalkan ' + jumlah : jumlah;
        html += `
          <tr class="${rowClass}">
            <td class="text-center">${i + 1}</td>
            <td>${operator}</td>
            <td class="text-end fw-semibold ${isBatalkan ? 'text-danger' : 'text-primary'}">${jumlahText}</td>
            <td><div>${waktu}</div><div class="text-muted small">${tanggal}</div></td>
          </tr>`;
      });

      html += `
      </tbody>
      </table>
      </div>
      `;

      riwayatEl.innerHTML = html;

    })

    .catch((err) => {

      riwayatEl.innerHTML = `
      <div class="text-danger small">
        ${(err && err.message) ? err.message : 'Gagal memuat riwayat.'}
      </div>`;

    });


          modal.show();
        });
      });

      document.getElementById('formCetakProgress').addEventListener('submit', function(e) {
        const sisa = parseInt(document.getElementById('cetak_sisa').textContent.replace(/\./g,'').replace(/,/g,''), 10) || 0;
        const val = parseInt(document.getElementById('cetak_jumlah').value || '0', 10) || 0;

        if (val <= 0) {
          e.preventDefault();
          Swal.fire({ title: 'Gagal!', text: 'Jumlah cetak minimal 1.', icon: 'error' });
          return;
        }

        if (val > sisa) {
          e.preventDefault();
          Swal.fire({ title: 'Overprint!', text: 'Jumlah melebihi sisa belum cetak.', icon: 'error' });
        }
      });

      // ================================
      // UPDATE SISA OTOMATIS
      // ================================
      const checkboxMax = document.getElementById("cetak_semua");
      const jumlahInput = document.getElementById('cetak_jumlah');
      const sisaLabel = document.getElementById('cetak_sisa_label');
      const sisaSetelah = document.getElementById('cetak_sisa_setelah');

      if (jumlahInput) {

        jumlahInput.addEventListener('input', function(){

          const sisaText = document
            .getElementById('cetak_sisa')
            .textContent
            .replace(/\./g,'')
            .replace(/,/g,'');

          const sisa = parseInt(sisaText) || 0;

          let val = parseInt(this.value || 0);

          // cegah lebih dari sisa
          if(val > sisa){
            val = sisa;
            this.value = sisa;
          }

          const remain = sisa - val;

          if(val <= 0){
            sisaSetelah.innerHTML = "Sisa setelah cetak: -";
          }else{
            sisaSetelah.innerHTML =
              `Sisa setelah cetak: 
              <span class="text-success fw-bold">
                ${remain.toLocaleString('id-ID')}
              </span>`;
          }

        });
      }

      if(checkboxMax){
        checkboxMax.addEventListener("change", function(){
          const sisaText = document
            .getElementById('cetak_sisa')
            .textContent
            .replace(/\./g,'')
            .replace(/,/g,'');

          const sisa = parseInt(sisaText) || 0;
          if(this.checked){
            jumlahInput.value = sisa;
            jumlahInput.readOnly = true;
            sisaSetelah.innerHTML =
              `Sisa setelah cetak: 
              <span class="text-success fw-bold">
                0
              </span>`;
          }else{
            jumlahInput.readOnly = false;
            jumlahInput.value = "";
            sisaSetelah.innerHTML = "Sisa setelah cetak: -";
          }
        });
      }

      const form = document.getElementById("formCetakProgress");
      const btn = document.getElementById('btn_simpan_progress');

      if(!form || !btn) return;
      form.addEventListener('submit', function(){
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
      });
    });

    document.addEventListener('click', function (e) {
      const btnHistory = e.target.closest('.btn-cetak-history');
        if (btnHistory) {
          const spkItemId = btnHistory.getAttribute('data-spk-item-id');
          const nomorSpk  = btnHistory.getAttribute('data-nomor-spk') || '-';
          const namaItem  = btnHistory.getAttribute('data-item-nama') || '-';
          document.getElementById('cetakHistoryNomorSpk').textContent = nomorSpk;
          document.getElementById('cetakHistoryNamaItem').textContent = namaItem;
          const tbody = document.getElementById('cetakHistoryBody');
          tbody.innerHTML = `
            <tr>
              <td colspan="6" class="text-center text-muted py-3">
                <span class="spinner-border spinner-border-sm me-1"></span> Memuat data...
              </td>
            </tr>
          `;
          // Panggil endpoint untuk ambil riwayat cetak item
          fetch(`{{ route('pekerjaan.operator-cetak.history', ['spkItem' => 'SPK_ITEM_ID_PLACEHOLDER']) }}`
                .replace('SPK_ITEM_ID_PLACEHOLDER', spkItemId),
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
              if (!Array.isArray(data.logs) || data.logs.length === 0) {
                tbody.innerHTML = `
                  <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                      Belum ada riwayat cetak untuk item ini.
                    </td>
                  </tr>
                `;
                return;
              }
              tbody.innerHTML = '';
              data.logs.forEach((log, idx) => {
                tbody.insertAdjacentHTML('beforeend', `
                  <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td>${log.tanggal_label}</td>
                    <td class="text-end">${log.jumlah_formatted}</td>
                    <td>${log.operator || '-'}</td>
                    <td class="text-center">
                      <button type="button"
                              class="btn btn-sm btn-outline-danger btn-undo-cetak"
                              data-log-id="${log.id}">
                        <i class="fa fa-undo me-1"></i> Batalkan
                      </button>
                    </td>
                  </tr>
                `);
              });
            })
            .catch(() => {
              tbody.innerHTML = `
                <tr>
                  <td colspan="6" class="text-center text-danger py-3">
                    Gagal memuat riwayat cetak.
                  </td>
                </tr>
              `;
            });
          const modalEl = document.getElementById('modalCetakHistory');
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
          modal.show();
          return;
      }
      const btnUndo = e.target.closest('.btn-undo-cetak');
      if (btnUndo) {
        const logId = btnUndo.getAttribute('data-log-id');
        if (!logId) return;

        Swal.fire({
            title: 'Batalkan cetakan?',
            text: 'Progress cetak akan disesuaikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {

            if (!result.isConfirmed) return;

            fetch(`{{ route('pekerjaan.operator-cetak.destroy-history', ['log' => 'LOG_ID_PLACEHOLDER']) }}`
                    .replace('LOG_ID_PLACEHOLDER', logId),
            {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(r => r.json())
            .then(data => {

                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal membatalkan cetakan.'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Cetakan berhasil dibatalkan.',
                    timer: 1500,
                    showConfirmButton: false
                });

                const sid = String(data.spk_item_id);
                const sudah = data.jumlah_sudah_cetak ?? 0;
                const sisa = data.sisa_belum_cetak ?? 0;
                const pct = Math.round(data.progress_persen ?? 0);
                const row = document.querySelector(
                    `.btn-open-cetak-modal[data-spk-item-id="${sid}"]`
                )?.closest('tr');
                if (row) {
                    const btnModal = row.querySelector(
                        `.btn-open-cetak-modal[data-spk-item-id="${sid}"]`
                    );
                    if (btnModal) {
                        btnModal.setAttribute('data-sudah', sudah);
                        btnModal.setAttribute('data-sisa', sisa);
                    }
                    const checkbox = row.querySelector('.cetak-item-checkbox');
                    if (checkbox) {
                        checkbox.setAttribute('data-sisa', sisa);
                        checkbox.setAttribute('data-locked', sisa <= 0 ? '1' : '0');
                        checkbox.disabled = sisa <= 0;
                        if (sisa <= 0) {
                            checkbox.classList.add('checkbox-done');
                        } else {
                            checkbox.classList.remove('checkbox-done');
                        }
                    }
                    const progressBar = row.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.width = pct + '%';
                        progressBar.setAttribute('aria-valuenow', pct);
                        progressBar.classList.remove('bg-success', 'bg-warning', 'bg-primary');
                        progressBar.classList.add(
                            pct >= 100 ? 'bg-success' : pct >= 50 ? 'bg-warning' : 'bg-primary'
                        );
                    }
                    const pctLabel = row.querySelector('.d-flex.justify-content-end span');
                    if (pctLabel) pctLabel.textContent = pct + '%';
                    const sisaText = row.querySelector('.small.text-danger.fw-semibold');
                    if (sisaText) {
                        if (sisa <= 0) {
                            sisaText.outerHTML =
                                '<span class="badge bg-success mb-2">Selesai</span>';
                        } else {
                            sisaText.textContent = 'Sisa: ' + sisa.toLocaleString('id-ID') + ' ' +
                                (checkbox?.getAttribute('data-satuan') ?? '');
                        }
                    }
                    const btnPrint = row.querySelector('.btn-open-cetak-modal');
                    if (btnPrint) {
                        btnPrint.setAttribute('data-sudah', sudah);
                        btnPrint.setAttribute('data-sisa', sisa);
                        if (sisa <= 0) {
                            btnPrint.classList.remove('btn-primary');
                            btnPrint.classList.add('btn-secondary', 'disabled');
                            btnPrint.disabled = true;
                            btnPrint.setAttribute('disabled', 'disabled');
                        } else {
                            btnPrint.classList.remove('btn-secondary', 'disabled');
                            btnPrint.classList.add('btn-primary');
                            btnPrint.disabled = false;
                            btnPrint.removeAttribute('disabled');
                        }
                    }
                }

                const spkItemIdEl = document.getElementById('cetak_spk_item_id');
                if (spkItemIdEl && spkItemIdEl.value === sid) {
                    document.getElementById('cetak_sudah').textContent =
                        sudah.toLocaleString('id-ID');
                    document.getElementById('cetak_sisa').textContent =
                        sisa.toLocaleString('id-ID');
                    document.getElementById('cetak_sisa_label').textContent =
                        sisa.toLocaleString('id-ID');
                    document.getElementById('cetak_jumlah').max = String(Math.max(0, sisa));
                    if (sisa <= 0) {
                        document.getElementById('cetak_jumlah').value = '0';
                        document.getElementById('cetak_jumlah').disabled = true;
                    } else {
                        document.getElementById('cetak_jumlah').disabled = false;
                    }
                }

                const currentBtnHistory = document.querySelector(
                    `.btn-cetak-history[data-spk-item-id="${data.spk_item_id}"]`
                );

                if (currentBtnHistory) {
                    currentBtnHistory.click();
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat membatalkan cetakan.'
                });
            });

        });
        return;
      }
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


<div class="modal fade" id="cetakProgressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('pekerjaan.operator-cetak.progress') }}" id="formCetakProgress">
        @csrf
        <div class="modal-header py-2">
          <h6 class="modal-title mb-0">Cetak - Progress</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="spk_item_id" id="cetak_spk_item_id">

          <div class="row g-3">
            <div class="col-md-6">
              <div class="border rounded p-3 bg-light-subtle">
                <div class="fw-bold mb-3 text-primary">
                  <i class="fa fa-file-alt me-1"></i> Informasi SPK
                </div>

                <div class="row g-2 small">
                  <div class="col-5 text-muted">Nomor SPK</div>
                  <div class="col-7 fw-semibold" id="cetak_nomor_spk"></div>

                  <div class="col-5 text-muted">Pelanggan</div>
                  <div class="col-7 fw-semibold" id="cetak_pelanggan"></div>

                  <div class="col-5 text-muted">Nama Item</div>
                  <div class="col-7 fw-semibold text-dark" id="cetak_nama_item"></div>
                </div>

                <hr class="my-2">

                <div class="row text-center g-2">
                  <div class="col-4">
                    <div class="border rounded p-2 bg-white">
                      <div class="text-muted small">Pesanan</div>
                      <div class="fw-bold text-primary fs-6" id="cetak_qty"></div>
                    </div>
                  </div>

                  <div class="col-4">
                    <div class="border rounded p-2 bg-white">
                      <div class="text-muted small">Sudah Cetak</div>
                      <div class="fw-bold text-success fs-6" id="cetak_sudah"></div>
                    </div>
                  </div>

                  <div class="col-4">
                    <div class="border rounded p-2 bg-white">
                      <div class="text-muted small">Sisa</div>
                      <div class="fw-bold text-danger fs-6" id="cetak_sisa"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="border rounded p-3 mt-3 bg-light-subtle">
              <div class="fw-semibold mb-2 text-primary">
                <i class="fa fa-print me-1"></i> Input Jumlah Cetak
              </div>

              <div class="input-group">
                <input 
                  type="number"
                  min="1"
                  class="form-control text-end fw-bold"
                  name="jumlah"
                  id="cetak_jumlah"
                  placeholder="0"
                  required
                >

                <span class="input-group-text bg-white">
                  / <span id="cetak_sisa_label" class="fw-bold text-danger ms-1">0</span>
                </span>
              </div>

              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="cetak_semua">
                <label class="form-check-label small" for="cetak_semua">
                  Cetak semua
                </label>
              </div>

              <div class="d-flex justify-content-between mt-2 small">
                <div class="text-muted">
                  Maksimal sesuai sisa cetak
                </div>
                <div id="cetak_sisa_setelah" class="fw-semibold text-success">
                  Sisa setelah cetak: -
                </div>
              </div>
            </div>
            </div>

            <div class="col-md-6">
              <div class="border rounded p-3">
                <div class="fw-semibold mb-2">Preview file desain</div>
                <div id="cetak_preview_container" class="text-muted small">Tidak ada file.</div>
              </div>

              <div class="border rounded p-3 mt-3">
                <div class="fw-semibold mb-2">Riwayat progress</div>
                <div id="cetak_riwayat_container" class="small text-muted">Belum ada riwayat.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btn_simpan_progress">
            <span class="btn-text">
              <i class="fa fa-save me-1"></i> Simpan Progress
            </span>

            <span class="btn-loading d-none">
              <span class="spinner-border spinner-border-sm me-1"></span>
              Menyimpan...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalCetakHistory" tabindex="-1" aria-labelledby="modalCetakHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCetakHistoryLabel">
          <i class="fa fa-history me-1"></i> Riwayat Cetak Item
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2 small text-muted">
          <div><strong>No. SPK:</strong> <span id="cetakHistoryNomorSpk">-</span></div>
          <div><strong>Nama Item:</strong> <span id="cetakHistoryNamaItem">-</span></div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 40px;">#</th>
                <th>Tanggal/Jam</th>
                <th class="text-end">Jumlah Cetak</th>
                <th>Operator</th>
                <th style="width: 120px;">Aksi</th>
              </tr>
            </thead>
            <tbody id="cetakHistoryBody">
              <tr>
                <td colspan="6" class="text-center text-muted py-3">
                  Memuat data...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <small class="text-muted d-block mt-2">
          Menghapus entri riwayat akan <strong>membatalkan cetakan</strong> tersebut dan mengurangi progress cetak item.
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endpush
