<!-- Modal Edit Gudang -->
<div class="modal fade" id="editGudangModal" tabindex="-1" aria-labelledby="editGudangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGudangModalLabel">Edit Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGudangForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Dasar</h6>
                            <div class="mb-3">
                                <label for="edit_kode_gudang" class="form-label">Kode Gudang</label>
                                <input type="text" class="form-control" id="edit_kode_gudang" name="kode_gudang" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit_nama_gudang" class="form-label">Nama Gudang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_gudang" name="nama_gudang" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_manager" class="form-label">Manager <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_manager" name="manager" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_kapasitas" class="form-label">Kapasitas (m³) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="0" required>
                                <small class="text-muted">Kapasitas total gudang dalam meter kubik</small>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="2" placeholder="Deskripsi gudang..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Alamat & Kontak</h6>
                            <div class="mb-3">
                                <label for="edit_alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_alamat" name="alamat" rows="2" required></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_kota" name="kota" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_provinsi" name="provinsi" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_kode_pos" name="kode_pos" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="edit_no_telepon" name="no_telepon">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditGudang">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinnerEditGudang"></span>
                        <i class="link-icon icon-sm me-1" data-feather="save"></i> Update Gudang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 