@extends('layout.master')

@push('plugin-styles')
  <style>
    .pembukuan-card {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      transition: all 0.2s ease;
      height: 100%;
      text-decoration: none;
      display: block;
      color: inherit;
    }

    .pembukuan-card:hover {
      border-color: #cfd4dc;
      box-shadow: 0 8px 20px rgba(31, 41, 55, 0.08);
      transform: translateY(-2px);
      color: inherit;
    }

    .menu-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 14px;
    }

    .menu-icon svg {
      width: 24px;
      height: 24px;
    }

    .menu-icon-primary { background: rgba(13, 110, 253, 0.15); color: #0d6efd; }
    .menu-icon-success { background: rgba(25, 135, 84, 0.15); color: #198754; }
    .menu-icon-warning { background: rgba(255, 193, 7, 0.2); color: #946200; }
    .menu-icon-info { background: rgba(13, 202, 240, 0.18); color: #0c7f99; }
    .menu-icon-secondary { background: rgba(108, 117, 125, 0.18); color: #495057; }
    .menu-icon-dark { background: rgba(33, 37, 41, 0.12); color: #212529; }

    .menu-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .menu-description {
      font-size: .85rem;
      color: #6c757d;
      line-height: 1.4;
      margin-bottom: 0;
    }
  </style>
@endpush

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">Pembukuan</li>
      <li class="breadcrumb-item active" aria-current="page">Buku Besar</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
            <div>
              <h6 class="card-title mb-1">Buku Besar</h6>
              <p class="text-muted mb-0">Prototype modul pembukuan (dummy display, belum terhubung database)</p>
            </div>
            <span class="badge bg-light text-dark border">Mode Review</span>
          </div>

          <div class="row g-3">
            @foreach($menus as $menu)
              <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ $menu['route'] }}" class="pembukuan-card">
                  <div class="card-body p-4">
                    <div class="menu-icon menu-icon-{{ $menu['color'] }}">
                      <i data-feather="{{ $menu['icon'] }}"></i>
                    </div>
                    <div class="menu-title">{{ $menu['title'] }}</div>
                    <p class="menu-description">{{ $menu['description'] }}</p>
                  </div>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('custom-scripts')
<script>
  feather.replace();
</script>
@endpush
