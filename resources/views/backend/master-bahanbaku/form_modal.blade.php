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
              <button class="nav-link" id="konversi-satuan-tab" data-bs-toggle="tab" data-bs-target="#konversi-satuan" type="button" role="tab" aria-controls="konversi-satuan" aria-selected="false"><i data-feather="refresh-cw" class="me-1 icon-sm"></i> Konversi Satuan</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pemasok-harga-tab" data-bs-toggle="tab" data-bs-target="#pemasok-harga" type="button" role="tab" aria-controls="pemasok-harga" aria-selected="false"><i data-feather="truck" class="me-1 icon-sm"></i> Pemasok & Harga</button>
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
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="namaBahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="namaBahan" name="nama_bahan" placeholder="Nama bahan baku" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                      <select class="form-select" id="kategori" name="kategori" required>
                        <option value="" selected disabled>Pilih kategori</option>
                        <option value="Bahan Lembaran">Bahan Lembaran</option>
                        <option value="Bahan Roll">Bahan Roll</option>
                        <option value="Bahan Cair">Bahan Cair</option>
                        <option value="Bahan Berat">Bahan Berat</option>
                        <option value="Bahan Unit/Biji">Bahan Unit/Biji</option>
                        <option value="Bahan Paket/Set">Bahan Paket/Set</option>
                        <option value="Bahan Waktu/Jasa">Bahan Waktu/Jasa</option>
                      </select>
                      <small class="text-muted">Otomatis menyesuaikan metode perhitungan sesuai kategori</small>
                    </div>
                    <div class="col-md-6">
                      <label for="subKategori" class="form-label">Sub-Kategori</label>
                      <select class="form-select" id="subKategori" name="sub_kategori">
                        <option value="" selected disabled>Pilih sub-kategori</option>
                        <!-- Options will be dynamically loaded based on category -->
                      </select>
                      <small class="text-muted">Pengelompokan lebih detail dalam kategori yang sama</small>
                    </div>
                  </div>
                    
                  <div class="row mb-3">
                  <div class="col-md-6">
                      <label for="satuanUtama" class="form-label">Satuan Utama <span class="text-danger">*</span></label>
                      <select class="form-select" id="satuanUtama" name="satuan_utama" required>
                        <option value="" selected disabled>Pilih satuan</option>
                        <option value="lembar">Lembar</option>
                        <option value="roll">Roll</option>
                        <option value="kg">Kg</option>
                        <option value="liter">Liter</option>
                        <option value="pcs">Pcs</option>
                        <option value="set">Set</option>
                        <option value="meter">Meter</option>
                        <option value="menit">Menit</option>
                      </select>
                      <small class="text-muted">Satuan utama untuk perhitungan stok</small>
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
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h6 class="mb-0">Konversi Satuan</h6>
                      <p class="text-muted mb-3" style="font-size: 0.85rem;">Konversi satuan membantu perhitungan otomatis antara satuan yang berbeda. Contoh: 1 Rim = 500 Lembar, 1 Roll = 50 Meter</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="tambahKonversi"><i data-feather="plus" class="me-1 icon-sm"></i>Tambah Konversi</button>
                  </div>
                  
                  <div id="conversionUnitsContainer">
                    <!-- Dynamic conversion rows will be added here -->
                    <div class="col-12 text-center text-muted py-4" id="noConversionMessage">
                        <i data-feather="refresh-cw" class="icon-lg mb-2"></i><br>Belum ada konversi satuan yang ditambahkan.
                    </div>
                  </div>
                  <!-- New: Contoh Konversi Satuan -->
                  <div class="alert alert-info mt-3" role="alert">
                    <h6 class="alert-heading mb-2">Contoh Konversi Satuan</h6>
                    <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
                      <li>1 Rim = 500 Lembar</li>
                      <li>1 Roll = 50 Meter</li>
                      <li>1 Karton = 20 Pack</li>
                      <li>1 Box = 100 Pcs</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pemasok & Harga Tab -->
            <div class="tab-pane fade" id="pemasok-harga" role="tabpanel" aria-labelledby="pemasok-harga-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="pemasokUtama" class="form-label">Pemasok Utama</label>
                      <div class="d-flex align-items-center">
                        <select class="form-select me-2" id="pemasokUtama" name="pemasok_utama_id">
                          <option value="" selected disabled>Pilih pemasok</option>
                          @foreach($pemasok as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                          @endforeach
                        </select>
                      </div>
                      <small class="text-muted">Pemasok utama untuk pembelian bahan baku</small>
                    </div>
                    <div class="col-md-6">
                      <label for="hargaTerakhir" class="form-label">Harga Terakhir</label>
                      <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control money-format" id="hargaTerakhir" name="harga_terakhir" value="0">
                      </div>
                      <small class="text-muted">Harga beli terakhir dari pemasok utama</small>
                    </div>
                  </div>
                  <!-- <div class="row mb-3">
                    <div class="col-md-12">
                      <label class="form-label">Histori Harga</label>
                      <p class="text-muted mb-3" style="font-size: 0.85rem;">Catatan perubahan harga dari waktu ke waktu</p>
                      <div class="table-responsive">
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Tanggal</th>
                              <th>Harga</th>
                              <th>Pemasok</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>2024-05-01</td>
                              <td>Rp 15.000</td>
                              <td>PT Kertas Nusantara</td>
                            </tr>
                            <tr>
                              <td>2024-04-15</td>
                              <td>Rp 14.500</td>
                              <td>PT Kertas Nusantara</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div> -->
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
                            <input type="number" class="form-control" id="stokSaatIni" name="stok_saat_ini" value="0" min="0">
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
                            <div class="col-md-10">
                                <input type="url" class="form-control" id="inputLinkPendukung" placeholder="https://contoh.com/link-pendukung">
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

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
    // Inisialisasi data sub-kategori
    initSubKategoriData(@json($subKategoriParameters ?? []));

    // Event listener untuk perubahan kategori
    $(document).ready(function() {
      // Inisialisasi awal
      updateSubKategoriOptions($('#kategori').val());

      $('#kategori').on('change', function() {
        const selectedKategori = $(this).val();
        updateSubKategoriOptions(selectedKategori);
      });
    });
  </script>
  <script src="{{ asset('js/bahanbaku/add-modal.js') }}"></script>
@endpush 