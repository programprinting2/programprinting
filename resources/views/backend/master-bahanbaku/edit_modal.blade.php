<!-- Modal Edit Bahan Baku -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
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
              <button class="nav-link" id="edit-konversi-satuan-tab" data-bs-toggle="tab" data-bs-target="#edit-konversi-satuan" type="button" role="tab" aria-controls="edit-konversi-satuan" aria-selected="false"><i data-feather="refresh-cw" class="me-1 icon-sm"></i> Konversi Satuan</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-pemasok-harga-tab" data-bs-toggle="tab" data-bs-target="#edit-pemasok-harga" type="button" role="tab" aria-controls="edit-pemasok-harga" aria-selected="false"><i data-feather="truck" class="me-1 icon-sm"></i> Pemasok & Harga</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-informasi-stok-tab" data-bs-toggle="tab" data-bs-target="#edit-informasi-stok" type="button" role="tab" aria-controls="edit-informasi-stok" aria-selected="false"><i data-feather="box" class="me-1 icon-sm"></i> Informasi Stok</button>
            </li>
            <!-- <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-media-dokumen-tab" data-bs-toggle="tab" data-bs-target="#edit-media-dokumen" type="button" role="tab" aria-controls="edit-media-dokumen" aria-selected="false"><i data-feather="file-text" class="me-1 icon-sm"></i> Media & Dokumen</button>
            </li> -->
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
                      <select class="form-select" id="edit_kategori" name="kategori" required>
                        <option value="" selected disabled>Pilih kategori</option>
                        <option value="Bahan Lembaran">Bahan Lembaran</option>
                        <option value="Bahan Roll">Bahan Roll</option>
                        <option value="Bahan Cair">Bahan Cair</option>
                        <option value="Bahan Berat">Bahan Berat</option>
                        <option value="Bahan Unit/Biji">Bahan Unit/Biji</option>
                        <option value="Bahan Paket/Set">Bahan Paket/Set</option>
                        <option value="Bahan Waktu/Jasa">Bahan Waktu/Jasa</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_sub_kategori" class="form-label">Sub Kategori</label>
                      <input type="text" class="form-control" id="edit_sub_kategori" name="sub_kategori">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_status_aktif" name="status_aktif" checked>
                        <label class="form-check-label" for="edit_status_aktif">
                          Status Aktif
                          <p class="text-muted mb-0" style="font-size: 0.85rem;">Bahan baku ini aktif dan dapat digunakan dalam proses produksi</p>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="edit_keterangan" class="form-label">Keterangan</label>
                      <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Spesifikasi Teknis Tab -->
            <div class="tab-pane fade" id="edit-spesifikasi-teknis" role="tabpanel" aria-labelledby="edit-spesifikasi-teknis-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_satuan_utama" class="form-label">Satuan Utama <span class="text-danger">*</span></label>
                      <select class="form-select" id="edit_satuan_utama" name="satuan_utama" required disabled>
                        <option value="">Pilih kategori terlebih dahulu</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_pilihan_warna" class="form-label">Pilihan Warna</label>
                      <select class="form-select" id="edit_pilihan_warna" name="pilihan_warna">
                        <option value="" selected disabled>Pilih warna</option>
                        <option value="merah">Merah</option>
                        <option value="biru">Biru</option>
                        <option value="hijau">Hijau</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_nama_warna_custom" class="form-label">Nama Warna Custom</label>
                      <input type="text" class="form-control" id="edit_nama_warna_custom" name="nama_warna_custom">
                    </div>
                    <div class="col-md-6">
                      <label for="edit_berat" class="form-label">Berat (gram)</label>
                      <input type="number" class="form-control" id="edit_berat" name="berat" min="0">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_tinggi" class="form-label">Tinggi (cm)</label>
                      <input type="number" class="form-control" id="edit_tinggi" name="tinggi" min="0">
                    </div>
                    <div class="col-md-6">
                      <label for="edit_tebal" class="form-label">Tebal (mm)</label>
                      <input type="number" class="form-control" id="edit_tebal" name="tebal" min="0">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_gramasi_densitas" class="form-label">Gramasi/Densitas</label>
                      <input type="number" class="form-control" id="edit_gramasi_densitas" name="gramasi_densitas" min="0">
                    </div>
                    <div class="col-md-6">
                      <label for="edit_volume" class="form-label">Volume (liter)</label>
                      <input type="number" class="form-control" id="edit_volume" name="volume" min="0">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Konversi Satuan Tab -->
            <div class="tab-pane fade" id="edit-konversi-satuan" role="tabpanel" aria-labelledby="edit-konversi-satuan-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Konversi Satuan</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="editTambahKonversi"><i data-feather="plus" class="me-1 icon-sm"></i>Tambah Konversi</button>
                  </div>
                  <div id="editConversionUnitsContainer">
                    <!-- Dynamic conversion rows will be added here -->
                  </div>
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

            <!-- Pemasok & Harga Tab -->
            <div class="tab-pane fade" id="edit-pemasok-harga" role="tabpanel" aria-labelledby="edit-pemasok-harga-tab">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_pemasok_utama_id" class="form-label">Pemasok Utama</label>
                      <select class="form-select" id="edit_pemasok_utama_id" name="pemasok_utama_id">
                        <option value="">Pilih pemasok</option>
                        @foreach($pemasok as $supplier)
                          <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Harga Terakhir</label>
                      <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control money-format" id="edit_harga_terakhir" name="harga_terakhir" value="0">
                      </div>
                    </div>
                  </div>
                  <!-- <div class="row mb-3">
                    <div class="col-md-12">
                      <label class="form-label">Histori Harga</label>
                      <div id="editHistoriHargaContainer">
                        <!-- Histori harga will be displayed here -->
                      <!-- </div> -->
                    <!-- </div> -->
                  <!-- </div> -->
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
                            <input type="number" class="form-control" id="edit_stok_saat_ini" name="stok_saat_ini" min="0">
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
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="edit_foto_produk" class="form-label">Foto Produk</label>
                      <input type="file" class="form-control" id="edit_foto_produk" name="foto_produk" accept="image/*">
                      <div id="editPreviewFoto" class="mt-2"></div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label class="form-label">Dokumen Pendukung</label>
                      <div id="editDokumenContainer">
                        <!-- Dokumen pendukung will be displayed here -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('custom-scripts')
  <script>
    // Inisialisasi data sub-kategori
    initSubKategoriData(@json($subKategoriParameters ?? []));

    // Event listener untuk perubahan kategori di modal edit
    $(document).ready(function() {
      // Inisialisasi awal
      updateEditSubKategoriOptions($('#edit_kategori').val(), $('#edit_sub_kategori').val());

      $('#edit_kategori').on('change', function() {
        const selectedKategori = $(this).val();
        updateEditSubKategoriOptions(selectedKategori);
      });

      // Panggil saat modal edit ditampilkan (untuk mengupdate sub-kategori berdasarkan data yang dimuat)
      $('#editModal').on('shown.bs.modal', function () {
        // Panggil ini lagi untuk memastikan sub-kategori dimuat setelah kategori diset oleh loadBahanBakuData
        const selectedKategoriOnShow = $('#edit_kategori').val();
        const currentSubKategoriOnShow = $('#edit_sub_kategori').val();
        updateEditSubKategoriOptions(selectedKategoriOnShow, currentSubKategoriOnShow);
      });
    });
  </script>
  <script src="{{ asset('js/bahanbaku/edit-modal.js') }}"></script>
@endpush 