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
                              <th>Nama Item</th>
                              <th>Ukuran</th>
                              <th class="text-end">Jumlah</th>
                              <th>Satuan</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($items as $rec)
                              @php
                                /** @var \App\Models\SPK $spkRow */
                                /** @var \App\Models\SPKItem $spkItem */
                                $spkRow  = $rec['spk'];
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
                              @endphp

                              <tr>
                                <td class="fw-bold">{{ $spkRow->nomor_spk }}</td>
                                <td>
                                  {{ \Carbon\Carbon::parse($spkRow->tanggal_spk)->format('d/m/Y') }}
                                  @php
                                    $spkDate = \Carbon\Carbon::parse($spkRow->tanggal_spk);
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
                                <td>{{ optional($spkRow->pelanggan)->nama ?? '-' }}</td>
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
                                <td colspan="7" class="text-center text-muted">
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
      document.querySelectorAll('.btn-preview-file').forEach(function (btn) {
          btn.addEventListener('click', function () {
              const path = btn.getAttribute('data-path');
              if (!path) return;

              // Sesuaikan nama route di web.php
              const baseUrl = "{{ route('backend.preview-file') }}";
              const url = baseUrl + '?path=' + encodeURIComponent(path);

              window.open(url, '_blank', 'noopener');
          });
      });
  });
  </script>
@endpush