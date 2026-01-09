@foreach($mesin as $item)
<div class="modal fade" id="editMesinModal{{ $item->id }}" tabindex="-1" aria-labelledby="editMesinModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMesinModalLabel{{ $item->id }}">Edit Mesin: {{ $item->nama_mesin }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditMesin{{ $item->id }}" action="{{ route('backend.master-mesin.update', $item->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="detail_mesin_json" id="edit_detail_mesin_json{{ $item->id }}" value="{{ json_encode($item->detail_mesin) }}">
                    <input type="hidden" name="biaya_perhitungan_profil_json" id="edit_biaya_perhitungan_profil_json{{ $item->id }}" value="{{ json_encode($item->biaya_perhitungan_profil) }}">
                    
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
                                    <label class="form-label">Tipe Mesin <span class="text-danger">*</span></label>
                                    <select class="form-select edit-tipe-mesin @error('tipe_mesin') is-invalid @enderror" name="tipe_mesin" id="edit_tipe_mesin{{ $item->id }}" required>
                                        <option value="">Pilih Tipe Mesin</option>
                                        @foreach($tipe_mesin as $tipe)
                                            <option value="{{ $tipe->nama_detail_parameter }}" {{ $item->tipe_mesin == $tipe->nama_detail_parameter ? 'selected' : '' }}>
                                                {{ $tipe->nama_detail_parameter }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipe_mesin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <label class="form-label">Harga Pembelian</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control money-format" name="harga_pembelian" id="edit_harga_pembelian{{ $item->id }}" value="{{ number_format($item->harga_pembelian, 0, ',', '.') }}">
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
                                                <li>Tambahkan spesifikasi mesin sesuai dengan kebutuhan</li>
                                        <li>Pastikan satuan yang digunakan konsisten</li>
                                                <li>Contoh spesifikasi: Dimensi, Daya Listrik, Resolusi, dll</li>
                                    </ul>
                                </div>
                            </div>
                                    
                                    <!-- Spesifikasi Mesin -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Spesifikasi Mesin</h6>
                                                <button type="button" class="btn btn-outline-primary btn-sm tambah-detail-edit" data-id="{{ $item->id }}">
                                                    <i data-feather="plus" class="icon-sm"></i> Tambah Spesifikasi
                                                </button>
                                </div>
                                </div>
                                        <div class="card-body">
                                <div id="edit_detail_mesin_container{{ $item->id }}">
                                    @if(is_array($item->detail_mesin) && count($item->detail_mesin) > 0)
                                        @foreach($item->detail_mesin as $detail)
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
                                                                    <i data-feather="trash-2" class="icon-sm"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <div class="text-muted text-center py-3" id="edit_no_specs_message{{ $item->id }}" style="display:none;">
                                                        Belum ada spesifikasi mesin. Klik tombol "Tambah Spesifikasi" untuk menambahkan.
                                                    </div>
                                                @else
                                                    <div class="text-muted text-center py-3" id="edit_no_specs_message{{ $item->id }}">
                                                        Belum ada spesifikasi mesin. Klik tombol "Tambah Spesifikasi" untuk menambahkan.
                                                </div>
                                                @endif
                                            </div>
                                            </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            
                        <!-- Tab Biaya Produksi -->
                        <div class="tab-pane fade" id="edit-biaya-produksi{{ $item->id }}" role="tabpanel" aria-labelledby="edit-biaya-produksi-tab{{ $item->id }}">
                            <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Profil Biaya Produksi</h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm tambah-profil-edit" data-id="{{ $item->id }}">
                                                <i data-feather="plus" class="icon-sm"></i> Tambah Profil
                                            </button>
                                        </div>
                                </div>
                                <div class="card-body">
                                        <div id="edit_biaya_perhitungan_profil_container{{ $item->id }}">
                                            <div class="text-muted text-center py-3" id="edit_no_profil_message{{ $item->id }}">
                                                Belum ada profil biaya. Klik tombol "Tambah Profil" untuk menambahkan.
                                            </div>
                                        </div>
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
                                            Gambar akan diupload menggunakan Supabase Storage
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
<script>
window.MODE_WARNA_OPTIONS = @json($mode_warna_options ?? []);
</script> 