<!-- Modal Edit Bahan Baku -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Bahan Baku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body modal-body-scrollable">
          <ul class="nav nav-tabs mb-3" id="editMaterialTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="edit-informasi-umum-tab" data-bs-toggle="tab" data-bs-target="#edit-informasi-umum" type="button" role="tab" aria-controls="edit-informasi-umum" aria-selected="true"><i data-feather="info" class="me-1 icon-sm"></i> Informasi Umum</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-spesifikasi-teknis-tab" data-bs-toggle="tab" data-bs-target="#edit-spesifikasi-teknis" type="button" role="tab" aria-controls="edit-spesifikasi-teknis" aria-selected="false"><i data-feather="tool" class="me-1 icon-sm"></i> Spesifikasi Teknis</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-konversi-satuan-tab" data-bs-toggle="tab" data-bs-target="#edit-konversi-satuan" type="button" role="tab" aria-controls="edit-konversi-satuan" aria-selected="false"><i data-feather="refresh-cw" class="me-1 icon-sm"></i> Satuan & Harga</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-informasi-stok-tab" data-bs-toggle="tab" data-bs-target="#edit-informasi-stok" type="button" role="tab" aria-controls="edit-informasi-stok" aria-selected="false"><i data-feather="box" class="me-1 icon-sm"></i> Informasi Stok</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-media-dokumen-tab" data-bs-toggle="tab" data-bs-target="#edit-media-dokumen" type="button" role="tab" aria-controls="edit-media-dokumen" aria-selected="false"><i data-feather="file-text" class="me-1 icon-sm"></i> Media & Dokumen</button>
            </li>
          </ul>

          <div class="tab-content mt-3">
            <!-- Informasi Umum Tab -->
            <div class="tab-pane fade show active" id="edit-informasi-umum" role="tabpanel" aria-labelledby="edit-informasi-umum-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_kode_bahan" class="form-label">Kode Bahan</label>
                      <input type="text" class="form-control" id="edit_kode_bahan" name="kode_bahan" readonly>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="edit_nama_bahan" name="nama_bahan" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_kategori" name="kategori_id" required>
                        <option value="" selected disabled>Pilih kategori</option>
                        @foreach($kategoriList as $kat)
                          <option value="{{ $kat->id }}">{{ $kat->nama_detail_parameter }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_sub_kategori_id" class="form-label">Sub Kategori <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_sub_kategori_id" name="sub_kategori_id">
                        <option value="" selected disabled>Pilih sub-kategori</option>
                        <!-- Options akan diisi dinamis oleh JS, value=id -->
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_pemasok_utama" class="form-label">Pemasok Utama</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="editNamaPemasokUtama" placeholder="Pilih pemasok..." readonly style="background:#fff;cursor:pointer;">
                        <input type="hidden" id="editPemasokUtamaId" name="pemasok_utama_id">
                        <button class="btn btn-outline-secondary" type="button" id="btnEditCariPemasokUtama"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_status_aktif" class="form-label">Status Aktif</label>
                      <select class="form-select" id="edit_status_aktif" name="status_aktif" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                      </select>
                      <div class="form-text">Bahan baku ini aktif dan dapat digunakan dalam proses produksi</div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_keterangan" class="form-label">Keterangan</label>
                      <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="2"></textarea>
                    </div>
                    <div class="col-md-5">
                        <label for="edit_warna_id" class="form-label">Warna</label>
                        <select class="form-select" id="edit_warna_id" name="warna_id">
                            <option value="">Pilih warna (opsional)</option>
                            @foreach($modeWarnaOptions ?? [] as $warnaOption)
                            <option value="{{ $warnaOption->id }}" data-hex="{{ $warnaOption->keterangan }}">{{ $warnaOption->nama_detail_parameter }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih warna bahan baku jika ada</small>
                    </div>
                    <div class="col-md-1">
                      <label class="form-label">&nbsp;</label>
                      <div id="editWarnaPreviewModal" class="warna-preview-modal" 
                          style="display: none; height: 38px; border: 2px solid #ced4da; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                          border-radius: 0.375rem; " title="Preview warna">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Spesifikasi Teknis Tab -->
            <div class="tab-pane fade" id="edit-spesifikasi-teknis" role="tabpanel" aria-labelledby="edit-spesifikasi-teknis-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i data-feather="info" class="me-2"></i>
                    <div>
                      <strong>Petunjuk:</strong>
                      <ul class="mb-0">
                        <li>Tambahkan spesifikasi teknis sesuai kebutuhan bahan baku</li>
                        <li>Contoh: Dimensi, Berat, Warna, Daya Serap, dll</li>
                      </ul>
                    </div>
                  </div>
                  <div class="card mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                      <h6 class="mb-0">Spesifikasi Teknis</h6>
                      <button type="button" class="btn btn-outline-primary btn-sm" id="edit_tambah_detail_spesifikasi">
                        <i data-feather="plus" class="icon-sm"></i> Tambah Spesifikasi
                      </button>
                    </div>
                    <div class="card-body">
                      <div id="edit_detail_spesifikasi_container">
                        <div class="text-muted text-center py-3" id="edit_no_spesifikasi_message">
                          Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk menambahkan.
                    </div>
                  </div>
                      <input type="hidden" name="detail_spesifikasi_json" id="edit_detail_spesifikasi_json" value="[]">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Konversi Satuan Tab -->
            <div class="tab-pane fade" id="edit-konversi-satuan" role="tabpanel" aria-labelledby="edit-konversi-satuan-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label for="edit_satuan_utama" class="form-label">Satuan Utama <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_satuan_utama" name="satuan_utama_id" required>
                        <option value="" selected disabled>Pilih satuan</option>
                        @foreach($satuanList as $satuan)
                          <option value="{{ $satuan->id }}">{{ $satuan->nama_detail_parameter }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label for="edit_sub_satuan" class="form-label">Detail Satuan <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_sub_satuan" name="sub_satuan_id" required>
                          <option value="" selected disabled>Pilih detail satuan</option>
                          <!-- Options akan diisi dinamis oleh JS -->
                      </select>
                      <small class="text-muted">Detail lebih spesifik dari satuan yang dipilih</small>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Harga Terakhir 
                      <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="edit-info-harga-terakhir" 
                              data-bs-toggle="tooltip" title="Lihat produk yang menggunakan bahan baku ini">
                        <i class="fas fa-info-circle text-info"></i>
                      </button>
                      </label>
                      <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <!-- <input type="number" class="form-control" id="edit_harga_terakhir" name="harga_terakhir" value="0" step="0.01" min="0"> -->
                        <input class="form-control" data-inputmask="'alias': 'currency', 'groupSeparator':',', 'radixPoint':'.', 'digits':2, 'autoGroup':true, 'removeMaskOnSubmit':true" id="edit_harga_terakhir" name="harga_terakhir" value="0" >
                        <span class="input-group-text" id="editLabelSatuanHargaTerakhir"></span>
                        <input type="hidden" id="edit_id" name="id" value="">
                      </div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h6 class="mb-0">Konversi Satuan</h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="editTambahKonversi"><i data-feather="plus" class="me-1 icon-sm"></i>Tambah Konversi</button>
                  </div>
                  <div id="editConversionUnitsContainer">
                    <!-- Dynamic conversion rows will be added here -->
                  </div>
                </div>
              </div>
            </div>

            <!-- Informasi Stok Tab -->
            <div class="tab-pane fade" id="edit-informasi-stok" role="tabpanel" aria-labelledby="edit-informasi-stok-tab">
              <div class="row">
                <div class="col-md-6">
                  <div class="card mb-3 mb-md-0">
                    <div class="card-body">
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="edit_stok_saat_ini" class="form-label">Stok Saat Ini</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="edit_stok_saat_ini" name="stok_saat_ini" min="0" value="0" step="0.01">
                            <span class="input-group-text" id="editStokSaatIniUnit">Unit</span>
                          </div>
                          <small class="text-muted">Jumlah stok tersedia saat ini</small>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="edit_stok_minimum" class="form-label">Stok Minimum</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="edit_stok_minimum" name="stok_minimum" min="0">
                            <span class="input-group-text" id="editStokMinimumUnit">Unit</span>
                          </div>
                          <small class="text-muted">Jumlah stok minimum sebelum perlu re-order</small>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="edit_stok_maksimum" class="form-label">Stok Maksimum</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="edit_stok_maksimum" name="stok_maksimum" min="0">
                            <span class="input-group-text" id="editStokMaksimumUnit">Unit</span>
                          </div>
                          <small class="text-muted">Jumlah stok maksimum yang direkomendasikan</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card mb-3">
                    <div class="card-body">
                      <h6 class="card-title">Informasi Stok</h6>
                      <p class="mb-2">Status Stok: <span id="editStatusStokText"></span></p>
                      <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" id="editStokProgressBar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <p class="text-muted" style="font-size: 0.85rem;" id="editStokSummary"></p>
                      <div class="alert alert-danger p-2 mb-0 d-none" role="alert" id="editStokAlert">
                        Stok di bawah minimum! Perlu melakukan pembelian segera.
                      </div>
                    </div>
                  </div>
                  <div class="card mb-3">
                    <div class="card-body">
                      <h6 class="card-title">Estimasi Nilai Stok</h6>
                      <p class="mb-0">Total Nilai Stok: <span id="editTotalNilaiStok">Rp 0</span></p>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <h6 class="card-title">Info Stok dan Gudang</h6>
                      <p class="mb-0 text-muted" style="font-size: 0.9rem;">Untuk pengelolaan gudang secara detail, silakan gunakan modul "Manajemen Stok & Gudang" yang memungkinkan pengaturan multi-gudang, pindah stok antar gudang, dan pencatatan lokasi stok secara detail.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Media & Dokumen Tab -->
            <div class="tab-pane fade" id="edit-media-dokumen" role="tabpanel" aria-labelledby="edit-media-dokumen-tab">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Media (Foto & Video)</h6>
                        <div class="dropzone-area-media mb-3 text-center p-4 border-2 border-dashed rounded bg-light position-relative" id="editMediaDropzoneArea" style="cursor:pointer;">
                            <input type="file" class="d-none" id="editMediaPendukungInput" name="media_pendukung_new[]" multiple accept="image/*,video/*">
                            <div class="dz-message text-muted">
                                <i data-feather="upload-cloud" class="icon-lg mb-2"></i><br>
                                <span>Seret & lepas foto/video di sini atau klik untuk memilih file</span>
                                <div style="font-size:0.85rem;">Maksimal 10 file, format gambar/video didukung</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12 mb-1 text-start"><strong>Foto</strong></div>
                            <div class="col-12" id="editFotoPendukungPreview">
                                <div class="text-muted text-center" id="noEditFotoMessage">
                                <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-12 mb-1 text-start"><strong>Video</strong></div>
                            <div class="col-12" id="editVideoPendukungPreview">
                                <div class="text-muted text-center" id="noEditVideoMessage">
                                <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video yang ditambahkan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Dokumen Pendukung</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="editTambahDokumen"><i data-feather="plus" class="me-1 icon-sm"></i> Tambah Dokumen</button>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan dokumen pendukung seperti spesifikasi teknis, sertifikat, atau laporan uji.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="editDokumenPendukungTable">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Nama Dokumen</th>
                                        <th style="width: 20%;">Jenis</th>
                                        <th style="width: 15%;">Ukuran</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="editDokumenPendukungBody">
                                    <!-- Document rows will be added here -->
                                    <tr id="noEditDokumenMessage">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Input file dokumen pendukung (hidden) -->
                        <input type="file" class="d-none" id="editDokumenPendukungInput" name="dokumen_pendukung_new[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv,.jpg,.jpeg,.png,.gif">
                    </div>
                </div>

                <!-- Link Pendukung -->
                <div class="card mt-3 mb-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Link Pendukung</h6>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan link pendukung seperti Google Drive, YouTube, atau website lain yang relevan.</p>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <input type="url" class="form-control" id="editInputLinkPendukung" placeholder="https://contoh.com/link-pendukung">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="editInputKeteranganLinkPendukung" placeholder="Keterangan (opsional)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-primary w-100" id="editTambahLinkPendukung"><i data-feather="plus" class="icon-sm me-1"></i>Tambah Link</button>
                            </div>
                        </div>
                        <ul class="list-group" id="editDaftarLinkPendukung">
                            <!-- Daftar link akan muncul di sini -->
                        </ul>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Preview Media (Foto/Video) -->
<div class="modal fade" id="editMediaPreviewModal" tabindex="-1" aria-labelledby="editMediaPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editMediaPreviewModalLabel">Preview Media</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" id="editMediaPreviewModalBody">
        <!-- Konten media akan diisi via JS -->
      </div>
    </div>
  </div>
</div>

@push('custom-styles')
<style>
.warna-preview-modal {
    width: 100%;
    height: 40px;
    cursor: pointer;
}

.warna-preview-modal:hover {
    border-color: #6c757d;
    transform: scale(1.02);
    transition: all 0.2s ease;
}
</style>
@endpush

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
@endpush

@push('custom-scripts')
@include('backend.general-form.cari-pemasok', [
  'modalId' => 'modalCariPemasokBahanBakuEdit',
  'inputId' => 'searchPemasokBahanBakuEdit',
  'tableId' => 'tabelCariPemasokBahanBakuEdit',
  'paginationId' => 'paginationCariPemasokBahanBakuEdit',
  'clearBtnId' => 'clearSearchPemasokBahanBakuEdit',
])
<script src="{{ asset('assets/js/inputmask.js') }}"></script>
<script>
$(function() {
  $('#btnEditCariPemasokUtama, #editNamaPemasokUtama').on('click', function() {
    $('#modalCariPemasokBahanBakuEdit').modal('show');
  });
  window.addEventListener('pemasokDipilih', function(e) {
    if (!$('#modalCariPemasokBahanBakuEdit').hasClass('show')) return;
    const data = e.detail;
    $('#editNamaPemasokUtama').val(data.nama + (data.kode ? ' ['+data.kode+']' : ''));
    $('#editPemasokUtamaId').val(data.id);
    $('#modalCariPemasokBahanBakuEdit').modal('hide');
  });
});
</script>
  <script>
    // Event listener untuk perubahan kategori di modal edit
    $(document).ready(function() {
      // Inisialisasi awal
      updateEditSubKategoriOptions($('#edit_kategori').val(), $('#edit_sub_kategori_id').val());

      $('#edit_kategori').on('change', function() {
        const selectedKategoriId = $(this).val();
        updateEditSubKategoriOptions(selectedKategoriId);
      });

      // Panggil saat modal edit ditampilkan (untuk mengupdate sub-kategori berdasarkan data yang dimuat)
      $('#editModal').on('shown.bs.modal', function () {
        const selectedKategoriOnShow = $('#edit_kategori').val();
        const currentSubKategoriOnShow = $('#edit_sub_kategori_id').val();
        updateEditSubKategoriOptions(selectedKategoriOnShow, currentSubKategoriOnShow);
      });
    });
  </script>
  <script src="{{ asset('js/bahanbaku/edit-modal.js') }}"></script>
@endpush 