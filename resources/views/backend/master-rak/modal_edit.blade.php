<!-- Modal Edit Rak -->
<div class="modal fade" id="editRakModal" tabindex="-1" aria-labelledby="editRakModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRakModalLabel">Edit Rak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRakForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Dasar</h6>
                            <div class="mb-3">
                                <label for="edit_gudang_id" class="form-label">Gudang <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_gudang_id" name="gudang_id" required>
                                    <option value="">Pilih gudang</option>
                                    @foreach($gudang as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_kode_rak" class="form-label">Kode Rak <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_kode_rak" name="kode_rak" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit_nama_rak" class="form-label">Nama Rak <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_rak" name="nama_rak" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_jumlah_level" class="form-label">Jumlah Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_jumlah_level" name="jumlah_level" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Dimensi & Spesifikasi</h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="edit_lebar" class="form-label">Lebar (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_lebar" name="lebar" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_tinggi" class="form-label">Tinggi (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_tinggi" name="tinggi" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_kedalaman" class="form-label">Kedalaman (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_kedalaman" name="kedalaman" min="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="2" placeholder="Deskripsi rak..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditRak">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinnerEditRak"></span>
                        <i class="link-icon icon-sm me-1" data-feather="save"></i> Update Rak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 