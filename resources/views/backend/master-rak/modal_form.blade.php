<div class="modal fade" id="tambahRak" tabindex="-1" aria-labelledby="tambahRakLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahRakLabel">Tambah Rak Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRak" method="POST" action="{{ route('backend.master-rak.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Dasar</h6>
                            <div class="mb-3">
                                <label for="gudang_id" class="form-label">Gudang <span class="text-danger">*</span></label>
                                <select class="form-select" id="gudang_id" name="gudang_id" required>
                                    <option value="">Pilih gudang</option>
                                    @foreach($gudang as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Gudang harus dipilih</div>
                            </div>
                            <div class="mb-3">
                                <label for="nama_rak" class="form-label">Nama Rak <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_rak" name="nama_rak" required>
                                <div class="invalid-feedback">Nama rak harus diisi</div>
                            </div>
                            <div class="mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="0" required>
                                <small class="text-muted">Kapasitas maksimal rak</small>
                                <div class="invalid-feedback">Kapasitas harus diisi dan minimal 0</div>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_level" class="form-label">Jumlah Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah_level" name="jumlah_level" min="1" required>
                                <small class="text-muted">Jumlah tingkat pada rak</small>
                                <div class="invalid-feedback">Jumlah level harus diisi dan minimal 1</div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback">Status harus dipilih</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Dimensi & Spesifikasi</h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="lebar" class="form-label">Lebar (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="lebar" name="lebar" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="tinggi" class="form-label">Tinggi (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="tinggi" name="tinggi" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="kedalaman" class="form-label">Kedalaman (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="kedalaman" name="kedalaman" min="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" placeholder="Deskripsi rak..."></textarea>
                                <small class="text-muted">Informasi tambahan tentang rak (opsional)</small>
                            </div>
                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <b>Informasi Volume</b><br>
                                    Volume Total: <span id="infoVolume">0 m³</span><br>
                                    Dimensi: <span id="infoDimensi">0m × 0m × 0m</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitRak" disabled>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinnerRak"></span>
                        <i class="link-icon icon-sm me-1" data-feather="save"></i> Simpan Rak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 