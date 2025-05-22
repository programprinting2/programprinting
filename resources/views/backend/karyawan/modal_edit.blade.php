<!-- Modal Edit Karyawan -->
<div class="modal fade" id="modalEditKaryawan" tabindex="-1" aria-labelledby="modalEditKaryawanLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditKaryawanLabel">Edit Data Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditKaryawan">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <!-- Tab Navigation -->
          <ul class="nav nav-tabs mb-3" id="editKaryawanTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="edit-tab-utama" data-bs-toggle="tab" data-bs-target="#edit-utama" type="button" role="tab">
                <i data-feather="user" class="me-1 icon-sm"></i> Data Utama
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-alamat" data-bs-toggle="tab" data-bs-target="#edit-alamat" type="button" role="tab">
                <i data-feather="home" class="me-1 icon-sm"></i> Alamat
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-pajak" data-bs-toggle="tab" data-bs-target="#edit-pajak" type="button" role="tab">
                <i data-feather="file-text" class="me-1 icon-sm"></i> Pajak Penghasilan
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-rekening" data-bs-toggle="tab" data-bs-target="#edit-rekening" type="button" role="tab">
                <i data-feather="credit-card" class="me-1 icon-sm"></i> Rekening Gaji
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-gaji" data-bs-toggle="tab" data-bs-target="#edit-gaji" type="button" role="tab">
                <i data-feather="dollar-sign" class="me-1 icon-sm"></i> Gaji
              </button>
            </li>
          </ul>

          <div class="tab-content" id="editKaryawanTabContent">
            <!-- Data Utama -->
            <div class="tab-pane fade show active" id="edit-utama" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <!-- Informasi Utama -->
                  <div class="card mb-3">
                    <div class="card-header bg-light">
                      <h6 class="mb-0">Informasi Utama</h6>
                    </div>
                    <div class="card-body">
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label class="form-label">Departemen <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="edit_departemen" name="departemen" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Jabatan/Posisi <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="edit_posisi" name="posisi" required>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-4">
                          <label class="form-label">Tanggal Lahir</label>
                          <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Jenis Kelamin</label>
                          <select class="form-select" id="edit_jenis_kelamin" name="jenis_kelamin">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Status Pernikahan</label>
                          <select class="form-select" id="edit_status_pernikahan" name="status_pernikahan">
                            <option value="">Pilih status</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Cerai">Cerai</option>
                          </select>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                          <input type="date" class="form-control" id="edit_tanggal_masuk" name="tanggal_masuk" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Status <span class="text-danger">*</span></label>
                          <select class="form-select" id="edit_status" name="status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                          </select>
                        </div>
                      </div>
                      <div class="row mb-0">
                        <div class="col-md-6">
                          <label class="form-label">Email</label>
                          <input type="email" class="form-control" id="edit_email" name="email">
                          <div class="form-text">Digunakan untuk komunikasi dan notifikasi</div>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Nomor Telepon</label>
                          <input type="text" class="form-control" id="edit_nomor_telepon" name="nomor_telepon">
                          <div class="form-text">Format: 08xxxxxxxxxx</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab Alamat -->
            <div class="tab-pane fade" id="edit-alamat" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <div id="edit-alamat-list">
                    <!-- Area ini akan diisi secara dinamis -->
                  </div>
                  
                  <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="editAddAlamat">
                    <i data-feather="plus"></i> Tambah Alamat
                  </button>

                  <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i data-feather="info" class="me-2"></i>
                    <div>
                      <strong>Petunjuk:</strong>
                      <ul class="mb-0">
                        <li>Anda dapat menambahkan beberapa alamat (rumah, kost, dll)</li>
                        <li>Pilih satu alamat sebagai alamat utama</li>
                        <li>Isi kontak darurat untuk keperluan darurat</li>
                      </ul>
                    </div>
                  </div>

                  <div class="mb-0">
                    <label class="form-label">Alamat Utama <span class="text-danger">*</span></label>
                    <select class="form-select" id="edit_alamat_utama" name="alamat_utama" required>
                      <option value="">Pilih alamat utama</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pajak -->
            <div class="tab-pane fade" id="edit-pajak" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label">NPWP</label>
                      <input type="text" class="form-control" id="edit_npwp" name="npwp" placeholder="00.000.000.0-000.000">
                      <div class="form-text">Format: 00.000.000.0-000.000</div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label">Status Pajak</label>
                      <select class="form-select" id="edit_status_pajak" name="status_pajak">
                        <option value="">Pilih status pajak</option>
                        <option value="TK/0">TK/0 - Tidak Kawin/0 Tanggungan</option>
                        <option value="TK/1">TK/1 - Tidak Kawin/1 Tanggungan</option>
                        <option value="TK/2">TK/2 - Tidak Kawin/2 Tanggungan</option>
                        <option value="TK/3">TK/3 - Tidak Kawin/3 Tanggungan</option>
                        <option value="K/0">K/0 - Kawin/0 Tanggungan</option>
                        <option value="K/1">K/1 - Kawin/1 Tanggungan</option>
                        <option value="K/2">K/2 - Kawin/2 Tanggungan</option>
                        <option value="K/3">K/3 - Kawin/3 Tanggungan</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Tarif Pajak (%)</label>
                      <input type="number" class="form-control" id="edit_tarif_pajak" name="tarif_pajak" min="0" max="100" step="1" value="0">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Rekening -->
            <div class="tab-pane fade" id="edit-rekening" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <div id="edit-rekening-list">
                    <!-- Area ini akan diisi secara dinamis -->
                  </div>
                  
                  <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="editAddRekening">
                    <i data-feather="plus"></i> Tambah Rekening
                  </button>

                  <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i data-feather="info" class="me-2"></i>
                    <div>
                      Pilih salah satu sebagai rekening utama untuk penerimaan gaji
                    </div>
                  </div>

                  <div class="mb-0">
                    <label class="form-label">Rekening Utama <span class="text-danger">*</span></label>
                    <select class="form-select" id="edit_rekening_utama" name="rekening_utama" required>
                      <option value="">Pilih rekening utama</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gaji -->
            <div class="tab-pane fade" id="edit-gaji" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <!-- Gaji Pokok -->
                  <div class="card mb-3">
                    <div class="card-header bg-light">
                      <h6 class="mb-0">Gaji Pokok</h6>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control money-format" id="edit_gaji_pokok" name="gaji_pokok" required>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Perhitungan Jam Kerja -->
                  <div class="card mb-3">
                    <div class="card-header bg-light">
                      <h6 class="mb-0">Perhitungan Jam Kerja</h6>
                    </div>
                    <div class="card-body">
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label class="form-label">Estimasi Hari Kerja</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="edit_estimasi_hari_kerja" name="estimasi_hari_kerja" min="0">
                            <span class="input-group-text">hari/bulan</span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Jam Kerja Per Hari</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="edit_jam_kerja_per_hari" name="jam_kerja_per_hari" min="0">
                            <span class="input-group-text">jam/hari</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <label class="form-label">Total Jam Kerja</label>
                          <p class="form-control-plaintext border rounded bg-light px-2 fw-bold" id="edit_total_jam_kerja">0 jam/bulan</p>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Gaji Per Jam</label>
                          <p class="form-control-plaintext border rounded bg-light px-2 fw-bold" id="edit_gaji_per_jam">Rp 0</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Komponen Gaji -->
                  <div class="card mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                      <h6 class="mb-0">Komponen Gaji</h6>
                      <button type="button" class="btn btn-outline-primary btn-sm" id="editAddKomponenGaji">
                        <i data-feather="plus"></i> Tambah Komponen
                      </button>
                    </div>
                    <div class="card-body">
                      <div id="edit-komponen-gaji-list">
                        <!-- Area ini akan diisi secara dinamis -->
                        <div class="text-center text-muted p-3" id="edit-empty-komponen-message">
                          <i data-feather="info" class="mb-2"></i>
                          <p class="mb-0">Belum ada komponen gaji. Klik tombol "Tambah Komponen" untuk menambahkan tunjangan atau potongan.</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Total Gaji -->
                  <div class="alert alert-primary d-flex align-items-center mb-0" role="alert">
                    <i data-feather="dollar-sign" class="me-2"></i>
                    <div class="d-flex justify-content-between align-items-center w-100">
                      <span class="fw-bold">Total Gaji Per Bulan:</span>
                      <span class="fw-bold" id="edit_total_gaji_bulan">Rp 0</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('custom-scripts')
<script>
  // URL endpoint untuk AJAX
  const karyawanUpdateUrl = "{{ route('backend.karyawan.update', ':id') }}";
</script>
<script src="{{ asset('js/karyawan/karyawan-edit-modal.js') }}"></script>
@endpush 