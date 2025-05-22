@foreach($mesin as $item)
<div class="modal fade" id="detailMesinModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailMesinModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="detailMesinModalLabel{{ $item->id }}">
                    <i data-feather="info" class="me-2"></i>
                    Detail Mesin: {{ $item->nama_mesin }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Informasi Utama -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i data-feather="info" class="me-2"></i>Informasi Utama</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="text-muted small">Tipe Mesin</label>
                                            <div class="fw-medium">{{ $item->tipe_mesin }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small">Merek</label>
                                            <div class="fw-medium">{{ $item->merek ?: '-' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small">Model</label>
                                            <div class="fw-medium">{{ $item->model ?: '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="text-muted small">Nomor Seri</label>
                                            <div class="fw-medium">{{ $item->nomor_seri ?: '-' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small">Status</label>
                                            <div>
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
                                        <div class="mb-3">
                                            <label class="text-muted small">Lebar Media Maksimum</label>
                                            <div class="fw-medium">{{ $item->lebar_media_maksimum ? $item->lebar_media_maksimum . ' cm' : '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Spesifikasi Teknis -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i data-feather="settings" class="me-2"></i>Spesifikasi Teknis</h6>
                            </div>
                            <div class="card-body">
                                @if(is_array($item->detail_mesin) && count($item->detail_mesin) > 0)
                                    <div class="row">
                                        @foreach($item->detail_mesin as $detail)
                                            <div class="col-md-6 mb-3">
                                                <label class="text-muted small">{{ $detail['nama'] ?? '' }}</label>
                                                <div class="fw-medium">
                                                    {{ $detail['nilai'] ?? '' }} {{ $detail['satuan'] ?? '' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-3">
                                        <i data-feather="info" class="mb-2"></i>
                                        <p class="mb-0">Tidak ada spesifikasi teknis yang tersedia</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Biaya Produksi -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i data-feather="dollar-sign" class="me-2"></i>Biaya Produksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">Harga Tinta per Liter</label>
                                        <div class="fw-medium">{{ $item->harga_tinta_per_liter ? 'Rp ' . number_format($item->harga_tinta_per_liter, 0, ',', '.') : '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">Konsumsi Tinta per m²</label>
                                        <div class="fw-medium">{{ $item->konsumsi_tinta_per_m2 ? $item->konsumsi_tinta_per_m2 . ' mL' : '-' }}</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="text-muted small">Biaya Tambahan</label>
                                    @if(is_array($item->biaya_tambahan) && count($item->biaya_tambahan) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Biaya</th>
                                                        <th class="text-end">Nilai (Rp/m²)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($item->biaya_tambahan as $biaya)
                                                        <tr>
                                                            <td>{{ $biaya['nama'] }}</td>
                                                            <td class="text-end">Rp {{ number_format($biaya['nilai'], 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="fw-medium">-</div>
                                    @endif
                                </div>
                                
                                <div class="alert alert-light text-dark">
                                    <div class="mb-1">
                                        <label class="text-muted small mb-1">Biaya Tinta per m²</label>
                                        <div class="fw-bold">{{ $item->biaya_tinta_per_m2 ? 'Rp ' . number_format($item->biaya_tinta_per_m2, 2, ',', '.') : 'Rp 0' }}</div>
                                    </div>
                                    <div class="mb-1">
                                        <label class="text-muted small mb-1">Biaya Tambahan per m²</label>
                                        <div class="fw-bold">Rp {{ number_format($item->biaya_tambahan_per_m2, 2, ',', '.') }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="mb-1">
                                        <label class="text-muted small mb-1">Total Biaya per m²</label>
                                        <div class="fw-bold">Rp {{ number_format($item->total_biaya_per_m2, 2, ',', '.') }}</div>
                                    </div>
                                    <div>
                                        <label class="text-muted small mb-1">Total Biaya per cm²</label>
                                        <div class="fw-bold">Rp {{ number_format($item->total_biaya_per_cm2, 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="col-md-4">
                        @if($item->gambar)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i data-feather="image" class="me-2"></i>Gambar Mesin</h6>
                            </div>
                            <div class="card-body p-0">
                                <img src="{{ asset('storage/images/mesin/' . $item->gambar) }}" class="img-fluid" alt="{{ $item->nama_mesin }}">
                            </div>
                        </div>
                        @endif
                        <!-- Informasi Pembelian -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i data-feather="shopping-cart" class="me-2"></i>Informasi Pembelian</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="text-muted small">Tanggal Pembelian</label>
                                    <div class="fw-medium">{{ $item->tanggal_pembelian ? $item->tanggal_pembelian->format('d M Y') : '-' }}</div>
                                </div>
                                <div class="mb-0">
                                    <label class="text-muted small">Harga Pembelian</label>
                                    <div class="fw-medium">{{ $item->formatted_price }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi & Catatan -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i data-feather="file-text" class="me-2"></i>Deskripsi & Catatan</h6>
                            </div>
                            <div class="card-body">
                                @if($item->deskripsi)
                                    <div class="mb-3">
                                        <label class="text-muted small">Deskripsi</label>
                                        <div class="fw-medium">{{ $item->deskripsi }}</div>
                                    </div>
                                @endif
                                @if($item->catatan_tambahan)
                                    <div class="mb-0">
                                        <label class="text-muted small">Catatan Tambahan</label>
                                        <div class="fw-medium">{{ $item->catatan_tambahan }}</div>
                                    </div>
                                @endif
                                @if(!$item->deskripsi && !$item->catatan_tambahan)
                                    <div class="text-center text-muted py-3">
                                        <i data-feather="info" class="mb-2"></i>
                                        <p class="mb-0">Tidak ada deskripsi atau catatan</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editMesinModal{{ $item->id }}" data-bs-dismiss="modal">
                    <i data-feather="edit-2" class="me-1"></i> Edit Mesin
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach 