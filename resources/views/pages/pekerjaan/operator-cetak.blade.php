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
              <div>
                  <h6 class="card-title mb-0">Data Pekerjaan Operator Cetak</h6>
                  <p class="text-muted mb-3">Daftar semua SPK yang perlu diproses oleh Operator Cetak.</p>
              </div>
              <div>
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalGlobalCetakHistory">
                      <i class="fa fa-history me-1"></i> History
                  </button>
              </div>
          </div>
          <div class="row g-3 mb-4" id="operatorTabs" role="tablist">
            {{-- TAB BARU: Pekerjaan Saya --}}
            <div class="col-md-3">
              <button class="card tab-card active w-100 text-start"
                      id="tab-pekerjaan-saya"
                      data-bs-toggle="tab"
                      data-bs-target="#tabPekerjaanSaya"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">
                  <div class="tab-icon bg-primary-subtle text-primary">
                    <i class="fa fa-user-check"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Pekerjaan Saya</h6>
                      <span class="badge bg-primary rounded-pill px-3">
                        {{ (int) ($pekerjaanSayaCount ?? 0) }}
                      </span>
                    </div>
                    <small class="text-muted">Item yang sudah diambil</small>
                    <!-- @php
                      $progressSaya = isset($progressPekerjaanSaya) ? (float) $progressPekerjaanSaya : 0.0;
                    @endphp
                    <div class="mt-1">
                      <div class="progress" style="height:4px;">
                        <div class="progress-bar bg-success"
                            role="progressbar"
                            style="width: {{ $progressSaya }}%;"
                            aria-valuenow="{{ $progressSaya }}"
                            aria-valuemin="0"
                            aria-valuemax="100"></div>
                      </div>
                      <small class="text-muted">Progress: {{ $progressSaya }}%</small>
                    </div> -->
                  </div>
                </div>
              </button>
            </div>

            {{-- TAB: Pool Pekerjaan --}}
            @php
              $poolTotalSpk = 0;
              foreach (($poolTipeMesinGroups ?? []) as $g) {
                $poolTotalSpk += count($g['spk'] ?? []);
              }
            @endphp
            <div class="col-md-3">
              <button class="card tab-card w-100 text-start"
                      id="tab-pool-pekerjaan"
                      data-bs-toggle="tab"
                      data-bs-target="#tabPoolPekerjaan"
                      type="button"
                      role="tab">
                <div class="card-body d-flex align-items-center gap-3">
                  <div class="tab-icon bg-success-subtle text-success">
                    <i class="fa fa-layer-group"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-semibold">Pool Pekerjaan</h6>
                      <span class="badge bg-success rounded-pill px-3">
                        {{ (int) $poolTotalSpk }}
                      </span>
                    </div>
                    <small class="text-muted">Semua pekerjaan</small>
                  </div>
                </div>
              </button>
            </div>
          </div>

          <!-- Divider -->
          <hr class="my-4">

          
    <div class="tab-content mt-3" id="operatorTabContent">
        {{-- TAB: PEKERJAAN SAYA --}}
        <div class="tab-pane fade show active"
            id="tabPekerjaanSaya"
            role="tabpanel"
            aria-labelledby="tab-pekerjaan-saya">
          {{-- Multi Cetak & Ambil Pekerjaan --}}
          <div class="d-flex justify-content-end gap-2 mb-3">
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAmbilPekerjaan">
              Ambil pekerjaan
            </button>
            <form method="POST" action="{{ route('pekerjaan.operator-cetak.bulk-complete') }}" id="bulkCetakFormGlobal">
              @csrf
              <input type="hidden" name="mesin_id" id="bulkCetakMesinId" value="">
              <div id="bulkCetakInputs"></div>
              <button type="submit" class="btn btn-sm btn-secondary" id="btnBulkCetak" disabled>
                Multi Cetak
              </button>
            </form>
          </div>
          @if(($pekerjaanSayaByMesin ?? collect())->count() > 0)
            {{-- Tab card mesin --}}
            <div class="row g-3 mb-3" id="pekerjaanSayaMesinTabs" role="tablist">
              @foreach($pekerjaanSayaByMesin as $group)
                @php
                  $isFirst = $loop->first;
                  $slug = 'ps-mesin-'.$group['mesin_id'];
                @endphp
                <div class="col-md-3">
                  <button class="card tab-card w-100 text-start {{ $isFirst ? 'active' : '' }}"
                          id="tab-{{ $slug }}"
                          data-bs-toggle="tab"
                          data-bs-target="#pane-{{ $slug }}"
                          type="button"
                          role="tab"
                          aria-controls="pane-{{ $slug }}"
                          aria-selected="{{ $isFirst ? 'true' : 'false' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                      <div class="tab-icon bg-primary-subtle text-primary">
                        <i class="fa fa-print"></i>
                      </div>
                      <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                          <h6 class="mb-0 fw-semibold">{{ $group['mesin_nama'] }}</h6>
                          <span class="badge bg-primary rounded-pill px-3">{{ $group['count'] ?? 0 }}</span>
                        </div>
                        <small class="text-muted">Pekerjaan per mesin</small>
                      </div>
                    </div>
                  </button>
                </div>
              @endforeach
            </div>
            {{-- Content per mesin --}}
            <div class="tab-content" id="pekerjaanSayaMesinTabContent">
              @foreach($pekerjaanSayaByMesin as $group)
                @php
                  $isFirst = $loop->first;
                  $slug = 'ps-mesin-'.$group['mesin_id'];
                  $rows = $group['items'] ?? collect();
                @endphp
                <div class="tab-pane fade {{ $isFirst ? 'show active' : '' }}"
                    id="pane-{{ $slug }}"
                    role="tabpanel"
                    aria-labelledby="tab-{{ $slug }}">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>No SPK</th>
                          <th>Pelanggan</th>
                          <th>Item</th>
                          <th class="text-end">Qty Diambil</th>
                          <th>Mesin</th>
                          <th class="text-end" style="width:180px;">Progress</th>
                          <th class="text-center" style="width:40px;">Pilih</th>
                          <th class="text-center" style="width:210px;">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($rows as $row)
                          @php
                            $spk = $row['spk'];
                            $item = $row['item'];
                            $queue = $row['queue'];
                            $mesinNama = $row['mesin_nama'] ?? '-';
                            $qtyDiambil = (int) ($row['qty_diambil'] ?? 0);
                            $printed = (int) ($row['printed'] ?? 0);
                            $progress = (float) ($row['progress'] ?? 0);
                            $sisa = max(0, $qtyDiambil - $printed);
                          @endphp
                          <tr>
                            <td>{{ $row['nomor_spk'] ?? '-' }}</td>
                            <td>{{ $row['pelanggan'] ?? '-' }}</td>
                            <td>{{ $row['nama_item'] ?? '-' }}</td>
                            <td class="text-end">{{ number_format((int) ($queue->jumlah ?? 0), 0, ',', '.') }}</td>
                            <td>{{ $mesinNama }}</td>
                            <td class="text-end">
                              <div class="small fw-semibold mb-1">Progress: {{ $progress }}%</div>
                              <div class="progress mb-1" style="height:6px;">
                                <div class="progress-bar {{ $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-primary') }}"
                                    role="progressbar"
                                    style="width: {{ $progress }}%;"
                                    aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"></div>
                              </div>
                              <div class="small text-muted">
                                Selesai: {{ number_format($printed,0,',','.') }} / {{ number_format($qtyDiambil,0,',','.') }}
                                (Sisa: {{ number_format($sisa,0,',','.') }})
                              </div>
                            </td>
                            <td class="text-center">
                              <input type="checkbox"
                                    class="form-check-input cetak-item-checkbox"
                                    value="{{ $item->id }}"
                                    data-mesin-id="{{ $queue->mesin_id }}"
                                    data-nomor-spk="{{ $spk->nomor_spk ?? '-' }}"
                                    data-pelanggan="{{ $spk?->pelanggan?->nama ?? '-' }}"
                                    data-item="{{ $item->nama_produk ?? '-' }}"
                                    data-qty="{{ $qtyDiambil }}"
                                    data-sisa="{{ $sisa }}">
                            </td>
                            <td class="text-center">
                              <button type="button"
                                      class="btn btn-xs btn-primary btn-open-cetak-modal"
                                      data-spk-item-id="{{ $item->id }}"
                                      data-nomor-spk="{{ $spk->nomor_spk ?? '-' }}"
                                      data-pelanggan="{{ $spk?->pelanggan?->nama ?? '-' }}"
                                      data-nama-item="{{ $item->nama_produk ?? '-' }}"
                                      data-qty="{{ $qtyDiambil }}"
                                      data-mesin-id="{{ (int) ($queue->mesin_id ?? 0) }}"
                                      data-diambil="{{ (int) ($queue->jumlah ?? 0) }}"
                                      data-sudah="{{ $printed }}"
                                      data-sisa="{{ $sisa }}">
                                <i class="fa fa-print"></i>
                              </button>
                              <form method="POST"
                                    action="{{ route('pekerjaan.operator-cetak.batal-ambil') }}"
                                    class="d-inline ms-1 form-batal-ambil">
                                @csrf
                                <input type="hidden" name="mesin_id" value="{{ (int) ($queue->mesin_id ?? 0) }}">
                                <input type="hidden" name="queue_ids[]" value="{{ (int) ($queue->id ?? 0) }}">
                                <input type="hidden" name="spk_item_ids[]" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-xs btn-outline-danger">Batal ambil</button>
                              </form>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada pekerjaan untuk mesin ini.</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-4">Belum ada pekerjaan yang diambil.</div>
          @endif
        </div>

        {{-- TAB : Pool Pekerjaan --}}
        <div class="tab-pane fade" id="tabPoolPekerjaan" role="tabpanel" aria-labelledby="tab-pool-pekerjaan">
          @if(!empty($poolTipeMesinGroups))
            {{-- Tab card tipe mesin --}}
            <div class="row g-3 mb-3" id="poolTipeTabs" role="tablist">
              @foreach($poolTipeMesinGroups as $tipe => $group)
                @php
                  $isFirst = $loop->first;
                  $slug = 'pool-tipe-'.\Illuminate\Support\Str::slug($tipe, '-');
                @endphp
                <div class="col-md-3">
                  <button class="card tab-card w-100 text-start {{ $isFirst ? 'active' : '' }}"
                          id="tab-{{ $slug }}"
                          data-bs-toggle="tab"
                          data-bs-target="#pane-{{ $slug }}"
                          type="button"
                          role="tab"
                          aria-controls="pane-{{ $slug }}"
                          aria-selected="{{ $isFirst ? 'true' : 'false' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                      <div class="tab-icon bg-success-subtle text-success">
                        <i class="fa fa-cogs"></i>
                      </div>
                      <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                          <h6 class="mb-0 fw-semibold">{{ $group['label'] ?? $tipe }}</h6>
                          <span class="badge bg-success rounded-pill px-3">{{ count($group['spk'] ?? []) }}</span>
                        </div>
                        <small class="text-muted">Pool per tipe mesin</small>
                      </div>
                    </div>
                  </button>
                </div>
              @endforeach
            </div>

            {{-- Content per tipe mesin --}}
            <div class="tab-content" id="poolTipeTabContent">
              @foreach($poolTipeMesinGroups as $tipe => $group)
                @php
                  $isFirst = $loop->first;
                  $slug = 'pool-tipe-'.\Illuminate\Support\Str::slug($tipe, '-');
                  $accordionIdBase = 'accordionTipe'.\Illuminate\Support\Str::slug($tipe, '-');
                  /** @var array $group */
                  $bahanGroups = $group['bahanGroups'] ?? [];
                @endphp

                <div class="tab-pane fade {{ $isFirst ? 'show active' : '' }}"
                    id="pane-{{ $slug }}"
                    role="tabpanel"
                    aria-labelledby="tab-{{ $slug }}">

                  <div class="pool-tipe-wrapper">
                    <div class="accordion" id="{{ $accordionIdBase }}">
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
                                    $totalMetric = 0;
                                    $metricUnitLabel = 'm';

                                    foreach ($items as $rec) {
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
                                      <th>Pelanggan</th>
                                      <th class="text-center" style="width:50px;">File</th>
                                      <th>Nama Item</th>
                                      <th>Ukuran / Luas</th>
                                      <th class="text-end">Jumlah</th>
                                      <th>Satuan</th>
                                      <th class="text-end">Progress</th>
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

                                          $workflowStep = $rec['workflow_step'] ?? [];
                                          $stepIndex = (int) ($workflowStep['step_index'] ?? 1);
                                          $stepTotal = (int) ($workflowStep['step_total'] ?? 1);
                                          $eligibleQty = (int) ($workflowStep['eligible_qty'] ?? 0);
                                          $totalDiambil = (int) ($workflowStep['queued_qty_step'] ?? 0);
                                          $sisaAmbil = (int) ($workflowStep['remaining_take_qty'] ?? 0);
                                          $pctAmbil = $eligibleQty > 0 ? min(100, round(($totalDiambil / $eligibleQty) * 100, 1)) : 0.0;

                                          $sudahCetak = (int) ($workflowStep['printed_qty_step'] ?? 0);
                                          $pctCetak = (float) ($workflowStep['progress_step_pct'] ?? 0);
                                          $sisa = (int) ($workflowStep['remaining_print_qty'] ?? 0);
                                        @endphp

                                        <tr class="{{ $sisaAmbil <= 0 ? 'item-selesai' : '' }}">
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
                                          <td class="text-end">{{ number_format($eligibleQty, 0, ',', '.') }}</td>
                                          <td>{{ $spkItem->satuan }}</td>

                                          <td class="text-end">
                                            <div class="small fw-semibold mb-1">Ambil: {{ $pctAmbil }}%</div>
                                            <div class="progress mb-2" style="height:6px;">
                                              <div class="progress-bar bg-success"
                                                  role="progressbar"
                                                  style="width: {{ $pctAmbil }}%;"
                                                  aria-valuenow="{{ $pctAmbil }}"
                                                  aria-valuemin="0"
                                                  aria-valuemax="100"></div>
                                            </div>
                                            <div class="small text-muted mb-2">
                                              Step {{ $stepIndex }}/{{ $stepTotal }} • Diambil: {{ number_format($totalDiambil,0,',','.') }} / {{ number_format($eligibleQty,0,',','.') }}
                                              (Sisa ambil: {{ number_format($sisaAmbil,0,',','.') }})
                                            </div>

                                            <div class="small fw-semibold mb-1">Cetak: {{ $pctCetak }}%</div>
                                            <div class="progress" style="height:6px;">
                                              <div class="progress-bar {{ $pctCetak >= 100 ? 'bg-success' : ($pctCetak >= 50 ? 'bg-warning' : 'bg-primary') }}"
                                                  role="progressbar"
                                                  style="width: {{ $pctCetak }}%;"
                                                  aria-valuenow="{{ $pctCetak }}"
                                                  aria-valuemin="0"
                                                  aria-valuemax="100"></div>
                                            </div>
                                            <div class="small text-muted mt-1">
                                              Sisa cetak: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                                            </div>
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
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-4">
              Tidak ada pool pekerjaan untuk role mesin Anda.
            </div>
          @endif
        </div>
  </div>

  {{-- MODAL: Ambil Pekerjaan --}}
  <div class="modal fade" id="modalAmbilPekerjaan" tabindex="-1" aria-labelledby="modalAmbilPekerjaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAmbilPekerjaanLabel">
            Ambil Pekerjaan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-target="#modalAmbilPekerjaan" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          {{-- Tab navigasi per tipe mesin di dalam modal --}}
          <div class="row g-3 mb-4" id="modalAmbilTabs" role="tablist">
            @foreach($tipeMesinGroups as $tipe => $group)
              @php
                $slug = \Illuminate\Support\Str::slug($tipe, '-');
                $label = $group['label'] ?? $tipe;
                $mesinListForTipe = $group['mesin_list'] ?? [];
                $isFirst = $loop->first;
              @endphp
              <div class="col-md-3">
                <button class="card tab-card w-100 text-start {{ $isFirst ? 'active' : '' }}"
                        id="modal-tab-tipe-{{ $slug }}"
                        data-bs-toggle="tab"
                        data-bs-target="#modalTabTipe{{ $slug }}"
                        data-mesin-list='@json($mesinListForTipe)'
                        data-slug="{{ $slug }}"
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
                      <small class="text-muted">Ambil pekerjaan</small>
                    </div>
                  </div>
                </button>
              </div>
            @endforeach
          </div>

          {{-- Tab content per tipe mesin di dalam modal --}}
          <div class="tab-content" id="modalAmbilTabContent">
            @foreach($tipeMesinGroups as $tipe => $group)
              @php
                $slug = \Illuminate\Support\Str::slug($tipe, '-');
                $accordionIdBase = 'modalAccordionTipe'.$slug;
                $isFirst = $loop->first;
              @endphp
              <div class="tab-pane fade {{ $isFirst ? 'show active' : ''}}" id="modalTabTipe{{ $slug }}"
                  role="tabpanel"
                  aria-labelledby="modal-tab-tipe-{{ $slug }}">

                <div class="d-flex align-items-center justify-content-between mb-3 flex-nowrap">
                  <div id="modalMesinSubTabsContainer-{{ $slug }}" class="d-flex align-items-center gap-2 d-none">
                    <span class="text-muted small">Mesin:</span>
                    <div id="modalMesinSubTabsInner-{{ $slug }}" class="nav nav-pills d-flex align-items-center gap-1" role="tablist"></div>
                  </div>

                  <form method="POST"
                        action="{{ route('pekerjaan.operator-cetak.multi-ambil-semua') }}"
                        class="d-flex justify-content-end gap-2 mb-2 modalBulkAmbilForm flex-grow-1"
                        data-tipe-tab="{{ $slug }}">
                    @csrf
                    <input type="hidden" name="mesin_id" class="modalBulkAmbilMesinId" value="">
                    <div class="modalBulkAmbilInputs"></div>
                    <button type="button" class="btn btn-sm btn-secondary btnModalMultiAmbil" disabled>
                      Multi Ambil
                    </button>
                  </form>
                </div>

                <div class="accordion" id="{{ $accordionIdBase }}">
                  @php
                    $bahanGroups = $group['bahanGroups'] ?? [];
                  @endphp

                  @forelse($bahanGroups as $bahan)
                    @php
                      $accordionId = ($accordionIdBase).'-bahan-'.$bahan['id'];
                      $items = array_values($bahan['items'] ?? []);
                    @endphp

                    <div class="accordion-item">
                      <h2 class="accordion-header" id="modal-heading-{{ $accordionId }}">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#modal-collapse-{{ $accordionId }}"
                                aria-expanded="false"
                                aria-controls="modal-collapse-{{ $accordionId }}">
                          <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div class="flex-grow-1">
                              @php
                                $totalMetric = 0;
                                $metricUnitLabel = 'm';
                                foreach ($items as $rec) {
                                    $spkItem = $rec['item'];
                                    $produk = $spkItem->produk;
                                    if (!$produk || $produk->is_metric !== true) continue;
                                    $metricUnit = $produk->metric_unit ?: 'cm';
                                    $panjang = (float) ($spkItem->panjang ?? 0);
                                    $lebar   = (float) ($spkItem->lebar ?? 0);
                                    $jumlah  = (float) ($spkItem->jumlah ?? 0);
                                    if ($panjang <= 0 || $lebar <= 0 || $jumlah <= 0) continue;
                                    switch (strtolower($metricUnit)) {
                                        case 'mm': $panjang /= 1000; $lebar /= 1000; break;
                                        case 'cm': $panjang /= 100;  $lebar /= 100;  break;
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
                              <div class="badge bg-secondary text-white mb-1">{{ count($items) }} item</div>
                              @if($totalMetric > 0)
                                <div class="small text-muted">
                                  Total: {{ number_format($totalMetric, 2, ',', '.') }} {{ strtolower($metricUnitLabel ?? 'cm') }}²
                                </div>
                              @endif
                            </div>
                          </div>
                        </button>
                      </h2>

                      <div id="modal-collapse-{{ $accordionId }}" class="accordion-collapse collapse"
                          aria-labelledby="modal-heading-{{ $accordionId }}"
                          data-bs-parent="#{{ $accordionIdBase }}">
                        <div class="accordion-body">
                          <div class="table-responsive">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                              <div class="form-check">
                                @php
                                  $remainingAccordionItems = collect($items)->filter(function ($rec) {
                                    $workflowStep = $rec['workflow_step'] ?? [];
                                    return (int) ($workflowStep['remaining_take_qty'] ?? 0) > 0;
                                  })->count();
                                @endphp
                                <input class="form-check-input modal-cek-all-accordion" type="checkbox"
                                      data-modal-accordion-id="{{ $accordionId }}"
                                      {{ $remainingAccordionItems === 0 ? 'disabled' : '' }}>
                                <label class="form-check-label">
                                  Pilih semua item pada tabel ini untuk <strong>diambil</strong>
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
                                      $filePendukung = is_string($filePendukungRaw) ? (json_decode($filePendukungRaw, true) ?: []) : (array) $filePendukungRaw;
                                      $firstFile  = is_array($filePendukung) && count($filePendukung) ? $filePendukung[0] : null;
                                      $thumbUrl   = null;
                                      $isPdfFirst = false;
                                      if ($firstFile && !empty($firstFile['path'])) {
                                          $thumbUrl   = route('backend.preview-file', ['path' => $firstFile['path']]);
                                          $isPdfFirst = strtolower((string) ($firstFile['type'] ?? '')) === 'pdf';
                                      }

                                      $workflowStep = $rec['workflow_step'] ?? [];
                                      $stepIndex = (int) ($workflowStep['step_index'] ?? 1);
                                      $stepTotal = (int) ($workflowStep['step_total'] ?? 1);
                                      $eligibleQty = (int) ($workflowStep['eligible_qty'] ?? 0);
                                      $totalDiambil = (int) ($workflowStep['queued_qty_step'] ?? 0);
                                      $sisaAmbil = (int) ($workflowStep['remaining_take_qty'] ?? 0);
                                      $pctAmbil = $eligibleQty > 0 ? min(100, round(($totalDiambil / $eligibleQty) * 100, 1)) : 0.0;
                                      $sudahCetak = (int) ($workflowStep['printed_qty_step'] ?? 0);
                                      $pctCetak = (float) ($workflowStep['progress_step_pct'] ?? 0);
                                      $sisa = (int) ($workflowStep['remaining_print_qty'] ?? 0);
                                    @endphp

                                    <tr class="{{ $sisaAmbil <= 0 ? 'item-selesai' : '' }}">
                                      @if($firstRow)
                                        <td rowspan="{{ $rowspan }}" class="fw-bold align-top">
                                          <div class="d-flex align-items-start gap-2">
                                            <div class="form-check mt-1">
                                              @php
                                                $remainingItems = collect($rows)->filter(function ($rec) {
                                                  $workflowStep = $rec['workflow_step'] ?? [];
                                                  return (int) ($workflowStep['remaining_take_qty'] ?? 0) > 0;
                                                })->count();
                                              @endphp
                                              <input type="checkbox"
                                                    class="form-check-input modal-cek-spk"
                                                    data-spk-id="{{ $spkRow->id }}"
                                                    data-modal-accordion-id="{{ $accordionId }}"
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
                                              <small class="text-muted d-block">{{ $spkDate->format('d/m/Y') }}</small>
                                              @if($spkDate->isPast())
                                                <small class="text-muted">{{ $diff->days }} hari
                                                  @if($diff->h > 0) {{ $diff->h }} jam @endif
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
                                        @if($luasText)<div class="text-muted small">{{ $luasText }}</div>@endif
                                      </td>
                                    <td class="text-end">{{ number_format($eligibleQty, 0, ',', '.') }}</td>
                                      <td>{{ $spkItem->satuan }}</td>

                                      <td class="text-end">
                                        <div class="small fw-semibold mb-1">Ambil: {{ $pctAmbil }}%</div>
                                        <div class="progress mb-2" style="height:6px;">
                                          <div class="progress-bar bg-success" role="progressbar"
                                              style="width: {{ $pctAmbil }}%;"
                                              aria-valuenow="{{ $pctAmbil }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="small text-muted mb-2">
                                          Step {{ $stepIndex }}/{{ $stepTotal }} • Diambil: {{ number_format($totalDiambil,0,',','.') }} / {{ number_format($eligibleQty,0,',','.') }}
                                          (Sisa ambil: {{ number_format($sisaAmbil,0,',','.') }})
                                        </div>
                                        <div class="small fw-semibold mb-1">Cetak: {{ $pctCetak }}%</div>
                                        <div class="progress" style="height:6px;">
                                          <div class="progress-bar {{ $pctCetak >= 100 ? 'bg-success' : ($pctCetak >= 50 ? 'bg-warning' : 'bg-primary') }}"
                                              role="progressbar"
                                              style="width: {{ $pctCetak }}%;"
                                              aria-valuenow="{{ $pctCetak }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="small text-muted mt-1">
                                          Sisa cetak: {{ number_format($sisa,0,',','.') }} {{ $spkItem->satuan }}
                                        </div>
                                      </td>

                                      <td class="text-center">
                                        <input type="checkbox"
                                              class="form-check-input modal-ambil-item-checkbox"
                                              value="{{ $spkItem->id }}"
                                              data-modal-accordion-id="{{ $accordionId }}"
                                              data-spk-id="{{ $spkRow->id }}"
                                              data-nomor-spk="{{ $spkRow->nomor_spk }}"
                                              data-pelanggan="{{ optional($spkRow->pelanggan)->nama ?? '-' }}"
                                              data-item="{{ $spkItem->nama_produk }}"
                                              data-qty="{{ $eligibleQty }}"
                                              data-sisa-ambil="{{ $sisaAmbil }}"
                                              @disabled($sisaAmbil <= 0)>
                                      </td>
                                      <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm {{ $sisaAmbil <= 0 ? 'btn-secondary disabled' : 'btn-success' }} btn-open-ambil-modal"
                                                {{ $sisaAmbil <= 0 ? 'disabled' : '' }}
                                                data-spk-item-id="{{ $spkItem->id }}"
                                                data-nomor-spk="{{ $spkRow->nomor_spk }}"
                                                data-pelanggan="{{ optional($spkRow->pelanggan)->nama ?? '-' }}"
                                                data-nama-item="{{ $spkItem->nama_produk }}"
                                                data-sisa-ambil="{{ $sisaAmbil }}">
                                          Ambil
                                        </button>
                                      </td>
                                    </tr>
                                  @endforeach
                                @empty
                                  <tr>
                                    <td colspan="10" class="text-center text-muted">
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

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-target="#modalAmbilPekerjaan">Tutup</button>
        </div>
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

  <!-- <script>
  document.querySelectorAll('#operatorTabs button').forEach(btn => {
      btn.addEventListener('shown.bs.tab', function () {

          document.querySelectorAll('.tab-card').forEach(el => {
              el.classList.remove('active');
          });

          this.classList.add('active');
      });
  });
  </script> -->

  <script>
    function syncMultiAmbilButtons() {
      const checked = document.querySelectorAll('.ambil-item-checkbox:checked');

      document.querySelectorAll('.btnMultiAmbil').forEach(btn => {
          const hasChecked = checked.length > 0;

          btn.disabled = !hasChecked;

          if (hasChecked) {
              btn.classList.remove('btn-secondary');
              btn.classList.add('btn-success');
          } else {
              btn.classList.remove('btn-success');
              btn.classList.add('btn-secondary');
          }
      });
    }
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

      const ambilModalEl = document.getElementById('ambilModal');
      const ambilModal = ambilModalEl ? new bootstrap.Modal(ambilModalEl) : null;

      const formAmbil = document.getElementById('formAmbil');
      const ambilInputs = document.getElementById('ambil_inputs');
      const ambilMesinId = document.getElementById('ambil_mesin_id');
      const ambilJumlah = document.getElementById('ambil_jumlah');

      function getActiveMesinId() {
        const activeSub = document.querySelector('[id^="mesinSubTabsInner-"] .nav-link.active');
        if (activeSub && activeSub.dataset && activeSub.dataset.mesinId) {
          return String(activeSub.dataset.mesinId);
        }

        // 2) Tidak ada sub-tab aktif -> cek tab tipe mesin yang sedang aktif
        const activeTabBtn = document.querySelector('#operatorTabs .tab-card.active[data-mesin-list]');
        if (activeTabBtn) {
          try {
            const raw = activeTabBtn.getAttribute('data-mesin-list');
            const list = raw ? JSON.parse(raw) : [];

            // Jika hanya 1 mesin di group ini, auto-pilih mesin tersebut
            if (Array.isArray(list) && list.length === 1 && list[0].id) {
              return String(list[0].id);
            }

            // Kalau mesin >1 tapi belum pilih sub-tab -> paksa user pilih dulu
            return '';
          } catch (e) {
            return '';
          }
        }

        // 3) Tidak ada tab tipe mesin aktif (misal lagi di "Pekerjaan Saya")
        return '';
      }

      function setAmbilHiddenIds(ids) {
        ambilInputs.innerHTML = '';
        ids.forEach(id => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'spk_item_ids[]';
          input.value = String(id);
          ambilInputs.appendChild(input);
        });
      }

      function openAmbilModalSingle(btn) {
        const itemId = btn.getAttribute('data-spk-item-id');
        const nomorSpk = btn.getAttribute('data-nomor-spk') || '-';
        const pelanggan = btn.getAttribute('data-pelanggan') || '-';
        const namaItem = btn.getAttribute('data-nama-item') || '-';
        const sisaAmbil = parseInt(btn.getAttribute('data-sisa-ambil') || '0', 10) || 0;

        const mesinId = getActiveMesinId();
        if (!mesinId) {
          Swal.fire({ icon: 'warning', title: 'Mesin belum dipilih', text: 'Silakan pilih mesin dulu.' });
          return;
        }

        ambilMesinId.value = mesinId;
        setAmbilHiddenIds([itemId]);

        document.getElementById('ambil_nomor_spk').textContent = nomorSpk;
        document.getElementById('ambil_pelanggan').textContent = pelanggan;
        document.getElementById('ambil_nama_item').textContent = namaItem;
        document.getElementById('ambil_sisa').textContent = sisaAmbil.toLocaleString('id-ID');

        ambilJumlah.value = '';
        ambilJumlah.max = String(Math.max(0, sisaAmbil));
        ambilModal.show();
      }

      function openAmbilModalMulti(selectedCheckboxes) {
        const mesinId = getActiveMesinId();
        if (!mesinId) {
          Swal.fire({ icon: 'warning', title: 'Mesin belum dipilih', text: 'Silakan pilih mesin dulu.' });
          return;
        }

        const ids = Array.from(selectedCheckboxes).map(cb => cb.value);
        ambilMesinId.value = mesinId;
        setAmbilHiddenIds(ids);

        document.getElementById('ambil_nomor_spk').textContent = 'Multi';
        document.getElementById('ambil_pelanggan').textContent = '-';
        document.getElementById('ambil_nama_item').textContent = `${ids.length} item`;
        document.getElementById('ambil_sisa').textContent = '-';

        ambilJumlah.value = '';
        ambilJumlah.removeAttribute('max');
        ambilModal.show();
      }

      document.addEventListener('click', function (e) {
        const btnSingle = e.target.closest('.btn-open-ambil-modal');
        // Skip jika tombol berasal dari dalam modal Ambil Pekerjaan (ditangani handler modal)
        if (btnSingle && !btnSingle.closest('#modalAmbilPekerjaan')) {
          openAmbilModalSingle(btnSingle);
          return;
        }

        const btnMulti = e.target.closest('.btnMultiAmbil');
        // Skip jika tombol berasal dari dalam modal Ambil Pekerjaan
        if (btnMulti && !btnMulti.closest('#modalAmbilPekerjaan')) {
          const checked = document.querySelectorAll('.ambil-item-checkbox:checked');
          if (!checked.length) {
            return;
          }
          const mesinId = getActiveMesinId();
          if (!mesinId) {
            Swal.fire({
              icon: 'warning',
              title: 'Mesin belum dipilih',
              text: 'Silakan pilih mesin dulu.'
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
                  <th class="text-end">Qty Diambil (baru)</th>
                </tr>
              </thead>
              <tbody>
          `;
          let totalItem = 0;
          checked.forEach(cb => {
            const spk    = cb.dataset.nomorSpk || '-';
            const pelanggan = cb.dataset.pelanggan || '-';
            const item   = cb.dataset.item || '-';
            const sisaAmbil = parseInt(cb.dataset.sisaAmbil || '0', 10) || 0;
            
            if (sisaAmbil <= 0) {
              return;
            }
            html += `
              <tr>
                <td>${spk}</td>
                <td>${pelanggan}</td>
                <td>${item}</td>
                <td class="text-end">${sisaAmbil.toLocaleString('id-ID')}</td>
              </tr>
            `;
            totalItem++;
          });
          html += `
              </tbody>
            </table>
            </div>
            <div class="mt-3 text-start">
              <strong>Total item dipilih: ${totalItem}</strong>
            </div>
          `;
          if (totalItem === 0) {
            Swal.fire({
              icon: 'info',
              title: 'Tidak ada qty yang bisa diambil',
              text: 'Semua item yang dipilih sudah tidak punya sisa ambil.'
            });
            return;
          }
          Swal.fire({
            title: 'Konfirmasi Multi Ambil (Semua Sisa)',
            html: html,
            icon: 'info',
            width: 800,
            showCancelButton: true,
            confirmButtonText: 'Ya, ambil semua',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#198754'
          }).then((result) => {
            if (!result.isConfirmed) {
              return;
            }

            const form = btnMulti.closest('.bulkAmbilForm');
            if (!form) return;
            const bulkInputs = form.querySelector('.bulkAmbilInputs');
            const mesinInput = form.querySelector('.bulkAmbilMesinId');
            if (!bulkInputs || !mesinInput) return;
            bulkInputs.innerHTML = '';
            checked.forEach(cb => {
              const sisaAmbil = parseInt(cb.dataset.sisaAmbil || '0', 10) || 0;
              if (sisaAmbil <= 0) {
                return;
              }
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = 'spk_item_ids[]';
              input.value = cb.value;
              bulkInputs.appendChild(input);
            });
            mesinInput.value = mesinId;
            Swal.fire({
              title: 'Memproses...',
              text: 'Sedang memproses multi ambil',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
            form.submit();
          });
          return;
        }
      });

      document.addEventListener('change', function (e) {
        if (e.target.classList.contains('ambil-item-checkbox')) {
          syncMultiAmbilButtons();
        }
      });

     
      if (formAmbil) {
        formAmbil.addEventListener('submit', function (e) {
          const val = parseInt(ambilJumlah.value || '0', 10) || 0;
          if (val <= 0) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Jumlah ambil minimal 1.' });
            return;
          }
          const max = ambilJumlah.getAttribute('max');
          if (max && val > parseInt(max, 10)) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Jumlah ambil melebihi sisa yang tersedia.' });
          }
        });
      }

      syncMultiAmbilButtons();
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
            const first = checked[0];
            const mesinId = first.dataset.mesinId || '';
            document.getElementById('bulkCetakMesinId').value = mesinId;

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
      const bulkMesinInput= document.getElementById('bulkCetakMesinId');

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

        const mesinIds = Array.from(checked).map(cb => cb.dataset.mesinId || '').filter(Boolean);
        const uniqueMesinIds = Array.from(new Set(mesinIds));

        if (!uniqueMesinIds.length) {
          bulkMesinInput.value = '';
        } else if (uniqueMesinIds.length === 1) {
          bulkMesinInput.value = uniqueMesinIds[0];
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Multi cetak dibatasi per mesin',
            text: 'Pilih item dengan mesin yang sama untuk multi cetak.',
          });
          bulkBtn.disabled = true;
          bulkMesinInput.value = '';
          return;
        }

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

      (function () {
        const bulkMesinInput = document.getElementById('bulkCetakMesinId');

        function clearAllMesinSubTabs() {
          document.querySelectorAll('[id^="mesinSubTabsContainer-"]').forEach(c => {
            c.classList.add('d-none');
          });
          document.querySelectorAll('[id^="mesinSubTabsInner-"]').forEach(i => {
            i.innerHTML = '';
          });
          if (bulkMesinInput) {
            bulkMesinInput.value = '';
          }
        }

        function renderMesinSubTabsForSlug(slug, mesinList) {
          const container = document.getElementById('mesinSubTabsContainer-' + slug);
          const inner = document.getElementById('mesinSubTabsInner-' + slug);
          if (!container || !inner) return;

          inner.innerHTML = '';
          if (bulkMesinInput) {
            bulkMesinInput.value = '';
          }

          if (!mesinList || mesinList.length <= 1) {
            container.classList.add('d-none');
            return;
          }

          container.classList.remove('d-none');

          mesinList.forEach(function (m, idx) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'nav-link btn btn-sm ' + (idx === 0 ? 'active' : '');
            btn.setAttribute('role', 'tab');
            btn.dataset.mesinId = m.id || '';
            btn.textContent = m.nama_mesin || ('Mesin #' + (m.id || idx));
            btn.addEventListener('click', function () {
              inner.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
              btn.classList.add('active');
              if (bulkMesinInput) {
                bulkMesinInput.value = btn.dataset.mesinId || '';
              }
            });
            inner.appendChild(btn);
          });

          const first = inner.querySelector('.nav-link');
          if (first && bulkMesinInput) {
            bulkMesinInput.value = first.dataset.mesinId || '';
          }
        }

        // Tab tipe mesin
        document.querySelectorAll('#operatorTabs [data-mesin-list]').forEach(function (btn) {
          const slug = btn.getAttribute('data-slug');
          btn.addEventListener('shown.bs.tab', function () {
            let list = [];
            try {
              const raw = btn.getAttribute('data-mesin-list');
              list = raw ? JSON.parse(raw) : [];
            } catch (e) {}
            clearAllMesinSubTabs();
            if (slug) {
              renderMesinSubTabsForSlug(slug, list);
            }
          });
        });

        // Tab "Pekerjaan Saya"
        const pekerjaanSayaTab = document.getElementById('tab-pekerjaan-saya');
        if (pekerjaanSayaTab) {
          pekerjaanSayaTab.addEventListener('shown.bs.tab', function () {
            clearAllMesinSubTabs();
          });
        }

        // Inisialisasi pertama: jika pada load sudah ada tab tipe mesin aktif
        const activeMesinTab = document.querySelector('#operatorTabs [data-mesin-list].active');
        if (activeMesinTab) {
          const slug = activeMesinTab.getAttribute('data-slug');
          let list = [];
          try {
            const raw = activeMesinTab.getAttribute('data-mesin-list');
            list = raw ? JSON.parse(raw) : [];
          } catch (e) {}
          clearAllMesinSubTabs();
          if (slug) {
            renderMesinSubTabsForSlug(slug, list);
          }
        } else {
          // default: Pekerjaan Saya aktif
          clearAllMesinSubTabs();
        }
      })();

      (function () {
        const modalEl = document.getElementById('modalGlobalCetakHistory');
        if (!modalEl) return;

        const tbody = document.getElementById('globalHistoryBody');
        const paginationEl = document.getElementById('globalHistoryPagination');
        const rentangGroup = document.getElementById('rentangDateGroup');
        const rentangToGroup = document.getElementById('rentangDateToGroup');

        function toggleRentangInputs() {
          const selected = document.querySelector('input[name="filterHistory"]:checked')?.value;
          const show = selected === 'rentang';
          rentangGroup.classList.toggle('d-none', !show);
          rentangToGroup.classList.toggle('d-none', !show);
        }

        document.querySelectorAll('input[name="filterHistory"]').forEach(radio => {
          radio.addEventListener('change', toggleRentangInputs);
        });

        function loadGlobalHistory(page = 1) {
          const filter = document.querySelector('input[name="filterHistory"]:checked')?.value || 'bulan_ini';
          const params = new URLSearchParams({ filter, page });

          if (filter === 'rentang') {
            const fromInput = document.getElementById('historyDateFrom');
            const toInput = document.getElementById('historyDateTo');
            const from = fromInput?.value?.trim();
            const to = toInput?.value?.trim();
            
            if (!from || !to) {
              Swal.fire({ icon: 'warning', text: 'Silakan pilih tanggal Dari dan Sampai!' });
              return;
            }

            params.set('date_from', from);
            params.set('date_to', to);
          }

          tbody.innerHTML = '<tr><td colspan="7" class="text-center py-3"><span class="spinner-border spinner-border-sm me-1"></span> Memuat...</td></tr>';

          fetch('{{ route("pekerjaan.operator-cetak.history-logs") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
          })
          .then(r => r.json())
          .then(res => {
            const data = res.data || [];
            const meta = res.meta || {};
            const links = res.links || [];

            if (!data.length) {
              tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>';
            } else {
              tbody.innerHTML = data.map((row, idx) => {
                const no = (meta.from || 0) + idx;
                const statusBadge = row.is_batalkan 
                  ? '<span class="badge bg-danger">Dibatalkan</span>'
                  : '<span class="badge bg-success">Selesai</span>';
                const actionHtml = row.can_cancel
                  ? `<button type="button"
                        class="btn btn-sm btn-outline-danger btn-global-batalkan-log"
                        data-log-id="${row.id}">
                        Batalkan
                    </button>`
                  : '<span class="text-muted small">-</span>';
                return `<tr>
                  <td class="text-center">${no}</td>
                  <td>${
                    new Date(row.tanggal_label.split(' ')[0].split('/').reverse().join('-') + 'T' + row.tanggal_label.split(' ')[1] + ':00')
                      .toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false })
                  }</td>
                  <td>${escapeHtml(row.nomor_spk || '-')}</td>
                  <td>${escapeHtml(row.nama_produk || '-')}</td>
                  <td class="text-end">${escapeHtml(row.jumlah_formatted || '0')}</td>
                  <td>${escapeHtml(row.operator || '-')}</td>
                  <td>${statusBadge}</td>
                  <td>${actionHtml}</td>
                </tr>`;
              }).join('');
            }

            // Pagination
            const prevLink = links.find(l => l.label === '&laquo; Previous');
            const nextLink = links.find(l => l.label === 'Next &raquo;');
            const pageLinks = links.filter(l => l.label !== '&laquo; Previous' && l.label !== 'Next &raquo;' && l.label !== '...');

            if (meta.last_page > 1) {
              let pagHtml = '<ul class="pagination pagination-sm mb-0">';
              if (prevLink?.url) pagHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${meta.current_page - 1}">Sebelumnya</a></li>`;
              pageLinks.forEach(l => {
                const isActive = l.active ? ' active' : '';
                pagHtml += `<li class="page-item${isActive}"><a class="page-link" href="#" data-page="${l.label}">${l.label}</a></li>`;
              });
              if (nextLink?.url) pagHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${meta.current_page + 1}">Selanjutnya</a></li>`;
              pagHtml += '</ul>';
              paginationEl.innerHTML = pagHtml;

              paginationEl.querySelectorAll('.page-link').forEach(a => {
                a.addEventListener('click', function(e) {
                  e.preventDefault();
                  const p = this.getAttribute('data-page');
                  if (p) loadGlobalHistory(parseInt(p, 10));
                });
              });
            } else {
              paginationEl.innerHTML = '';
            }
          })
          .catch(() => {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-3">Gagal memuat data.</td></tr>';
            paginationEl.innerHTML = '';
          });
        }

        function escapeHtml(s) {
          const div = document.createElement('div');
          div.textContent = s;
          return div.innerHTML;
        }

        document.getElementById('btnApplyFilterHistory')?.addEventListener('click', () => loadGlobalHistory(1));
        modalEl.addEventListener('shown.bs.modal', () => loadGlobalHistory(1));

        modalEl.addEventListener('shown.bs.modal', toggleRentangInputs);
      })();

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

        if (el.classList.contains('ambil-item-checkbox')) {
          const spkId = el.dataset.spkId;
          const accordionId = el.dataset.accordionId;
          const spkItems = document.querySelectorAll(
            `.ambil-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]`
          );
          const spkChecked = document.querySelectorAll(
            `.ambil-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]:checked`
          );
          const spkMaster = document.querySelector(
            `.cek-spk[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]`
          );
          if (spkMaster) {
            spkMaster.checked = spkItems.length === spkChecked.length;
          }
          const accItems = document.querySelectorAll(
            `.ambil-item-checkbox[data-accordion-id="${accordionId}"]`
          );
          const accChecked = document.querySelectorAll(
            `.ambil-item-checkbox[data-accordion-id="${accordionId}"]:checked`
          );
          const accMaster = document.querySelector(
            `.cek-all-accordion[data-accordion-id="${accordionId}"]`
          );
          if (accMaster) {
            accMaster.checked = accItems.length === accChecked.length;
          }
          syncMultiAmbilButtons();
        }


        // =====================
        // SPK SELECT
        // =====================
        // if(el.classList.contains("cek-spk")){
        //   const spkId = el.dataset.spkId;
        //   const accordionId = el.dataset.accordionId;

        //   document
        //     .querySelectorAll(`.cetak-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]:not([data-locked="1"])`)
        //     .forEach(cb => cb.checked = el.checked);

        //   const accItems = document.querySelectorAll(
        //     `.cetak-item-checkbox[data-accordion-id="${accordionId}"]:not([data-locked="1"])`
        //   );

        //   const accChecked = document.querySelectorAll(
        //     `.cetak-item-checkbox[data-accordion-id="${accordionId}"]:checked`
        //   );

        //   const accMaster = document.querySelector(
        //     `.cek-all-accordion[data-accordion-id="${accordionId}"]`
        //   );

        //   if(accMaster){
        //     accMaster.checked = accItems.length === accChecked.length;
        //   }

        //   syncBulk();
        // }
        if (el.classList.contains('cek-spk')) {
          const spkId = el.dataset.spkId;
          const accordionId = el.dataset.accordionId;
          document
            .querySelectorAll(`.ambil-item-checkbox[data-spk-id="${spkId}"][data-accordion-id="${accordionId}"]`)
            .forEach(cb => { cb.checked = el.checked; });
          const accItems = document.querySelectorAll(
            `.ambil-item-checkbox[data-accordion-id="${accordionId}"]`
          );
          const accChecked = document.querySelectorAll(
            `.ambil-item-checkbox[data-accordion-id="${accordionId}"]:checked`
          );
          const accMaster = document.querySelector(
            `.cek-all-accordion[data-accordion-id="${accordionId}"]`
          );
          if (accMaster) {
            accMaster.checked = accItems.length === accChecked.length;
          }
          syncMultiAmbilButtons();
        }


        // =====================
        // ACCORDION SELECT
        // =====================
        // if(el.classList.contains("cek-all-accordion")){

        //   const accordionId = el.dataset.accordionId;

        //   document
        //     .querySelectorAll(`.cetak-item-checkbox[data-accordion-id="${accordionId}"]:not([data-locked="1"])`)
        //     .forEach(cb => cb.checked = el.checked);

        //   document
        //     .querySelectorAll(`.cek-spk[data-accordion-id="${accordionId}"]`)
        //     .forEach(spk => spk.checked = el.checked);

        //   syncBulk();
        // }

        if (el.classList.contains('cek-all-accordion')) {
          const accordionId = el.dataset.accordionId;
          document
            .querySelectorAll(`.ambil-item-checkbox[data-accordion-id="${accordionId}"]`)
            .forEach(cb => { cb.checked = el.checked; });
          document
            .querySelectorAll(`.cek-spk[data-accordion-id="${accordionId}"]`)
            .forEach(spk => { spk.checked = el.checked; });
          syncMultiAmbilButtons();
        }

      });

      syncMultiAmbilButtons();

      document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.classList.contains('form-batal-ambil')) {
          return;
        }

        e.preventDefault();

        Swal.fire({
          title: 'Batalkan ambil pekerjaan?',
          text: 'Item ini akan dihapus dari antrian pekerjaan Anda.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, batalkan',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });

      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-global-batalkan-log');
        if (!btn) return;

        const logId = btn.getAttribute('data-log-id');
        if (!logId) return;

        Swal.fire({
          title: 'Batalkan cetak?',
          text: 'Log cetak akan dibatalkan.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, batalkan',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#d33'
        }).then((result) => {
          if (!result.isConfirmed) return;

          fetch(`{{ route('pekerjaan.operator-cetak.destroy-history', ['log' => 'LOG_ID_PLACEHOLDER']) }}`
            .replace('LOG_ID_PLACEHOLDER', logId), {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          })
          .then(async (res) => {
            const data = await res.json();

            if (!res.ok || !data.success) {
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
              text: 'Cetakan berhasil dibatalkan.'
            }).then(() => {
              location.reload(); 
            });

            // const currentPage = 1;
            // loadGlobalHistory(currentPage);
          })
          .catch(() => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Terjadi kesalahan jaringan.'
            });
          });
        });
      });

      document.querySelectorAll('.btn-open-cetak-modal').forEach(btn => {
        btn.addEventListener('click', function () {
          const mesinId = btn.getAttribute('data-mesin-id') || '';
          document.getElementById('cetak_mesin_id').value = mesinId;
          const spkItemId = btn.getAttribute('data-spk-item-id');
          const nomorSpk = btn.getAttribute('data-nomor-spk') || '';
          const pelanggan = btn.getAttribute('data-pelanggan') || '';
          const namaItem = btn.getAttribute('data-nama-item') || '';
          // const qty = parseInt(btn.getAttribute('data-qty') || '0', 10);
          // const sudah = parseInt(btn.getAttribute('data-sudah') || '0', 10);
          // const sisa = parseInt(btn.getAttribute('data-sisa') || '0', 10);
          const qtyTotal   = parseInt(btn.getAttribute('data-qty') || '0', 10);        
          const qtyDiambil = parseInt(btn.getAttribute('data-diambil') || '0', 10);   
          const sudah      = parseInt(btn.getAttribute('data-sudah') || '0', 10);
          const sisaGlobal = parseInt(btn.getAttribute('data-sisa') || '0', 10);

          const filePath = btn.getAttribute('data-file-path') || '';
          const fileType = (btn.getAttribute('data-file-type') || '').toLowerCase();
          const fileName = btn.getAttribute('data-file-name') || '';

          let riwayat = [];
          try { riwayat = JSON.parse(btn.getAttribute('data-riwayat') || '[]'); } catch (e) { riwayat = []; }

          document.getElementById('cetak_spk_item_id').value = spkItemId;
          document.getElementById('cetak_nomor_spk').textContent = nomorSpk;
          document.getElementById('cetak_pelanggan').textContent = pelanggan;
          document.getElementById('cetak_nama_item').textContent = namaItem;
          // document.getElementById('cetak_qty').textContent = qty.toLocaleString('id-ID');
          // document.getElementById('cetak_sudah').textContent = sudah.toLocaleString('id-ID');
          // document.getElementById('cetak_sisa').textContent = sisa.toLocaleString('id-ID');
          document.getElementById('cetak_qty').textContent   = qtyDiambil.toLocaleString('id-ID');
          document.getElementById('cetak_sudah').textContent = sudah.toLocaleString('id-ID');
          document.getElementById('cetak_sisa').textContent  = sisaGlobal.toLocaleString('id-ID');
          document.getElementById('cetak_sisa_label').textContent = sisaGlobal.toLocaleString('id-ID');
          document.getElementById('cetak_sisa_setelah').innerHTML = "Sisa setelah cetak: -";

          const jumlahInput = document.getElementById('cetak_jumlah');
          const maxCetak = Math.max(0, Math.min(qtyDiambil, sisaGlobal));
          jumlahInput.value = '';
          jumlahInput.max = String(maxCetak);
          if (maxCetak <= 0) {
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      const formAmbil = document.getElementById('formAmbil');
      const btnAmbil  = document.getElementById('btnAmbil');

      if (!formAmbil || !btnAmbil) return;

      formAmbil.addEventListener('submit', function () {

        btnAmbil.disabled = true;

        const text = btnAmbil.querySelector('.btn-text');
        const loading = btnAmbil.querySelector('.btn-loading');

        if (text) text.classList.add('d-none');
        if (loading) loading.classList.remove('d-none');

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

<div class="modal fade" id="ambilModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm">
      <form method="POST" action="{{ route('pekerjaan.operator-cetak.ambil') }}" id="formAmbil">
        @csrf

        <div class="modal-header bg-light py-2">
          <h6 class="modal-title mb-0 fw-semibold">
            <i class="fa fa-hand-paper me-1 text-success"></i> Ambil Pekerjaan
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="mesin_id" id="ambil_mesin_id">
          <div id="ambil_inputs"></div>

          <!-- Info pekerjaan -->
          <div class="border rounded p-2 mb-3 bg-light small">
            <div class="row g-2">

              <div class="col-12">
                <div class="text-muted">SPK</div>
                <div class="fw-semibold" id="ambil_nomor_spk">-</div>
              </div>

              <div class="col-12">
                <div class="text-muted">Pelanggan</div>
                <div class="fw-semibold" id="ambil_pelanggan">-</div>
              </div>

              <div class="col-12">
                <div class="text-muted">Item</div>
                <div class="fw-semibold" id="ambil_nama_item">-</div>
              </div>

              <div class="col-12">
                <div class="text-muted">Sisa yang bisa diambil</div>
                <div class="fw-bold text-danger fs-6">
                  <span id="ambil_sisa">0</span>
                </div>
              </div>

            </div>
          </div>

          <!-- Input jumlah -->
          <div>
            <label class="form-label small fw-semibold">
              Jumlah yang diambil
            </label>

            <div class="input-group">
              <span class="input-group-text">
                <i class="fa fa-sort-numeric-up"></i>
              </span>
              <input type="number"
                     name="jumlah"
                     id="ambil_jumlah"
                     class="form-control text-end fw-bold"
                     min="1"
                     required>
            </div>

            <div class="form-text">
              Masukkan jumlah item yang akan diambil untuk dicetak.
            </div>
          </div>

        </div>

        <div class="modal-footer py-2">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Tutup
          </button>

          <button type="submit" class="btn btn-success px-4" id="btnAmbil">
            <span class="btn-text">
              <i class="fa fa-check me-1"></i> Ambil
            </span>

            <span class="btn-loading d-none">
              <span class="spinner-border spinner-border-sm me-1"></span>
              Memproses...
            </span>
          </button>
        </div>

      </form>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-target="#cetakProgressModal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="spk_item_id" id="cetak_spk_item_id">
          <input type="hidden" name="mesin_id" id="cetak_mesin_id">

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
                      <div class="text-muted small">Pekerjaan</div>
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
          <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-bs-target="#cetakProgressModal">Tutup</button>
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

<div class="modal fade" id="modalGlobalCetakHistory" tabindex="-1" aria-labelledby="modalGlobalCetakHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalGlobalCetakHistoryLabel">
          <i class="fa fa-history me-1"></i> History Cetak Semua Log
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 align-items-end mb-3">
          <!-- Filter radio -->
          <div class="col-auto">
            <label class="form-label mb-0 small">Filter:</label>
            <div class="btn-group" role="group" id="filterHistoryBtns">
              <input type="radio" class="btn-check" name="filterHistory" id="filterHariIni" value="hari_ini">
              <label class="btn btn-outline-secondary btn-sm" for="filterHariIni">Hari ini</label>

              <input type="radio" class="btn-check" name="filterHistory" id="filterKemarin" value="kemarin">
              <label class="btn btn-outline-secondary btn-sm" for="filterKemarin">Kemarin</label>

              <input type="radio" class="btn-check" name="filterHistory" id="filterBulanIni" value="bulan_ini" checked>
              <label class="btn btn-outline-secondary btn-sm" for="filterBulanIni">Bulan ini</label>

              <input type="radio" class="btn-check" name="filterHistory" id="filterRentang" value="rentang">
              <label class="btn btn-outline-secondary btn-sm" for="filterRentang">Rentang</label>
            </div>
          </div>

          <!-- Input Rentang -->
          <div class="col-auto d-none" id="rentangDateGroup">
            <label class="form-label mb-0 small">Dari</label>
            <input type="date" id="historyDateFrom" class="form-control form-control-sm" name="date_from">
          </div>
          <div class="col-auto d-none" id="rentangDateToGroup">
            <label class="form-label mb-0 small">Sampai</label>
            <input type="date" id="historyDateTo" class="form-control form-control-sm" name="date_to">
          </div>

          <!-- Tombol Terapkan -->
          <div class="col-auto">
            <button type="button" class="btn btn-primary btn-sm" id="btnApplyFilterHistory">
              <i class="fa fa-filter me-1"></i> Terapkan
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 50px;">#</th>
                <th>Tanggal/Jam</th>
                <th>No. SPK</th>
                <th>Produk</th>
                <th class="text-end">Jumlah</th>
                <th>Operator</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 140px;">Aksi</th>
              </tr>
            </thead>
            <tbody id="globalHistoryBody">
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Klik tombol Terapkan untuk memuat data.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <nav aria-label="Pagination history" class="mt-3" id="globalHistoryPagination">
          <!-- Pagination links diisi via JS -->
        </nav>
      </div>
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
  {{-- =========================================================
       Modal Ambil Pekerjaan
       ========================================================= --}}
  <script>
  (function () {
    'use strict';

    const ambilModalEl  = document.getElementById('ambilModal');
    const ambilModal    = ambilModalEl ? bootstrap.Modal.getOrCreateInstance(ambilModalEl) : null;
    const ambilMesinId  = document.getElementById('ambil_mesin_id');
    const ambilInputs   = document.getElementById('ambil_inputs');
    const ambilJumlah   = document.getElementById('ambil_jumlah');

    function modalSetAmbilHiddenIds(ids) {
      if (!ambilInputs) return;
      ambilInputs.innerHTML = '';
      ids.forEach(function (id) {
        var inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'spk_item_ids[]';
        inp.value = String(id);
        ambilInputs.appendChild(inp);
      });
    }

    function modalGetActiveMesinId() {
      // Cek sub-tab mesin yang sedang aktif di dalam modal
      var activeSub = document.querySelector('[id^="modalMesinSubTabsInner-"] .nav-link.active');
      if (activeSub && activeSub.dataset && activeSub.dataset.mesinId) {
        return String(activeSub.dataset.mesinId);
      }

      // Tidak ada sub-tab aktif → cek tab tipe mesin yang aktif di dalam modal
      var activeTabBtn = document.querySelector('#modalAmbilTabs .tab-card.active[data-mesin-list]');
      if (activeTabBtn) {
        try {
          var raw  = activeTabBtn.getAttribute('data-mesin-list');
          var list = raw ? JSON.parse(raw) : [];
          if (Array.isArray(list) && list.length === 1 && list[0].id) {
            return String(list[0].id);
          }
          // Lebih dari 1 mesin tapi belum pilih sub-tab
          return '';
        } catch (e) {
          return '';
        }
      }
      return '';
    }

    function modalClearAllMesinSubTabs() {
      document.querySelectorAll('[id^="modalMesinSubTabsContainer-"]').forEach(function (c) {
        c.classList.add('d-none');
      });
      document.querySelectorAll('[id^="modalMesinSubTabsInner-"]').forEach(function (i) {
        i.innerHTML = '';
      });
    }

    function modalRenderMesinSubTabsForSlug(slug, mesinList) {
      var container = document.getElementById('modalMesinSubTabsContainer-' + slug);
      var inner     = document.getElementById('modalMesinSubTabsInner-' + slug);
      if (!container || !inner) return;

      inner.innerHTML = '';

      if (!mesinList || mesinList.length <= 1) {
        container.classList.add('d-none');
        return;
      }

      container.classList.remove('d-none');

      mesinList.forEach(function (m, idx) {
        var btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'nav-link btn btn-sm ' + (idx === 0 ? 'active' : '');
        btn.setAttribute('role', 'tab');
        btn.dataset.mesinId = m.id || '';
        btn.textContent = m.nama_mesin || ('Mesin #' + (m.id || idx));

        btn.addEventListener('click', function () {
          inner.querySelectorAll('.nav-link').forEach(function (el) {
            el.classList.remove('active');
          });
          btn.classList.add('active');
        });

        inner.appendChild(btn);
      });
    }

    // -------------------------------------------------------
    // 5. Sync tombol Multi Ambil di dalam modal
    // -------------------------------------------------------
    function modalSyncMultiAmbilButtons() {
      var checked    = document.querySelectorAll('.modal-ambil-item-checkbox:checked');
      var hasChecked = checked.length > 0;

      document.querySelectorAll('.btnModalMultiAmbil').forEach(function (btn) {
        btn.disabled = !hasChecked;
        if (hasChecked) {
          btn.classList.remove('btn-secondary');
          btn.classList.add('btn-success');
        } else {
          btn.classList.remove('btn-success');
          btn.classList.add('btn-secondary');
        }
      });
    }

    // -------------------------------------------------------
    // 6. Buka ambilModal (single) dari dalam modal
    // -------------------------------------------------------
    function modalOpenAmbilSingle(btn) {
      var itemId    = btn.getAttribute('data-spk-item-id');
      var nomorSpk  = btn.getAttribute('data-nomor-spk')   || '-';
      var pelanggan = btn.getAttribute('data-pelanggan')    || '-';
      var namaItem  = btn.getAttribute('data-nama-item')    || '-';
      var sisaAmbil = parseInt(btn.getAttribute('data-sisa-ambil') || '0', 10) || 0;

      var mesinId = modalGetActiveMesinId();
      if (!mesinId) {
        Swal.fire({ icon: 'warning', title: 'Mesin belum dipilih', text: 'Silakan pilih mesin dulu.' });
        return;
      }

      ambilMesinId.value = mesinId;
      modalSetAmbilHiddenIds([itemId]);

      document.getElementById('ambil_nomor_spk').textContent = nomorSpk;
      document.getElementById('ambil_pelanggan').textContent  = pelanggan;
      document.getElementById('ambil_nama_item').textContent  = namaItem;
      document.getElementById('ambil_sisa').textContent       = sisaAmbil.toLocaleString('id-ID');

      ambilJumlah.value = '';
      ambilJumlah.max   = String(Math.max(0, sisaAmbil));
      ambilModal.show();
    }

    // -------------------------------------------------------
    // 7. Buka ambilModal (multi) dari dalam modal
    // -------------------------------------------------------
    function modalOpenAmbilMulti(btnMulti) {
      var checked = document.querySelectorAll('.modal-ambil-item-checkbox:checked');
      if (!checked.length) return;

      var mesinId = modalGetActiveMesinId();
      if (!mesinId) {
        Swal.fire({ icon: 'warning', title: 'Mesin belum dipilih', text: 'Silakan pilih mesin dulu.' });
        return;
      }

      var html = `
        <div style="max-height:350px;overflow:auto">
        <table class="table table-sm table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>SPK</th>
              <th>Pelanggan</th>
              <th>Item</th>
              <th class="text-end">Qty Diambil (baru)</th>
            </tr>
          </thead>
          <tbody>
      `;
      var totalItem = 0;
      checked.forEach(function (cb) {
        var sisaAmbil = parseInt(cb.dataset.sisaAmbil || '0', 10) || 0;
        if (sisaAmbil <= 0) return;
        html += `
          <tr>
            <td>${cb.dataset.nomorSpk || '-'}</td>
            <td>${cb.dataset.pelanggan || '-'}</td>
            <td>${cb.dataset.item || '-'}</td>
            <td class="text-end">${sisaAmbil.toLocaleString('id-ID')}</td>
          </tr>
        `;
        totalItem++;
      });
      html += `
          </tbody>
        </table>
        </div>
        <div class="mt-3 text-start"><strong>Total item dipilih: ${totalItem}</strong></div>
      `;

      if (totalItem === 0) {
        Swal.fire({ icon: 'info', title: 'Tidak ada qty yang bisa diambil', text: 'Semua item yang dipilih sudah tidak punya sisa ambil.' });
        return;
      }

      Swal.fire({
        title: 'Konfirmasi Multi Ambil (Semua Sisa)',
        html: html,
        icon: 'info',
        width: 800,
        showCancelButton: true,
        confirmButtonText: 'Ya, ambil semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#198754'
      }).then(function (result) {
        if (!result.isConfirmed) return;

        var form      = btnMulti.closest('.modalBulkAmbilForm');
        if (!form) return;
        var bulkInputs = form.querySelector('.modalBulkAmbilInputs');
        var mesinInput = form.querySelector('.modalBulkAmbilMesinId');
        if (!bulkInputs || !mesinInput) return;

        bulkInputs.innerHTML = '';
        document.querySelectorAll('.modal-ambil-item-checkbox:checked').forEach(function (cb) {
          var sisa = parseInt(cb.dataset.sisaAmbil || '0', 10) || 0;
          if (sisa <= 0) return;
          var inp = document.createElement('input');
          inp.type  = 'hidden';
          inp.name  = 'spk_item_ids[]';
          inp.value = cb.value;
          bulkInputs.appendChild(inp);
        });
        mesinInput.value = mesinId;

        Swal.fire({
          title: 'Memproses...',
          text: 'Sedang memproses multi ambil',
          allowOutsideClick: false,
          didOpen: function () { Swal.showLoading(); }
        });
        form.submit();
      });
    }

    // -------------------------------------------------------
    // 8. Pasang listener tab tipe mesin di dalam modal
    // -------------------------------------------------------
    document.querySelectorAll('#modalAmbilTabs [data-mesin-list]').forEach(function (btn) {
      var slug = btn.getAttribute('data-slug');

      btn.addEventListener('shown.bs.tab', function () {
        var list = [];
        try {
          var raw = btn.getAttribute('data-mesin-list');
          list = raw ? JSON.parse(raw) : [];
        } catch (e) {}

        modalClearAllMesinSubTabs();
        if (slug) modalRenderMesinSubTabsForSlug(slug, list);
      });

      // Active-class styling untuk tab-card di dalam modal
      btn.addEventListener('shown.bs.tab', function () {
        document.querySelectorAll('#modalAmbilTabs .tab-card').forEach(function (el) {
          el.classList.remove('active');
        });
        btn.classList.add('active');
      });
    });

    // -------------------------------------------------------
    // 9. Inisialisasi saat modal pertama kali dibuka
    // -------------------------------------------------------
    var modalAmbilPekerjaanEl = document.getElementById('modalAmbilPekerjaan');
    if (modalAmbilPekerjaanEl) {
      modalAmbilPekerjaanEl.addEventListener('shown.bs.modal', function () {
        var activeTabBtn = document.querySelector('#modalAmbilTabs .tab-card.active[data-mesin-list]');
        modalClearAllMesinSubTabs();
        if (activeTabBtn) {
          try {
            var tabInstance = bootstrap.Tab.getOrCreateInstance(activeTabBtn);
            tabInstance.show(); // trigger show agar konten tab bootstrap ikut sinkron
          } catch(e) {}
          
          var slug = activeTabBtn.getAttribute('data-slug');
          var list = [];
          try {
            var raw = activeTabBtn.getAttribute('data-mesin-list');
            list = raw ? JSON.parse(raw) : [];
          } catch (e) {}
          if (slug) modalRenderMesinSubTabsForSlug(slug, list);
        }
        modalSyncMultiAmbilButtons();
      });
    }

    // -------------------------------------------------------
    // 10. Delegasi event click di dalam modal
    // -------------------------------------------------------
    document.addEventListener('click', function (e) {

      // -- tombol Ambil (single) di dalam modal --
      var btnAmbilSingle = e.target.closest('#modalAmbilPekerjaan .btn-open-ambil-modal');
      if (btnAmbilSingle) {
        modalOpenAmbilSingle(btnAmbilSingle);
        return;
      }

      // -- tombol Multi Ambil di dalam modal --
      var btnMulti = e.target.closest('.btnModalMultiAmbil');
      if (btnMulti) {
        modalOpenAmbilMulti(btnMulti);
        return;
      }

      // -- checkbox modal-ambil-item-checkbox --
      if (e.target.classList && e.target.classList.contains('modal-ambil-item-checkbox')) {
        var cb         = e.target;
        var spkId      = cb.dataset.spkId;
        var accordionId = cb.dataset.modalAccordionId;

        // Sync cek-spk
        var spkItems   = document.querySelectorAll(`.modal-ambil-item-checkbox[data-spk-id="${spkId}"][data-modal-accordion-id="${accordionId}"]`);
        var spkChecked = document.querySelectorAll(`.modal-ambil-item-checkbox[data-spk-id="${spkId}"][data-modal-accordion-id="${accordionId}"]:checked`);
        var spkMaster  = document.querySelector(`.modal-cek-spk[data-spk-id="${spkId}"][data-modal-accordion-id="${accordionId}"]`);
        if (spkMaster) spkMaster.checked = spkItems.length === spkChecked.length;

        // Sync cek-all-accordion
        var accItems   = document.querySelectorAll(`.modal-ambil-item-checkbox[data-modal-accordion-id="${accordionId}"]`);
        var accChecked = document.querySelectorAll(`.modal-ambil-item-checkbox[data-modal-accordion-id="${accordionId}"]:checked`);
        var accMaster  = document.querySelector(`.modal-cek-all-accordion[data-modal-accordion-id="${accordionId}"]`);
        if (accMaster) accMaster.checked = accItems.length === accChecked.length;

        modalSyncMultiAmbilButtons();
        return;
      }

      // -- cek-spk di dalam modal --
      if (e.target.classList && e.target.classList.contains('modal-cek-spk')) {
        var spkCb      = e.target;
        var spkId2     = spkCb.dataset.spkId;
        var accordionId2 = spkCb.dataset.modalAccordionId;

        document.querySelectorAll(`.modal-ambil-item-checkbox[data-spk-id="${spkId2}"][data-modal-accordion-id="${accordionId2}"]`)
          .forEach(function (c) { c.checked = spkCb.checked; });

        var accItems2   = document.querySelectorAll(`.modal-ambil-item-checkbox[data-modal-accordion-id="${accordionId2}"]`);
        var accChecked2 = document.querySelectorAll(`.modal-ambil-item-checkbox[data-modal-accordion-id="${accordionId2}"]:checked`);
        var accMaster2  = document.querySelector(`.modal-cek-all-accordion[data-modal-accordion-id="${accordionId2}"]`);
        if (accMaster2) accMaster2.checked = accItems2.length === accChecked2.length;

        modalSyncMultiAmbilButtons();
        return;
      }

      // -- cek-all-accordion di dalam modal --
      if (e.target.classList && e.target.classList.contains('modal-cek-all-accordion')) {
        var allCb      = e.target;
        var accordionId3 = allCb.dataset.modalAccordionId;

        document.querySelectorAll(`.modal-ambil-item-checkbox[data-modal-accordion-id="${accordionId3}"]`)
          .forEach(function (c) { c.checked = allCb.checked; });

        document.querySelectorAll(`.modal-cek-spk[data-modal-accordion-id="${accordionId3}"]`)
          .forEach(function (s) { s.checked = allCb.checked; });

        modalSyncMultiAmbilButtons();
        return;
      }
    });

    // -------------------------------------------------------
    // 11. Sync tombol multi ambil jika checkbox berubah via change
    // -------------------------------------------------------
    document.addEventListener('change', function (e) {
      if (e.target.classList && e.target.classList.contains('modal-ambil-item-checkbox')) {
        modalSyncMultiAmbilButtons();
      }
    });

  })();
  </script>

<script>
  (function () {
    const ambilModalEl = document.getElementById('ambilModal');

    if (!ambilModalEl) return;

    const ambilModalInstance = bootstrap.Modal.getOrCreateInstance(ambilModalEl);

    ambilModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        ambilModalInstance.hide();
      });
    });

    ambilModalEl.addEventListener('click', function (e) {
      if (e.target === ambilModalEl) {
        ambilModalInstance.hide();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        if (ambilModalEl.classList.contains('show')) {
          ambilModalInstance.hide();
        }
      }
    });

    ambilModalEl.addEventListener('shown.bs.modal', function () {
      document.body.classList.add('modal-open');
    });

  })();
</script>

<script>
  (function () {
    function bindCardTabScope(containerSelector) {
      const container = document.querySelector(containerSelector);
      if (!container) return;

      container.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function () {
          container.querySelectorAll('.tab-card').forEach(el => el.classList.remove('active'));
          this.classList.add('active');
        });
      });
    }

    bindCardTabScope('#operatorTabs');
    bindCardTabScope('#pekerjaanSayaMesinTabs');
    bindCardTabScope('#poolTipeTabs');
    bindCardTabScope('#modalAmbilTabs');
  })();
</script>

@endpush
