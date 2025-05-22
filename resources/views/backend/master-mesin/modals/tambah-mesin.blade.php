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
                    <input type="hidden" name="biaya_tambahan_json" id="biaya_tambahan_json" value="[]">
                    
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
                                    <label for="tipe_mesin" class="form-label">Tipe Mesin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tipe_mesin" name="tipe_mesin" required>
                                        <option value="">Pilih Tipe Mesin</option>
                                        <option value="Printer Large Format">Printer Large Format</option>
                                        <option value="Digital Printer A3+">Digital Printer A3+</option>
                                        <option value="Mesin Finishing">Mesin Finishing</option>
                                    </select>
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
                                    <label for="harga_pembelian" class="form-label">Harga Pembelian</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga_pembelian" name="harga_pembelian" placeholder="0">
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
                                        <li>Lebar media maksimum digunakan untuk validasi ukuran cetak</li>
                                        <li>Spesifikasi tambahan bisa berupa resolusi, kecepatan cetak, dll</li>
                                        <li>Pastikan satuan yang digunakan konsisten</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mb-3" id="printer-specs">
                                <div class="col-md-6">
                                    <label for="lebar_media_maksimum" class="form-label">Lebar Media Maksimum (cm)</label>
                                    <input type="number" step="0.1" class="form-control" id="lebar_media_maksimum" name="lebar_media_maksimum" placeholder="0">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Spesifikasi Tambahan</label>
                                <div id="detail_mesin_container">
                                    <div class="row mb-2 detail-item">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="detail_nama[]" placeholder="Nama Spesifikasi">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="detail_nilai[]" placeholder="Nilai">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="detail_satuan[]" placeholder="Satuan">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-detail">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="tambah_detail">
                                    <i data-feather="plus"></i> Tambah Spesifikasi
                                </button>
                            </div>
                            
                            <div class="mt-2 mb-0">
                                <small class="text-muted">
                                    <i class="icon-sm" data-feather="info"></i> 
                                    Spesifikasi akan menyesuaikan dengan tipe mesin yang dipilih.
                                </small>
                            </div>
                        </div>
                    </div>
                        </div>

                        <!-- Tab Biaya Produksi -->
                        <div class="tab-pane fade" id="biaya-produksi" role="tabpanel" aria-labelledby="biaya-produksi-tab">
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
                                            <input type="number" class="form-control" name="harga_tinta_per_liter" id="harga_tinta_per_liter" placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Konsumsi Tinta per m² (mL)</label>
                                            <input type="number" class="form-control" name="konsumsi_tinta_per_m2" id="konsumsi_tinta_per_m2" placeholder="0" min="0" step="0.01">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="tambah_biaya">
                                        <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="biaya_tambahan_container">
                                        <div class="text-muted mb-2" id="no_biaya_message">
                                            Belum ada biaya tambahan. Klik tombol "Tambah Biaya" untuk menambahkan.
                                        </div>
                                        <div class="alert alert-light mt-2 mb-0" style="display: none;" id="total_biaya_tambahan_container">
                                            <div class="d-flex text-dark justify-content-between align-items-center">
                                                <span class="fw-bold">Total Biaya Tambahan:</span>
                                                <span class="fw-bold" id="total_biaya_tambahan">Rp 0</span>
                                            </div>
                                        </div>
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
                                                <td class="text-end"><span id="biaya_tinta_per_m2">Rp 0</span></td>
                                            </tr>
                                            <tr>
                                                <th>Biaya Tambahan per m²:</th>
                                                <td class="text-end"><span id="biaya_tambahan_per_m2">Rp 0</span></td>
                                            </tr>
                                            <tr class="table-active fw-bold">
                                                <th>Total Biaya per m²:</th>
                                                <td class="text-end"><span id="total_biaya_per_m2">Rp 0</span></td>
                                            </tr>
                                            <tr>
                                                <th>Total Biaya per cm²:</th>
                                                <td class="text-end"><span id="total_biaya_per_cm2">Rp 0</span></td>
                                            </tr>
                                        </table>
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