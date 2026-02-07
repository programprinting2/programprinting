@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-0">Master Mesin</h4>
                        <p class="text-muted">Kelola mesin produksi untuk keperluan perhitungan biaya dan penjadwalan.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#tambahMesinModal">
                            <i data-feather="plus" class="btn-icon-prepend"></i>
                            Tambah Mesin
                        </button>
                    </div>
                </div>

                <!-- Filter Panel (moved to top) -->
                <div class="mb-4">
                        @include('backend.master-mesin.partials.filter-panel')
                    </div>

                <!-- Content Panel (now full width) -->
                <div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-column">
                                        <h5 class="mb-0">Daftar Mesin</h5>
                                        <p class="text-muted mb-0">Menampilkan {{ $mesin->total() }} mesin.</p>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary active" id="cardViewBtn">
                                            <i data-feather="grid" class="icon-sm"></i> Card
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="tableViewBtn">
                                            <i data-feather="list" class="icon-sm"></i> Tabel
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Card View -->
                                @include('backend.master-mesin.partials.card-view')
                                
                                <!-- Table View -->
                                @include('backend.master-mesin.partials.table-view')

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $mesin->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Mesin -->
@include('backend.master-mesin.modals.tambah-mesin')

<!-- Modal Detail & Edit Mesin -->
@include('backend.master-mesin.modals.detail-mesin')
@include('backend.master-mesin.modals.edit-mesin')

<!-- Modal Image Preview -->
@foreach($mesin as $item)
    @if($item->cloudinary_public_id)
    <div class="modal fade" id="imageModal{{ $item->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel{{ $item->id }}">{{ $item->nama_mesin }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <img src="{{ $item->mediumUrl }}" alt="{{ $item->nama_mesin }}" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
@endpush

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/master-mesin/style.css') }}">
@endpush

@push('custom-scripts')
    <script src="{{ asset('js/master-mesin/master-mesin.js') }}"></script>
    <script src="{{ asset('js/master-mesin/filter.js') }}"></script>
    <script src="{{ asset('js/master-mesin/form-handler.js') }}"></script>
    <script src="{{ asset('js/master-mesin/detail-handler.js') }}"></script>
    <script src="{{ asset('js/master-mesin/production-cost-handler.js') }}"></script>
@endpush 