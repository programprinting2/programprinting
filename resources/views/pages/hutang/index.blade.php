@extends('layout.master')

@push('plugin-styles')
  <style>
    .accordion-button:not(.collapsed) {
      background-color: #e7f3ff;
      color: #0c63e4;
    }
    .accordion-button:focus {
      box-shadow: none;
    }
    .accordion-item {
      border: 1px solid #dee2e6;
      border-radius: 0.375rem !important;
      margin-bottom: 1rem;
    }
    .accordion-item:not(:first-of-type) {
      border-top: 1px solid #dee2e6 !important; 
    }
    .accordion-header {
      border-radius: 0.375rem 0.375rem 0 0 !important;
    }
    .accordion-body {
      border-radius: 0 0 0.375rem 0.375rem !important;
    }
    .pemasok-header, .umur-header {
      transition: all 0.3s ease;
    }
    .pemasok-header:hover, .umur-header:hover {
      background-color: #f8f9fa;
    }
    .loading-spinner {
      display: none;
    }
    .accordion-body.loading .loading-spinner {
      display: block;
    }
    .nav-tabs .nav-link.active {
      border-bottom: 3px solid #0c63e4;
    }
    .umur-badge {
      font-size: 0.8em;
    }
    .umur-badge.bg-success { background-color: #198754 !important; }
    .umur-badge.bg-warning { background-color: #fd7e14 !important; }
    .umur-badge.bg-danger { background-color: #dc3545 !important; }
    .umur-badge.bg-dark { background-color: #6c757d !important; }
  </style>
@endpush

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Hutang</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="row">
              <h6 class="card-title mb-0">Kelola Hutang Pembelian</h6>
              <p class="text-muted mb-3">Pantau dan kelola hutang pembelian yang belum lunas</p>
            </div>
          </div>

          <!-- Bootstrap Tabs -->
          <ul class="nav nav-tabs" id="hutangTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $activeTab == 'pemasok' ? 'active' : '' }}" 
                      id="pemasok-tab" 
                      data-bs-toggle="tab" 
                      data-bs-target="#pemasok-content" 
                      type="button" 
                      role="tab" 
                      aria-controls="pemasok-content" 
                      aria-selected="{{ $activeTab == 'pemasok' ? 'true' : 'false' }}">
                <i data-feather="users" class="icon-sm me-2"></i>
                Per Pemasok
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $activeTab == 'umur' ? 'active' : '' }}" 
                      id="umur-tab" 
                      data-bs-toggle="tab" 
                      data-bs-target="#umur-content" 
                      type="button" 
                      role="tab" 
                      aria-controls="umur-content" 
                      aria-selected="{{ $activeTab == 'umur' ? 'true' : 'false' }}">
                <i data-feather="clock" class="icon-sm me-2"></i>
                Per Umur Hutang
              </button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content mt-4" id="hutangTabContent">
            
            <!-- Tab Per Pemasok -->
            <div class="tab-pane fade {{ $activeTab == 'pemasok' ? 'show active' : '' }}" 
                 id="pemasok-content" 
                 role="tabpanel" 
                 aria-labelledby="pemasok-tab">
              <div class="accordion" id="pemasokAccordion">
                @forelse($pemasokWithHutang as $index => $pemasok)
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="pemasok-heading{{ $pemasok['id'] }}">
                      <button class="accordion-button collapsed pemasok-header" 
                              type="button" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#pemasok-collapse{{ $pemasok['id'] }}" 
                              aria-expanded="false" 
                              aria-controls="pemasok-collapse{{ $pemasok['id'] }}"
                              data-pemasok-id="{{ $pemasok['id'] }}">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                          <div class="flex-grow-1">
                            <h6 class="mb-1 text-dark fw-bold">{{ $pemasok['nama'] }}</h6>
                            <small class="text-muted">{{ $pemasok['kode_pemasok'] }}</small>
                          </div>
                          <div class="text-end">
                            <div class="badge bg-warning text-dark mb-1">
                              {{ $pemasok['jumlah_transaksi'] }} transaksi
                            </div>
                            <div class="fw-bold text-danger fs-6">
                              Rp {{ number_format($pemasok['total_hutang'], 0, ',', '.') }}
                            </div>
                          </div>
                        </div>
                      </button>
                    </h2>
                    <div id="pemasok-collapse{{ $pemasok['id'] }}" 
                         class="accordion-collapse collapse" 
                         aria-labelledby="pemasok-heading{{ $pemasok['id'] }}" 
                         data-bs-parent="#pemasokAccordion">
                      <div class="accordion-body">
                        <div class="loading-spinner text-center py-4">
                          <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Memuat...</span>
                          </div>
                          <h6 class="text-muted">Memuat detail hutang...</h6>
                          <p class="text-muted small">Mohon tunggu sebentar</p>
                        </div>
                        <div class="pemasok-detail-content" data-loaded="false"></div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i data-feather="check-circle" class="icon-lg text-success mb-3"></i>
                    <h5 class="text-muted">Tidak ada hutang pembelian</h5>
                    <p class="text-muted">Semua pembelian telah lunas</p>
                  </div>
                @endforelse
              </div>
            </div>

            <!-- Tab Per Umur Hutang -->
            <div class="tab-pane fade {{ $activeTab == 'umur' ? 'show active' : '' }}" 
                 id="umur-content" 
                 role="tabpanel" 
                 aria-labelledby="umur-tab">
              <div class="accordion" id="umurAccordion">
                @forelse($umurHutangGroups as $groupKey => $group)
                  @php
                    $safeKey = str_replace('+', 'plus', $groupKey);
                  @endphp
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="umur-heading{{ $safeKey }}">
                      <button class="accordion-button collapsed umur-header" 
                              type="button" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#umur-collapse{{ $safeKey }}" 
                              aria-expanded="false" 
                              aria-controls="umur-collapse{{ $safeKey }}"
                              data-umur-group="{{ $safeKey }}">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                          <div class="flex-grow-1">
                            <h6 class="mb-1 text-dark fw-bold">
                              {{ $group['label'] }}
                              <span class="umur-badge badge ms-2 
                                @if($groupKey == '0-30') bg-success
                                @elseif($groupKey == '30-60') bg-warning  
                                @elseif($groupKey == '60-90') bg-danger
                                @else bg-dark @endif">
                                <i data-feather="clock" class="icon-xs me-1"></i>
                                {{ count($group['data']) }} transaksi
                              </span>
                            </h6>
                            <small class="text-muted">Hutang berdasarkan umur pembelian</small>
                          </div>
                          <div class="text-end">
                            <div class="fw-bold text-danger fs-6">
                              Rp {{ number_format($group['total'], 0, ',', '.') }}
                            </div>
                          </div>
                        </div>
                      </button>
                    </h2>
                    <div id="umur-collapse{{ $safeKey }}" 
                         class="accordion-collapse collapse" 
                         aria-labelledby="umur-heading{{ $groupKey }}" 
                         data-bs-parent="#umurAccordion">
                      <div class="accordion-body">
                        @if(count($group['data']) >= 0)
                        <div class="loading-spinner text-center py-4">
                          <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Memuat...</span>
                          </div>
                          <h6 class="text-muted">Memuat detail hutang...</h6>
                          <p class="text-muted small">Mohon tunggu sebentar</p>
                        </div>
                        <div class="umur-detail-content" data-loaded="false"></div>
                      @else
                        <div class="text-center py-4">
                          <i data-feather="check-circle" class="icon-lg text-success mb-2"></i>
                          <h6 class="text-muted">Tidak ada hutang dalam kategori ini</h6>
                          <p class="text-muted small">
                            Semua hutang telah lunas atau tidak ada di rentang {{ $group['label'] }}
                          </p>
                        </div>
                      @endif
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i data-feather="check-circle" class="icon-lg text-success mb-3"></i>
                    <h5 class="text-muted">Tidak ada hutang pembelian</h5>
                    <p class="text-muted">Semua pembelian telah lunas</p>
                  </div>
                @endforelse
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
  feather.replace();

  // CSRF token for AJAX
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Tab switching functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Handle tab changes
    const tabButtons = document.querySelectorAll('#hutangTabs .nav-link');
    tabButtons.forEach(button => {
      button.addEventListener('shown.bs.tab', function (event) {
        const targetTab = event.target.id;
        
        // Update URL without page reload
        const tabName = targetTab === 'pemasok-tab' ? 'pemasok' : 'umur';
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
      });
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle accordion events for both tabs
    ['pemasokAccordion', 'umurAccordion'].forEach(accordionId => {
      const accordion = document.getElementById(accordionId);
      if (accordion) {
        accordion.addEventListener('show.bs.collapse', function (event) {
          const target = event.target;
          const button = target.previousElementSibling.querySelector('.accordion-button');
          
          if (button.hasAttribute('data-pemasok-id')) {
            // Pemasok accordion
            const pemasokId = button.dataset.pemasokId;
            const contentDiv = target.querySelector('.pemasok-detail-content');
            if (contentDiv.dataset.loaded === 'false') {
              loadPemasokDetail(pemasokId, contentDiv);
            }
          } else if (button.hasAttribute('data-umur-group')) {
            // Umur accordion
            const umurGroup = button.dataset.umurGroup;
            const contentDiv = target.querySelector('.umur-detail-content');
            if (contentDiv.dataset.loaded === 'false') {
              loadUmurDetail(umurGroup, contentDiv);
            }
          }
        });
      }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.accordion-collapse.show').forEach(collapse => {
          const bsCollapse = new bootstrap.Collapse(collapse, {
            hide: true
          });
        });
      }
    });
  });

  // Load pemasok detail via AJAX
  function loadPemasokDetail(pemasokId, contentDiv) {
    const accordionBody = contentDiv.closest('.accordion-body');
    const loadingSpinner = accordionBody.querySelector('.loading-spinner');
    
    loadingSpinner.style.display = 'block';
    contentDiv.innerHTML = '';
    
    fetch(`/hutang?pemasok_id=${pemasokId}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      loadingSpinner.style.display = 'none';
      if (data.success) {
        contentDiv.innerHTML = renderPemasokDetail(data);
        contentDiv.dataset.loaded = 'true';
        feather.replace();
        initTooltipsForContent(contentDiv);
      } else {
        throw new Error(data.message);
      }
    })
    .catch(error => {
      console.error('Error loading pemasok detail:', error);
      loadingSpinner.style.display = 'none';
      contentDiv.innerHTML = `
        <div class="alert alert-danger">
          <i data-feather="alert-circle" class="icon-sm me-2"></i>
          Gagal memuat detail pembelian. Silakan coba lagi.
        </div>
      `;
      feather.replace();
    });
  }

  // Load umur hutang detail via AJAX
  function loadUmurDetail(umurGroup, contentDiv) {
    const accordionBody = contentDiv.closest('.accordion-body');
    const loadingSpinner = accordionBody.querySelector('.loading-spinner');
    
    loadingSpinner.style.display = 'block';
    contentDiv.innerHTML = '';
    
    fetch(`/hutang?umur_group=${umurGroup}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      loadingSpinner.style.display = 'none';
      if (data.success) {
        contentDiv.innerHTML = renderUmurDetail(data);
        contentDiv.dataset.loaded = 'true';
        feather.replace();
        initTooltipsForContent(contentDiv);
      } else {
        throw new Error(data.message);
      }
    })
    .catch(error => {
      console.error('Error loading umur detail:', error);
      loadingSpinner.style.display = 'none';
      contentDiv.innerHTML = `
        <div class="alert alert-danger">
          <i data-feather="alert-circle" class="icon-sm me-2"></i>
          Gagal memuat detail hutang. Silakan coba lagi.
        </div>
      `;
      feather.replace();
    });
  }

  // Render pemasok detail HTML
  function renderPemasokDetail(data) {
    if (!data.data || data.data.length === 0) {
      return `
        <div class="text-center py-4">
          <i data-feather="check-circle" class="icon-lg text-success mb-2"></i>
          <h6 class="text-muted">Tidak ada hutang pembelian</h6>
          <p class="text-muted small">Semua pembelian dari pemasok ini telah lunas</p>
        </div>
      `;
    }

    let tableHtml = `
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nomor Faktur</th>
              <th>Tanggal Faktur</th>
              <th>Jatuh Tempo</th>
              <th>Umur Hutang</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
    `;

    data.data.forEach(pembelian => {
      const umurHari = calculateUmurHutang(pembelian.tanggal_pembelian);
      tableHtml += `
        <tr>
          <td>
            <div class="fw-bold">${pembelian.nomor_form || '-'}</div>
            <small class="text-muted">${pembelian.kode_pembelian || '-'}</small>
          </td>
          <td>${formatDate(pembelian.tanggal_pembelian)}</td>
          <td>${pembelian.jatuh_tempo ? formatDate(pembelian.jatuh_tempo) : '<span class="text-muted">-</span>'}</td>
          <td>
            <span class="badge ${getUmurBadgeClass(umurHari)}">${umurHari} hari</span>
          </td>
          <td class="fw-bold text-danger">Rp ${formatNumber(pembelian.total)}</td>
          <td>
            <div class="btn-group" role="group">
              <a href="/pembelian/${pembelian.kode_pembelian}" class="btn btn-primary btn-sm" title="Lihat Detail">
                <i data-feather="eye" class="icon-xs"></i>
              </a>
              <a href="/pembelian/${pembelian.kode_pembelian}/edit" class="btn btn-warning btn-sm" title="Edit">
                <i data-feather="edit" class="icon-xs"></i>
              </a>
            </div>
          </td>
        </tr>
      `;
    });

    tableHtml += `
          </tbody>
        </table>
      </div>
      <div class="mt-3 p-3 bg-light rounded">
        <div class="row">
          <div class="col-md-6">
            <strong>Total Transaksi:</strong> ${data.summary.total_transaksi} pembelian
          </div>
          <div class="col-md-6 text-end">
            <strong>Total Hutang:</strong> 
            <span class="text-danger fw-bold fs-5">Rp ${formatNumber(data.summary.total_hutang)}</span>
          </div>
        </div>
      </div>
    `;

    return tableHtml;
  }

  // Render umur hutang detail HTML
  function renderUmurDetail(data) {
    if (!data.data || data.data.length === 0) {
      return `
        <div class="text-center py-4">
          <i data-feather="check-circle" class="icon-lg text-success mb-2"></i>
          <h6 class="text-muted">Tidak ada hutang dalam kategori ini</h6>
          <p class="text-muted small">Semua hutang telah lunas atau tidak ada dalam rentang ${data.summary.group_label}</p>
        </div>
      `;
    }

    let tableHtml = `
      <div class="mb-3">
        <h6 class="text-muted">
          <i data-feather="calendar" class="icon-sm me-2"></i>
          Hutang dengan umur: ${data.summary.group_label}
        </h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nomor Faktur</th>
              <th>Pemasok</th>
              <th>Tanggal Faktur</th>
              <th>Umur Hutang</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
    `;

    data.data.forEach(pembelian => {
      const umurHari = calculateUmurHutang(pembelian.tanggal_pembelian);
      tableHtml += `
        <tr>
          <td>
            <div class="fw-bold">${pembelian.nomor_form || '-'}</div>
            <small class="text-muted">${pembelian.kode_pembelian || '-'}</small>
          </td>
          <td>
            <div class="fw-bold">${pembelian.pemasok?.nama || '-'}</div>
            <small class="text-muted">${pembelian.pemasok?.kode_pemasok || ''}</small>
          </td>
          <td>${formatDate(pembelian.tanggal_pembelian)}</td>
          <td>
            <span class="badge ${getUmurBadgeClass(umurHari)}">${umurHari} hari</span>
          </td>
          <td class="fw-bold text-danger">Rp ${formatNumber(pembelian.total)}</td>
          <td>
            <div class="btn-group" role="group">
              <a href="/pembelian/${pembelian.kode_pembelian}" class="btn btn-primary btn-sm" title="Lihat Detail">
                <i data-feather="eye" class="icon-xs"></i>
              </a>
              <a href="/pembelian/${pembelian.kode_pembelian}/edit" class="btn btn-warning btn-sm" title="Edit">
                <i data-feather="edit" class="icon-xs"></i>
              </a>
            </div>
          </td>
        </tr>
      `;
    });

    tableHtml += `
          </tbody>
        </table>
      </div>
      <div class="mt-3 p-3 bg-light rounded">
        <div class="row">
          <div class="col-md-6">
            <strong>Total Transaksi:</strong> ${data.summary.total_transaksi} pembelian
          </div>
          <div class="col-md-6 text-end">
            <strong>Total Hutang:</strong> 
            <span class="text-danger fw-bold fs-5">Rp ${formatNumber(data.summary.total_hutang)}</span>
          </div>
        </div>
      </div>
    `;

    return tableHtml;
  }

  // Helper functions
  function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }

  function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
  }

  function getUmurBadgeClass(umurHari) {
    if (umurHari <= 30) {
      return 'bg-success';  
    } else if (umurHari <= 60) {
      return 'bg-warning';  
    } else if (umurHari <= 90) {
      return 'bg-danger';  
    } else {
      return 'bg-dark';    
    }
  }

  function calculateUmurHutang(tanggalPembelian) {
    const d = new Date(tanggalPembelian);
    const now = new Date();

    d.setHours(0, 0, 0, 0);
    now.setHours(0, 0, 0, 0);

    const msPerDay = 1000 * 60 * 60 * 24;
    return Math.round((now - d) / msPerDay);
  }

  function initTooltipsForContent(contentDiv) {
    const newTooltips = contentDiv.querySelectorAll('[title]');
    newTooltips.forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }
</script>
@endpush