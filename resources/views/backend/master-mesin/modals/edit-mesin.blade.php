@foreach($mesin as $item)
<div class="modal fade" id="editMesinModal{{ $item->id }}" tabindex="-1" aria-labelledby="editMesinModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMesinModalLabel{{ $item->id }}">Edit Mesin: {{ $item->nama_mesin }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditMesin{{ $item->id }}" action="{{ route('backend.master-mesin.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="detail_mesin_json" id="edit_detail_mesin_json{{ $item->id }}" value="{{ json_encode($item->detail_mesin) }}">
                    <input type="hidden" name="biaya_tambahan_json" id="edit_biaya_tambahan_json{{ $item->id }}" value="{{ json_encode($item->biaya_tambahan) }}">
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="editMesinTabs{{ $item->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-identitas-tab{{ $item->id }}" data-bs-toggle="tab" data-bs-target="#edit-identitas{{ $item->id }}" type="button" role="tab" aria-controls="edit-identitas{{ $item->id }}" aria-selected="true">
                                <i data-feather="info" class="me-1 icon-sm"></i> Identitas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-spesifikasi-tab{{ $item->id }}" data-bs-toggle="tab" data-bs-target="#edit-spesifikasi{{ $item->id }}" type="button" role="tab" aria-controls="edit-spesifikasi{{ $item->id }}" aria-selected="false">
                                <i data-feather="settings" class="me-1 icon-sm"></i> Spesifikasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-biaya-produksi-tab{{ $item->id }}" data-bs-toggle="tab" data-bs-target="#edit-biaya-produksi{{ $item->id }}" type="button" role="tab" aria-controls="edit-biaya-produksi{{ $item->id }}" aria-selected="false">
                                <i data-feather="dollar-sign" class="me-1 icon-sm"></i> Biaya Produksi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-deskripsi-tab{{ $item->id }}" data-bs-toggle="tab" data-bs-target="#edit-deskripsi{{ $item->id }}" type="button" role="tab" aria-controls="edit-deskripsi{{ $item->id }}" aria-selected="false">
                                <i data-feather="file-text" class="me-1 icon-sm"></i> Deskripsi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-gambar-tab{{ $item->id }}" data-bs-toggle="tab" data-bs-target="#edit-gambar{{ $item->id }}" type="button" role="tab" aria-controls="edit-gambar{{ $item->id }}" aria-selected="false">
                                <i data-feather="image" class="me-1 icon-sm"></i> Gambar
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="editMesinTabContent{{ $item->id }}">
                        <!-- Tab Identitas -->
                        <div class="tab-pane fade show active" id="edit-identitas{{ $item->id }}" role="tabpanel" aria-labelledby="edit-identitas-tab{{ $item->id }}">
                            <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                <i data-feather="info" class="me-2"></i>
                                <div>
                                    <strong>Petunjuk:</strong>
                                    <ul class="mb-0">
                                        <li>Nama Mesin dan Tipe Mesin adalah field wajib diisi</li>
                                        <li>Status mesin akan mempengaruhi ketersediaan mesin untuk produksi</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_nama_mesin{{ $item->id }}" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nama_mesin{{ $item->id }}" name="nama_mesin" value="{{ $item->nama_mesin }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_tipe_mesin{{ $item->id }}" class="form-label">Tipe Mesin <span class="text-danger">*</span></label>
                                    <select class="form-select edit-tipe-mesin" id="edit_tipe_mesin{{ $item->id }}" name="tipe_mesin" data-id="{{ $item->id }}" required>
                                        <option value="Printer Large Format" {{ $item->tipe_mesin == 'Printer Large Format' ? 'selected' : '' }}>Printer Large Format</option>
                                        <option value="Digital Printer A3+" {{ $item->tipe_mesin == 'Digital Printer A3+' ? 'selected' : '' }}>Digital Printer A3+</option>
                                        <option value="Mesin Finishing" {{ $item->tipe_mesin == 'Mesin Finishing' ? 'selected' : '' }}>Mesin Finishing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_merek{{ $item->id }}" class="form-label">Merek</label>
                                    <input type="text" class="form-control" id="edit_merek{{ $item->id }}" name="merek" value="{{ $item->merek }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_model{{ $item->id }}" class="form-label">Model</label>
                                    <input type="text" class="form-control" id="edit_model{{ $item->id }}" name="model" value="{{ $item->model }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_nomor_seri{{ $item->id }}" class="form-label">Nomor Seri</label>
                                    <input type="text" class="form-control" id="edit_nomor_seri{{ $item->id }}" name="nomor_seri" value="{{ $item->nomor_seri }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_status{{ $item->id }}" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_status{{ $item->id }}" name="status" required>
                                        <option value="Aktif" {{ $item->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Maintenance" {{ $item->status == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="Rusak" {{ $item->status == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                        <option value="Tidak Aktif" {{ $item->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_tanggal_pembelian{{ $item->id }}" class="form-label">Tanggal Pembelian</label>
                                    <input type="date" class="form-control" id="edit_tanggal_pembelian{{ $item->id }}" name="tanggal_pembelian" value="{{ $item->tanggal_pembelian ? $item->tanggal_pembelian->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_harga_pembelian{{ $item->id }}" class="form-label">Harga Pembelian</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="edit_harga_pembelian{{ $item->id }}" name="harga_pembelian" value="{{ $item->harga_pembelian }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Spesifikasi -->
                        <div class="tab-pane fade" id="edit-spesifikasi{{ $item->id }}" role="tabpanel" aria-labelledby="edit-spesifikasi-tab{{ $item->id }}">
                            <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                <i data-feather="info" class="me-2"></i>
                                <div>
                                    <strong>Petunjuk:</strong>
                                    <ul class="mb-0">
                                        <li>Lebar media maksimum digunakan untuk validasi ukuran cetak</li>
                                        <li>Spesifikasi tambahan bisa berupa resolusi, kecepatan cetak, dll</li>
                                        <li>Pastikan satuan yang digunakan konsisten</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mb-3" id="edit_printer_specs{{ $item->id }}">
                                @if($item->tipe_mesin == 'Printer Large Format' || $item->tipe_mesin == 'Digital Printer A3+')
                                <div class="col-md-6 mb-3">
                                    <label for="edit_lebar_media_maksimum{{ $item->id }}" class="form-label">Lebar Media Maksimum (cm)</label>
                                    <input type="number" step="0.1" class="form-control" id="edit_lebar_media_maksimum{{ $item->id }}" name="lebar_media_maksimum" value="{{ $item->lebar_media_maksimum }}" placeholder="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_resolusi{{ $item->id }}" class="form-label">Resolusi Default</label>
                                    <input type="text" class="form-control" id="edit_resolusi{{ $item->id }}" name="resolusi" value="{{ $item->detail_mesin ? collect($item->detail_mesin)->firstWhere('nama', 'Resolusi')['nilai'] ?? '' : '' }}" placeholder="contoh: 1440 x 1440 dpi">
                                </div>
                                @elseif($item->tipe_mesin == 'Mesin Finishing')
                                <div class="col-md-6 mb-3">
                                    <label for="edit_dimensi{{ $item->id }}" class="form-label">Dimensi Mesin (cm)</label>
                                    <input type="text" class="form-control" id="edit_dimensi{{ $item->id }}" name="dimensi" value="{{ $item->detail_mesin ? collect($item->detail_mesin)->firstWhere('nama', 'Dimensi')['nilai'] ?? '' : '' }}" placeholder="P x L x T">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_daya_listrik{{ $item->id }}" class="form-label">Daya Listrik</label>
                                    <input type="text" class="form-control" id="edit_daya_listrik{{ $item->id }}" name="daya_listrik" value="{{ $item->detail_mesin ? collect($item->detail_mesin)->firstWhere('nama', 'Daya Listrik')['nilai'] ?? '' : '' }}" placeholder="contoh: 220V/380V">
                                </div>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Spesifikasi Tambahan</label>
                                <div id="edit_detail_mesin_container{{ $item->id }}">
                                    @if(is_array($item->detail_mesin) && count($item->detail_mesin) > 0)
                                        @foreach($item->detail_mesin as $detail)
                                            @if(!in_array($detail['nama'], ['Resolusi', 'Dimensi', 'Daya Listrik']))
                                            <div class="row mb-2 detail-item">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="detail_nama[]" value="{{ $detail['nama'] ?? '' }}" placeholder="Nama Spesifikasi">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="detail_nilai[]" value="{{ $detail['nilai'] ?? '' }}" placeholder="Nilai">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="detail_satuan[]" value="{{ $detail['satuan'] ?? '' }}" placeholder="Satuan">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-detail">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2 tambah-detail-edit" data-id="{{ $item->id }}">
                                    <i data-feather="plus"></i> Tambah Spesifikasi
                                </button>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Biaya Produksi -->
                        <div class="tab-pane fade edit-biaya-produksi-tab" id="edit-biaya-produksi{{ $item->id }}" role="tabpanel" aria-labelledby="edit-biaya-produksi-tab{{ $item->id }}" data-mesin-id="{{ $item->id }}">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Biaya Tinta</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Harga tinta per liter digunakan untuk menghitung biaya tinta per m²</li>
                                                <li>Konsumsi tinta per m² dalam milliliter (mL)</li>
                                                <li>Biaya tambahan bisa berupa biaya operator, listrik, maintenance, dll</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Harga Tinta per Liter (Rp)</label>
                                            <input type="number" class="form-control" name="harga_tinta_per_liter" id="harga_tinta_per_liter{{ $item->id }}" placeholder="0" min="0" value="{{ $item->harga_tinta_per_liter }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Konsumsi Tinta per m² (mL)</label>
                                            <input type="number" class="form-control" name="konsumsi_tinta_per_m2" id="konsumsi_tinta_per_m2{{ $item->id }}" placeholder="0" min="0" step="0.01" value="{{ $item->konsumsi_tinta_per_m2 }}">
                                            <small class="text-muted mt-1 d-block">
                                                Jumlah tinta dalam milliliter (mL) yang digunakan untuk mencetak 1 m²
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Biaya Tambahan</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary tambah-biaya-edit" data-id="{{ $item->id }}">
                                        <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="edit_biaya_tambahan_container{{ $item->id }}">
                                        @if(is_array($item->biaya_tambahan) && count($item->biaya_tambahan) > 0)
                                            @foreach($item->biaya_tambahan as $biaya)
                                                <div class="card mb-2 biaya-item" id="biaya_row_edit_{{ $loop->index }}_{{ $item->id }}" data-mesin-id="{{ $item->id }}">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-5 mb-2">
                                                                <label class="form-label">Nama Biaya</label>
                                                                <input type="text" class="form-control biaya-nama" placeholder="Contoh: Biaya Operator" value="{{ $biaya['nama'] ?? '' }}">
                                                            </div>
                                                            <div class="col-md-5 mb-2">
                                                                <label class="form-label">Nilai (Rp/m²)</label>
                                                                <input type="number" class="form-control biaya-nilai" placeholder="0" min="0" step="0.01" value="{{ $biaya['nilai'] ?? 0 }}">
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-end mb-2">
                                                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-biaya" data-row="biaya_row_edit_{{ $loop->index }}_{{ $item->id }}">
                                                                    <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="alert alert-light mt-2 mb-0">
                                                <div class="d-flex text-dark justify-content-between align-items-center">
                                                    <span class="fw-bold">Total Biaya Tambahan:</span>
                                                    <span class="fw-bold" id="total_biaya_tambahan{{ $item->id }}">Rp {{ number_format($item->biaya_tambahan_per_m2, 2, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted mb-2" id="no_biaya_message">
                                                Belum ada biaya tambahan. Klik tombol "Tambah Biaya" untuk menambahkan.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Total Biaya Produksi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Biaya Tinta per m²:</th>
                                                <td class="text-end"><span id="biaya_tinta_per_m2{{ $item->id }}">Rp {{ number_format($item->biaya_tinta_per_m2 ?? 0, 2, ',', '.') }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Biaya Tambahan per m²:</th>
                                                <td class="text-end"><span id="biaya_tambahan_per_m2{{ $item->id }}">Rp {{ number_format($item->biaya_tambahan_per_m2 ?? 0, 2, ',', '.') }}</span></td>
                                            </tr>
                                            <tr class="table-active fw-bold">
                                                <th>Total Biaya per m²:</th>
                                                <td class="text-end"><span id="total_biaya_per_m2{{ $item->id }}">Rp {{ number_format($item->total_biaya_per_m2 ?? 0, 2, ',', '.') }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Total Biaya per cm²:</th>
                                                <td class="text-end"><span id="total_biaya_per_cm2{{ $item->id }}">Rp {{ number_format($item->total_biaya_per_cm2 ?? 0, 2, ',', '.') }}</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Deskripsi -->
                        <div class="tab-pane fade" id="edit-deskripsi{{ $item->id }}" role="tabpanel" aria-labelledby="edit-deskripsi-tab{{ $item->id }}">
                            <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="edit_deskripsi{{ $item->id }}" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="edit_deskripsi{{ $item->id }}" name="deskripsi" rows="3">{{ $item->deskripsi }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label for="edit_catatan_tambahan{{ $item->id }}" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" id="edit_catatan_tambahan{{ $item->id }}" name="catatan_tambahan" rows="2">{{ $item->catatan_tambahan }}</textarea>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Gambar -->
                        <div class="tab-pane fade" id="edit-gambar{{ $item->id }}" role="tabpanel" aria-labelledby="edit-gambar-tab{{ $item->id }}">
                            <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="edit_gambar{{ $item->id }}" class="form-label">Upload Gambar</label>
                                <input type="file" class="form-control" id="edit_gambar{{ $item->id }}" name="gambar" accept="image/*" onchange="previewImage(this, 'preview-edit-gambar{{ $item->id }}')">
                                        <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 5MB</small>
                                <div class="mt-2">
                                            @if($item->imageUrl)
                                                <img id="preview-edit-gambar{{ $item->id }}" src="{{ $item->imageUrl }}" alt="Gambar Mesin" style="max-width: 200px;" class="img-thumbnail">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="hapus_gambar{{ $item->id }}" name="hapus_gambar" value="1">
                                            <label class="form-check-label text-danger" for="hapus_gambar{{ $item->id }}">
                                                Hapus gambar ini
                                            </label>
                                        </div>
                                    @else
                                        <img id="preview-edit-gambar{{ $item->id }}" src="#" alt="Preview Gambar" style="max-width: 200px; display: none;" class="img-thumbnail">
                                    @endif
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i data-feather="cloud" class="icon-sm me-1"></i>
                                            Gambar akan diupload dan dioptimasi menggunakan Cloudinary
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-submit-edit" data-id="{{ $item->id }}">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
@endforeach 