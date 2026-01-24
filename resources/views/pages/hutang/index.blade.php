@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
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
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="row">
              <h6 class="card-title mb-0">Data Hutang Pembelian</h6>
              <p class="text-muted mb-3">Kelola hutang pembelian yang belum lunas</p>
            </div>
          </div>

          <div class="row">
            <!-- Sidebar Kiri - List Pemasok -->
            <div class="col-md-4">
              <div class="card border">
                <div class="card-header bg-light">
                  <h6 class="card-title mb-0">
                    <i data-feather="users" class="icon-sm me-2"></i>
                    Daftar Pemasok 
                  </h6>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                  @forelse($pemasokWithHutang as $pemasok)
                    <div class="p-3 border-bottom {{ $selectedPemasokId == $pemasok['id'] ? 'bg-primary bg-opacity-10' : 'hover-bg-light' }} 
                         {{ !$loop->last ? 'border-bottom' : '' }}">
                      <a href="{{ route('hutang.index', ['pemasok_id' => $pemasok['id']]) }}" 
                         class="text-decoration-none d-block">
                        <div class="d-flex justify-content-between align-items-start">
                          <div class="flex-grow-1">
                            <h6 class="mb-1 text-dark">{{ $pemasok['nama'] }}</h6>
                            <small class="text-muted">{{ $pemasok['kode_pemasok'] }}</small>
                          </div>
                          <div class="text-end">
                            <div class="badge bg-warning text-dark mb-1">
                              {{ $pemasok['jumlah_transaksi'] }} transaksi
                            </div>
                            <div class="fw-bold text-danger">
                              Rp {{ number_format($pemasok['total_hutang'], 0, ',', '.') }}
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                  @empty
                    <div class="p-4 text-center text-muted">
                      <i data-feather="check-circle" class="icon-lg mb-2"></i>
                      <p class="mb-0">Tidak ada hutang pembelian</p>
                    </div>
                  @endforelse
                </div>
              </div>
            </div>

            <!-- Konten Kanan - Tabel Pembelian -->
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                      @if($selectedPemasokId)
                        Detail Hutang: {{ $pemasokWithHutang->where('id', $selectedPemasokId)->first()['nama'] ?? 'Pemasok' }}
                      @else
                        <i data-feather="file-text" class="icon-sm me-2"></i>
                        Pilih Pemasok untuk Melihat Detail
                      @endif
                    </h6>
                    @if($selectedPemasokId)
                      <a href="{{ route('hutang.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="x" class="icon-sm me-1"></i>
                        Tutup Detail
                      </a>
                    @endif
                  </div>
                </div>
                <div class="card-body">
                  @if($selectedPemasokId)
                    @if($pembelianData->count() > 0)
                      <div class="table-responsive">
                        <table class="table table-hover align-middle">
                          <thead class="table-light">
                            <tr>
                              <th>Kode Pembelian</th>
                              <th>Tanggal</th>
                              <th>Jatuh Tempo</th>
                              <th>Total</th>
                              <th>Status</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($pembelianData as $pembelian)
                              <tr>
                                <td>
                                  <div class="fw-bold">{{ $pembelian->kode_pembelian }}</div>
                                  <small class="text-muted">{{ $pembelian->nomor_form }}</small>
                                </td>
                                <td>
                                  {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->locale('id')->translatedFormat('d/m/Y') }}
                                </td>
                                <td>
                                  @if($pembelian->jatuh_tempo)
                                    {{ \Carbon\Carbon::parse($pembelian->jatuh_tempo)->locale('id')->translatedFormat('d/m/Y') }}
                                  @else
                                    <span class="text-muted">-</span>
                                  @endif
                                </td>
                                <td class="fw-bold text-danger">
                                  Rp {{ number_format($pembelian->total, 0, ',', '.') }}
                                </td>
                                <td>
                                  <span class="badge bg-danger">
                                    <i data-feather="clock" class="icon-xs me-1"></i>
                                    Belum Lunas
                                  </span>
                                </td>
                                <td>
                                  <div class="btn-group" role="group">
                                    <a href="{{ route('pembelian.show', $pembelian->kode_pembelian) }}" 
                                       class="btn btn-primary btn-sm" 
                                       title="Lihat Detail">
                                      <i data-feather="eye" class="icon-xs"></i>
                                    </a>
                                    <a href="{{ route('pembelian.edit', $pembelian->kode_pembelian) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Edit">
                                      <i data-feather="edit" class="icon-xs"></i>
                                    </a>
                                  </div>
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>

                      <!-- Pagination -->
                      <div class="d-flex justify-content-center mt-4">
                        {{ $pembelianData->links() }}
                      </div>
                    @else
                      <div class="text-center py-5">
                        <i data-feather="check-circle" class="icon-lg text-success mb-3"></i>
                        <h5 class="text-muted">Tidak ada hutang pembelian</h5>
                        <p class="text-muted">Semua pembelian dari pemasok ini telah lunas</p>
                      </div>
                    @endif
                  @else
                    <div class="text-center py-5">
                      <i data-feather="arrow-left" class="icon-lg text-muted mb-3"></i>
                      <h5 class="text-muted">Pilih Pemasok</h5>
                      <p class="text-muted">Klik pada salah satu pemasok untuk melihat detail hutang</p>
                    </div>
                  @endif
                </div>
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
  // Initialize feather icons
  feather.replace();

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href*="pemasok_id"]').forEach(link => {
      link.addEventListener('click', function(e) {
        const pemasokCard = this.closest('.p-3');
        
        // Remove active state from all pemasok cards
        document.querySelectorAll('.p-3').forEach(card => {
          card.classList.remove('bg-primary', 'bg-opacity-10');
          card.classList.add('hover-bg-light');
        });
        
        // Add active state to clicked pemasok
        pemasokCard.classList.remove('hover-bg-light');
        pemasokCard.classList.add('bg-primary', 'bg-opacity-10');
        
        // Show loading state in right panel
        showLoadingState();
        
        // Add loading class to clicked card
        pemasokCard.classList.add('loading');
        
        setTimeout(() => {
          pemasokCard.classList.remove('loading');
        }, 500);
      });
    });
    
    // Handle close detail button
    const closeDetailBtn = document.querySelector('a[href*="hutang"]');
    if (closeDetailBtn && closeDetailBtn.textContent.includes('Tutup Detail')) {
      closeDetailBtn.addEventListener('click', function(e) {
        // Remove active state from all pemasok cards
        document.querySelectorAll('.p-3').forEach(card => {
          card.classList.remove('bg-primary', 'bg-opacity-10');
          card.classList.add('hover-bg-light');
        });
      });
    }
    
    // Initialize tooltips if needed
    initTooltips();
  });

  // Function to show loading 
  function showLoadingState() {
    const rightPanel = document.querySelector('.col-md-8 .card-body');
    if (rightPanel) {
      // Save original content
      if (!rightPanel.dataset.originalContent) {
        rightPanel.dataset.originalContent = rightPanel.innerHTML;
      }
      
      // Show loading spinner
      rightPanel.innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Memuat data...</span>
          </div>
          <h6 class="text-muted">Memuat detail hutang...</h6>
          <p class="text-muted small">Mohon tunggu sebentar</p>
        </div>
      `;
    }
  }

  // Function to initialize tooltips
  function initTooltips() {
    // Initialize Bootstrap tooltips for buttons
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  // Add smooth scrolling for better UX
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Add hover effects
  document.querySelectorAll('.p-3.hover-bg-light').forEach(card => {
    card.addEventListener('mouseenter', function() {
      if (!this.classList.contains('bg-primary')) {
        this.style.backgroundColor = '#f8f9fa';
      }
    });
    
    card.addEventListener('mouseleave', function() {
      if (!this.classList.contains('bg-primary')) {
        this.style.backgroundColor = '';
      }
    });
  });
</script>
@endpush