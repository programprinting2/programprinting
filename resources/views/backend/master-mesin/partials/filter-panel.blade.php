<div class="filter-panel mb-4">
    <form method="GET" action="{{ url()->current() }}">
        <div class="row g-3">
            <!-- Search Bar -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i data-feather="search" class="icon-sm"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" name="search" placeholder="Cari mesin..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Filter Tipe Mesin -->
            <div class="col-md-3">
                <select class="form-select filter-type" id="filterTipeMesin" name="type">
                    <option value="semua" {{ request('type') == 'semua' ? 'selected' : '' }}>Semua Tipe Mesin</option>
                    @foreach($tipe_mesin as $tipe)
                        <option value="{{ $tipe->nama_detail_parameter }}" {{ request('type') == $tipe->nama_detail_parameter ? 'selected' : '' }}>
                            {{ $tipe->nama_detail_parameter }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-md-3">
                <select class="form-select" id="filterStatus" name="status">
                    <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="col-md-2">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i data-feather="filter" class="icon-sm me-1"></i> Cari
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                        <i data-feather="refresh-cw" class="icon-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi Feather Icons
    feather.replace();
});
</script>
@endpush