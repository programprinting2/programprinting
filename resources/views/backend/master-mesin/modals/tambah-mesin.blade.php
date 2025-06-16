<div class="modal fade" id="tambahMesinModal" tabindex="-1" aria-labelledby="tambahMesinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahMesinModalLabel">Tambah Mesin Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahMesin" action="{{ route('backend.master-mesin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="detail_mesin_json" id="detail_mesin_json" value="[]">
                    <input type="hidden" name="biaya_perhitungan_profil_json" id="biaya_perhitungan_profil_json" value="[]">
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="mesinTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="identitas-tab" data-bs-toggle="tab" data-bs-target="#identitas" type="button" role="tab" aria-controls="identitas" aria-selected="true">
                                <i data-feather="info" class="me-1 icon-sm"></i> Identitas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="spesifikasi-tab" data-bs-toggle="tab" data-bs-target="#spesifikasi" type="button" role="tab" aria-controls="spesifikasi" aria-selected="false">
                                <i data-feather="settings" class="me-1 icon-sm"></i> Spesifikasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="biaya-produksi-tab" data-bs-toggle="tab" data-bs-target="#biaya-produksi" type="button" role="tab" aria-controls="biaya-produksi" aria-selected="false">
                                <i data-feather="dollar-sign" class="me-1 icon-sm"></i> Biaya Produksi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="deskripsi-tab" data-bs-toggle="tab" data-bs-target="#deskripsi" type="button" role="tab" aria-controls="deskripsi" aria-selected="false">
                                <i data-feather="file-text" class="me-1 icon-sm"></i> Deskripsi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="gambar-tab" data-bs-toggle="tab" data-bs-target="#gambar" type="button" role="tab" aria-controls="gambar" aria-selected="false">
                                <i data-feather="image" class="me-1 icon-sm"></i> Gambar
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="mesinTabContent">
                        <!-- Tab Identitas -->
                        <div class="tab-pane fade show active" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">
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
                                    <label for="nama_mesin" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_mesin" name="nama_mesin" placeholder="Nama Mesin" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipe Mesin <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tipe_mesin') is-invalid @enderror" name="tipe_mesin" id="tipe_mesin" required>
                                        <option value="">Pilih Tipe Mesin</option>
                                        @foreach($tipe_mesin as $tipe)
                                            <option value="{{ $tipe->nama_detail_parameter }}" {{ old('tipe_mesin') == $tipe->nama_detail_parameter ? 'selected' : '' }}>
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
                                    <label for="merek" class="form-label">Merek</label>
                                    <input type="text" class="form-control" id="merek" name="merek" placeholder="Merek Mesin">
                                </div>
                                <div class="col-md-6">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control" id="model" name="model" placeholder="Model Mesin">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nomor_seri" class="form-label">Nomor Seri</label>
                                    <input type="text" class="form-control" id="nomor_seri" name="nomor_seri" placeholder="Nomor Seri">
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Aktif">Aktif</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Rusak">Rusak</option>
                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tanggal_pembelian" class="form-label">Tanggal Pembelian</label>
                                    <input type="date" class="form-control" id="tanggal_pembelian" name="tanggal_pembelian">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga Pembelian</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control money-format" name="harga_pembelian" id="harga_pembelian">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Spesifikasi -->
                        <div class="tab-pane fade" id="spesifikasi" role="tabpanel" aria-labelledby="spesifikasi-tab">
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
                                                <button type="button" class="btn btn-outline-primary btn-sm" id="tambah_detail">
                                                    <i data-feather="plus" class="icon-sm"></i> Tambah Spesifikasi
                                                </button>
                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="detail_mesin_container">
                                                <div class="text-muted text-center py-3" id="no_specs_message">
                                                    Belum ada spesifikasi mesin. Klik tombol "Tambah Spesifikasi" untuk menambahkan.
                                        </div>
                                        </div>
                                        </div>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Biaya Produksi -->
                        <div class="tab-pane fade" id="biaya-produksi" role="tabpanel" aria-labelledby="biaya-produksi-tab">
                            <div class="col-md-12">
                                <div class="card">
                                <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Profil Biaya Produksi</h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="tambah_profil">
                                                <i data-feather="plus" class="icon-sm"></i> Tambah Profil
                                            </button>
                                        </div>
                                </div>
                                <div class="card-body">
                                        <div id="biaya_perhitungan_profil_container">
                                            <div class="text-muted text-center py-3" id="no_profil_message">
                                                Belum ada profil biaya. Klik tombol "Tambah Profil" untuk menambahkan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Deskripsi -->
                        <div class="tab-pane fade" id="deskripsi" role="tabpanel" aria-labelledby="deskripsi-tab">
                            <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat mengenai mesin..."></textarea>
                            </div>
                            <div class="mb-0">
                                <label for="catatan_tambahan" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" id="catatan_tambahan" name="catatan_tambahan" rows="2" placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Gambar -->
                        <div class="tab-pane fade" id="gambar" role="tabpanel" aria-labelledby="gambar-tab">
                            <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Upload Gambar</label>
                                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this, 'preview-gambar')">
                                        <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 5MB</small>
                                <div class="mt-2">
                                    <img id="preview-gambar" src="#" alt="Preview Gambar" style="max-width: 200px; display: none;" class="img-thumbnail">
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
                <button type="button" id="submitFormMesin" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div> 

<script>
window.MODE_WARNA_OPTIONS = @json($mode_warna_options ?? []);
</script> 