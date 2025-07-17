<div class="modal fade" id="tambahGudang" tabindex="-1" aria-labelledby="tambahGudangLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahGudangLabel">Tambah Gudang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formGudang" method="POST" action="{{ route('backend.master-gudang.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Dasar</h6>
                            <div class="mb-3">
                                <label for="nama_gudang" class="form-label">Nama Gudang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_gudang" name="nama_gudang" required>
                                <div class="invalid-feedback">Nama gudang harus diisi</div>
                            </div>
                            <div class="mb-3">
                                <label for="manager" class="form-label">Manager <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="manager" name="manager" required>
                                <div class="invalid-feedback">Manager harus diisi</div>
                            </div>
                            <div class="mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas (m³) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="0" required>
                                <small class="text-muted">Kapasitas total gudang dalam meter kubik</small>
                                <div class="invalid-feedback">Kapasitas harus diisi dan minimal 0</div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback">Status harus dipilih</div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" placeholder="Deskripsi gudang..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Alamat & Kontak</h6>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" required></textarea>
                                <div class="invalid-feedback">Alamat harus diisi</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kota" name="kota" required>
                                    <div class="invalid-feedback">Kota harus diisi</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="provinsi" name="provinsi" required>
                                    <div class="invalid-feedback">Provinsi harus diisi</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode_pos" name="kode_pos" required>
                                    <div class="invalid-feedback">Kode pos harus diisi</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                                <div class="invalid-feedback">Format email tidak valid</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitGudang" disabled>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinnerGudang"></span>
                        <i class="link-icon icon-sm me-1" data-feather="save"></i> Simpan Gudang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 