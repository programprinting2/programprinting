<div class="filter-panel mb-4">
    <div class="row g-3">
        <!-- Search Bar -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i data-feather="search" class="icon-sm"></i>
                </span>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari mesin...">
            </div>
        </div>

        <!-- Filter Tipe Mesin -->
        <div class="col-md-3">
            <select class="form-select filter-type" id="filterTipeMesin">
                <option value="semua">Semua Tipe Mesin</option>
                @foreach($tipe_mesin as $tipe)
                    <option value="{{ $tipe->nama_detail_parameter }}">{{ $tipe->nama_detail_parameter }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status -->
        <div class="col-md-3">
            <select class="form-select" id="filterStatus">
                <option value="semua">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Maintenance">Maintenance</option>
                <option value="Rusak">Rusak</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
        </div>

        <!-- Tombol Reset -->
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100" id="resetFilters">
                <i data-feather="refresh-cw" class="icon-sm me-1"></i> Reset
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi Feather Icons
    feather.replace();
});
</script>
@endpush 