<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addMaterialModalLabel">Tambah Bahan Baku Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addMaterialForm">
        @csrf
        <div class="modal-body modal-body-scrollable">
          <!-- <p class="text-muted mb-3">Isi informasi bahan baku dengan lengkap beserta detail pengukuran, konversi satuan, dan histori harga.</p> -->
          <ul class="nav nav-tabs mb-3" id="materialTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="informasi-umum-tab" data-bs-toggle="tab" data-bs-target="#informasi-umum" type="button" role="tab" aria-controls="informasi-umum" aria-selected="true"><i data-feather="info" class="me-1 icon-sm"></i> Informasi Umum</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="spesifikasi-teknis-tab" data-bs-toggle="tab" data-bs-target="#spesifikasi-teknis" type="button" role="tab" aria-controls="spesifikasi-teknis" aria-selected="false"><i data-feather="tool" class="me-1 icon-sm"></i> Spesifikasi Teknis</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="konversi-satuan-tab" data-bs-toggle="tab" data-bs-target="#konversi-satuan" type="button" role="tab" aria-controls="konversi-satuan" aria-selected="false"><i data-feather="refresh-cw" class="me-1 icon-sm"></i> Satuan & Harga</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="informasi-stok-tab" data-bs-toggle="tab" data-bs-target="#informasi-stok" type="button" role="tab" aria-controls="informasi-stok" aria-selected="false"><i data-feather="box" class="me-1 icon-sm"></i> Informasi Stok</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="media-dokumen-tab" data-bs-toggle="tab" data-bs-target="#media-dokumen" type="button" role="tab" aria-controls="media-dokumen" aria-selected="false"><i data-feather="file-text" class="me-1 icon-sm"></i> Media & Dokumen</button>
            </li>
          </ul>

          <div class="tab-content mt-3">
            <!-- Informasi Umum Tab -->
            <div class="tab-pane fade show active" id="informasi-umum" role="tabpanel" aria-labelledby="informasi-umum-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                      <label for="namaBahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="namaBahan" name="nama_bahan" placeholder="Nama bahan baku" required>
                    </div>
                    <div class="col-md-5">
                        <label for="warna_id" class="form-label">Warna</label>
                        <select class="form-select" id="warna_id" name="warna_id">
                            <option value="">Pilih warna (opsional)</option>
                            @foreach($modeWarnaOptions ?? [] as $warnaOption)
                                <option data-hex="{{ $warnaOption->keterangan }}" value="{{ $warnaOption->id }}">{{ $warnaOption->nama_detail_parameter }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div id="warnaPreviewModal" class="warna-preview-modal" 
                            style="display: none; height: 38px; border: 2px solid #ced4da; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                            border-radius: 0.375rem; " title="Preview warna">
                        </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                      <select class="form-select" id="kategori" name="kategori_id" required>
                        <option value="" selected disabled>Pilih kategori</option>
                        @foreach($kategoriList as $kat)
                          <option value="{{ $kat->id }}">{{ $kat->nama_detail_parameter }}</option>
                        @endforeach
                      </select>
                      <small class="text-muted">Otomatis menyesuaikan metode perhitungan sesuai kategori</small>
                    </div>
                    <div class="col-md-6">
                      <label for="subKategori" class="form-label">Sub-Kategori <span class="text-danger">*</span></label>
                      <select class="form-select" id="sub_kategori_id" name="sub_kategori_id">
                        <option value="" selected disabled>Pilih sub-kategori</option>
                        <!-- Options akan diisi dinamis oleh JS, value=id -->
                      </select>
                      <small class="text-muted">Pengelompokan lebih detail dalam kategori yang sama</small>
                    </div>
                  </div>
                    
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="pemasokUtama" class="form-label">Pemasok Utama</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="namaPemasokUtama" placeholder="Pilih pemasok..." readonly style="background:#fff;cursor:pointer;">
                        <input type="hidden" id="pemasokUtamaId" name="pemasok_utama_id">
                        <button class="btn btn-outline-secondary" type="button" id="btnCariPemasokUtama"><i class="fa fa-search"></i></button>
                      </div>
                      <small class="text-muted">Pemasok utama untuk pembelian bahan baku</small>
                    </div>

                    <div class="col-md-6">
                      <label for="statusAktif" class="form-label">Status Aktif</label>
                      <select class="form-select" id="statusAktif" name="status_aktif" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                      </select>
                      <div class="form-text">Bahan baku ini aktif dan dapat digunakan dalam proses produksi</div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="deskripsi" class="form-label">Spesifikasi</label>
                      <textarea class="form-control" id="deskripsi" name="keterangan" rows="3" placeholder="Deskripsi lengkap dan spesifikasi teknis bahan baku."></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Spesifikasi Teknis Tab -->
            <div class="tab-pane fade" id="spesifikasi-teknis" role="tabpanel" aria-labelledby="spesifikasi-teknis-tab">
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
                      <button type="button" class="btn btn-outline-primary btn-sm" id="tambah_detail_spesifikasi">
                        <i data-feather="plus" class="icon-sm"></i> Tambah Spesifikasi
                      </button>
                    </div>
                    <div class="card-body">
                      <div id="detail_spesifikasi_container">
                        <div class="text-muted text-center py-3" id="no_spesifikasi_message">
                          Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk menambahkan.
                    </div>
                  </div>
                      <input type="hidden" name="detail_spesifikasi_json" id="detail_spesifikasi_json" value="[]">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Konversi Satuan Tab -->
            <div class="tab-pane fade" id="konversi-satuan" role="tabpanel" aria-labelledby="konversi-satuan-tab">
              <div class="card mb-0">
                <div class="card-body">

                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label for="satuanUtama" class="form-label">Satuan Utama <span class="text-danger">*</span></label>
                      <select class="form-select" id="satuanUtama" name="satuan_utama_id" required>
                        <option value="" selected disabled>Pilih satuan</option>
                        @foreach($satuanList as $satuan)
                          <option value="{{ $satuan->id }}">{{ $satuan->nama_detail_parameter }}</option>
                        @endforeach
                      </select>
                      <small class="text-muted">Satuan utama untuk perhitungan stok</small>
                    </div>

                    <div class="col-md-4">
                      <label for="sub_satuan" class="form-label">Detail Satuan <span class="text-danger">*</span></label>
                      <select class="form-select" id="sub_satuan" name="sub_satuan_id" required>
                          <option value="" selected disabled>Pilih detail satuan</option>
                          <!-- Options akan diisi dinamis oleh JS -->
                      </select>
                      <small class="text-muted">Detail lebih spesifik dari satuan yang dipilih</small>
                    </div>

                    <div class="col-md-4">
                      <label for="hargaTerakhir" class="form-label">Harga Terakhir
                      <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="info-harga-terakhir" 
                              data-bs-toggle="tooltip" title="Lihat produk yang menggunakan bahan baku ini">
                        <i class="fas fa-info-circle text-info"></i>
                      </button>
                      </label>
                      <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <!-- <input type="number" class="form-control" id="hargaTerakhir" name="harga_terakhir" value="0" step="0.01" min="0"> -->
                        <input class="form-control" data-inputmask="'alias': 'currency', 'groupSeparator':',', 'radixPoint':'.', 'digits':2, 'autoGroup':true, 'removeMaskOnSubmit':true" id="hargaTerakhir" name="harga_terakhir" value="0" >
                        <span class="input-group-text" id="labelSatuanHargaTerakhir"></span>
                      </div>
                      <small class="text-muted">Harga beli terakhir dari pemasok utama</small>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    
                    <div>
                      <h6 class="mb-0">Konversi Satuan</h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="tambahKonversi"><i data-feather="plus" class="me-1 icon-sm"></i>Tambah Konversi</button>
                  </div>
                  
                  <div id="conversionUnitsContainer">
                    <!-- Dynamic conversion rows will be added here -->
                    <div class="col-12 text-center text-muted py-4" id="noConversionMessage">
                        <i data-feather="refresh-cw" class="icon-lg mb-2"></i><br>Belum ada konversi satuan yang ditambahkan.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Informasi Stok Tab -->
            <div class="tab-pane fade" id="informasi-stok" role="tabpanel" aria-labelledby="informasi-stok-tab">
              <div class="row">
                <div class="col-md-6">
                  <div class="card mb-3 mb-md-0">
                    <div class="card-body">
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="stokSaatIni" class="form-label">Stok Saat Ini</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="stokSaatIni" name="stok_saat_ini" value="0" step="0.01" min="0">
                            <span class="input-group-text" id="stokSaatIniUnit">Unit</span>
                          </div>
                          <small class="text-muted">Jumlah stok tersedia saat ini</small>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="stokMinimum" class="form-label">Stok Minimum</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="stokMinimum" name="stok_minimum" value="20" min="0">
                            <span class="input-group-text" id="stokMinimumUnit">Unit</span>
                          </div>
                          <small class="text-muted">Jumlah stok minimum sebelum perlu re-order</small>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="stokMaksimum" class="form-label">Stok Maksimum</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="stokMaksimum" name="stok_maksimum" value="100" min="0">
                            <span class="input-group-text" id="stokMaksimumUnit">Unit</span>
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
                      <p class="mb-2">Status Stok: <span id="statusStokText"></span></p>
                      <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" id="stokProgressBar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <p class="text-muted" style="font-size: 0.85rem;" id="stokSummary"></p>
                      <div class="alert alert-danger p-2 mb-0 d-none" role="alert" id="stokAlert">
                        Stok di bawah minimum! Perlu melakukan pembelian segera.
                      </div>
                    </div>
                  </div>
                  <div class="card mb-3">
                    <div class="card-body">
                      <h6 class="card-title">Estimasi Nilai Stok</h6>
                      <p class="mb-0">Total Nilai Stok: <span id="totalNilaiStok">Rp 0</span></p>
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
            <div class="tab-pane fade" id="media-dokumen" role="tabpanel" aria-labelledby="media-dokumen-tab">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Media (Foto & Video)</h6>
                        <div class="dropzone-area-media mb-3 text-center p-4 border-2 border-dashed rounded bg-light position-relative" id="mediaDropzoneArea" style="cursor:pointer;">
                            <input type="file" class="d-none" id="mediaPendukungInput" name="media_pendukung_new[]" multiple accept="image/*,video/*">
                            <div class="dz-message text-muted">
                                <i data-feather="upload-cloud" class="icon-lg mb-2"></i><br>
                                <span>Seret & lepas foto/video di sini atau klik untuk memilih file</span>
                                <div style="font-size:0.85rem;">Maksimal 10 file, format gambar/video didukung</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12 mb-1 text-start"><strong>Foto</strong></div>
                            <div class="col-12" id="fotoPendukungPreview">
                                <div class="text-muted text-center" id="noFotoMessage">
                                <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-12 mb-1 text-start"><strong>Video</strong></div>
                            <div class="col-12" id="videoPendukungPreview">
                                <div class="text-muted text-center" id="noVideoMessage">
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
                            <button type="button" class="btn btn-sm btn-primary" id="tambahDokumen"><i data-feather="plus" class="me-1 icon-sm"></i> Tambah Dokumen</button>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan dokumen pendukung seperti spesifikasi teknis, sertifikat, atau laporan uji.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="dokumenPendukungTable">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Nama Dokumen</th>
                                        <th style="width: 20%;">Jenis</th>
                                        <th style="width: 15%;">Ukuran</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dokumenPendukungBody">
                                    <!-- Document rows will be added here -->
                                    <tr id="noDokumenMessage">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Input file dokumen pendukung (hidden) -->
                        <input type="file" class="d-none" id="dokumenPendukungInput" name="dokumen_pendukung_new[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv,.jpg,.jpeg,.png,.gif">
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
                                <input type="url" class="form-control" id="inputLinkPendukung" placeholder="https://contoh.com/link-pendukung">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="inputKeteranganLinkPendukung" placeholder="Keterangan (opsional)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-primary w-100" id="tambahLinkPendukung"><i data-feather="plus" class="icon-sm me-1"></i>Tambah Link</button>
                            </div>
                        </div>
                        <ul class="list-group" id="daftarLinkPendukung">
                            <!-- Daftar link akan muncul di sini -->
                        </ul>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer modal-footer-sticky">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Preview Media (Foto/Video) -->
<div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-labelledby="mediaPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mediaPreviewModalLabel">Preview Media</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" id="mediaPreviewModalBody">
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
</style>
@endpush

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
@endpush

@push('plugin-scripts')
  <script src="{{ asset('assets/js/inputmask.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
    // Event listener untuk perubahan kategori
    $(document).ready(function() {
      // Inisialisasi awal
      updateSubKategoriOptions($('#kategori').val());

      $('#kategori').on('change', function() {
        const selectedKategoriId = $(this).val();
        updateSubKategoriOptions(selectedKategoriId);
      });
    });
  </script>
  <script src="{{ asset('js/bahanbaku/add-modal.js') }}"></script>
@endpush 

@push('custom-scripts')
  <script>
    // Event listener untuk perubahan kategori
    $(document).ready(function() {
  
      // Inisialisasi awal
       updateSubKategoriOptions($('#kategori_utama').val());
          
      $('#kategori_utama').on('change', function() {
        const selectedKategoriId = $(this).val();
        
        updateSubKategoriOptions(selectedKategoriId);
      });


    });
  </script>
  <!-- {{-- <script src="{{ asset('js/master-produk/add-modal.js') }}"></script> --}} -->
@endpush

@push('custom-scripts')
@include('backend.general-form.cari-pemasok', [
  'modalId' => 'modalCariPemasokBahanBaku',
  'inputId' => 'searchPemasokBahanBaku',
  'tableId' => 'tabelCariPemasokBahanBaku',
  'paginationId' => 'paginationCariPemasokBahanBaku',
  'clearBtnId' => 'clearSearchPemasokBahanBaku',
])
<script>
$(function() {
  $('#btnCariPemasokUtama, #namaPemasokUtama').on('click', function() {
    $('#modalCariPemasokBahanBaku').modal('show');
  });
  window.addEventListener('pemasokDipilih', function(e) {
    if (!$('#modalCariPemasokBahanBaku').hasClass('show')) return;
    const data = e.detail;
    $('#namaPemasokUtama').val(data.nama + (data.kode ? ' ['+data.kode+']' : ''));
    $('#pemasokUtamaId').val(data.id);
    $('#modalCariPemasokBahanBaku').modal('hide');
  });
});
</script>
@endpush