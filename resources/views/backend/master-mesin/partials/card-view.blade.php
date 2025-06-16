<div id="cardView" class="row mesin-list">
    @forelse($mesin as $item)
    <div class="col-md-6 mb-4 mesin-item" 
         data-type="{{ $item->tipe_mesin }}" 
         data-status="{{ $item->status }}" 
         data-name="{{ $item->nama_mesin }}" 
         data-brand="{{ $item->merek }}" 
         data-model="{{ $item->model }}">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    @if($item->thumbnailUrl)
                        <img src="{{ $item->thumbnailUrl }}" alt="{{ $item->nama_mesin }}" 
                        class="img-thumbnail cursor-pointer" style="width: 50px; height: 50px; object-fit: cover;"
                        data-bs-toggle="modal" data-bs-target="#imageModal{{ $item->id }}">
                    @else
                        <div class="bg-light text-center" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i data-feather="image" style="width: 20px; height: 20px; color: #aaa;"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $item->nama_mesin }}</h5>
                        <span class="badge bg-primary">{{ $item->tipe_mesin }}</span>
                        <span class="badge bg-{{ 
                            $item->status == 'Aktif' ? 'success' : 
                            ($item->status == 'Maintenance' ? 'warning' : 
                             ($item->status == 'Rusak' ? 'danger' : 'secondary')) 
                        }}">
                            <i data-feather="{{ 
                                $item->status == 'Aktif' ? 'check-circle' : 
                                ($item->status == 'Maintenance' ? 'tool' : 
                                 ($item->status == 'Rusak' ? 'alert-triangle' : 'slash')) 
                            }}" class="icon-status me-1"></i>
                            {{ $item->status }}
                        </span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-mesin" 
                        data-id="{{ $item->id }}" 
                        data-nama="{{ $item->nama_mesin }}" 
                        title="Hapus">
                    <i data-feather="trash-2" class="icon-sm"></i>
                </button>
                <form id="formDeleteMesin{{ $item->id }}" 
                      action="{{ route('backend.master-mesin.destroy', $item->id) }}" 
                      method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <span class="text-muted">Merek:</span>
                            <span class="fw-medium">{{ $item->merek ?: '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Model:</span>
                            <span class="fw-medium">{{ $item->model ?: '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <span class="text-muted">Tanggal Beli:</span>
                            <span class="fw-medium">{{ $item->tanggal_pembelian ? $item->tanggal_pembelian->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Spesifikasi Teknis -->
                @if(is_array($item->detail_mesin) && count($item->detail_mesin) > 0)
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Spesifikasi:</h6>
                    <div class="row">
                        @foreach($item->detail_mesin as $index => $detail)
                            @if($index < 4) <!-- Batasi tampilan detail -->
                            <div class="col-md-6 mb-1">
                                <small class="text-muted">{{ $detail['nama'] ?? '' }}:</small>
                                <span class="ms-1">{{ $detail['nilai'] ?? '' }} {{ $detail['satuan'] ?? '' }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @if(count($item->detail_mesin) > 4)
                        <div class="mt-1">
                            <small><a href="#" data-bs-toggle="modal" data-bs-target="#detailMesinModal{{ $item->id }}">Lihat semua spesifikasi</a></small>
                        </div>
                    @endif
                </div>
                @endif
                
                <!-- Deskripsi Singkat -->
                @if($item->deskripsi)
                <div class="mt-3 border-top pt-2">
                    <small class="text-muted">{{ Str::limit($item->deskripsi, 100) }}</small>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-sm btn-outline-warning me-1">
                            <i data-feather="settings" class="icon-sm"></i> Service
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#detailMesinModal{{ $item->id }}">
                            <i data-feather="eye" class="icon-sm"></i> Detail
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editMesinModal{{ $item->id }}">
                            <i data-feather="edit" class="icon-sm"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-4">
        <p>Belum ada data mesin.</p>
    </div>
    @endforelse
</div> 