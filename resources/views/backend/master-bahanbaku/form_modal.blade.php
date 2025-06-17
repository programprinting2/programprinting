<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
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
            <!-- <li class="nav-item" role="presentation">
              <button class="nav-link" id="media-dokumen-tab" data-bs-toggle="tab" data-bs-target="#media-dokumen" type="button" role="tab" aria-controls="media-dokumen" aria-selected="false"><i data-feather="file-text" class="me-1 icon-sm"></i> Media & Dokumen</button>
            </li> -->
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
                    <div class="col-md-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="statusAktif" name="status_aktif" checked>
                        <label class="form-check-label" for="statusAktif">
                            Status Aktif
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Bahan baku ini aktif dan dapat digunakan dalam proses produksi</p>
                        </label>
                      </div>
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
                      <label for="pilihanWarna" class="form-label">Pilihan Warna</label>
                      <select class="form-select" id="pilihanWarna" name="pilihan_warna">
                        <option value="" selected disabled>Pilih warna</option>
                        <option value="merah">Merah</option>
                        <option value="biru">Biru</option>
                        <option value="hijau">Hijau</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="namaWarnaCustom" class="form-label">Nama Warna Custom</label>
                      <input type="text" class="form-control" id="namaWarnaCustom" name="nama_warna_custom" placeholder="Contoh: Merah Marun, Biru Navy, dll">
                      <small class="text-muted">Nama warna spesifik jika berbeda dari pilihan standar</small>
                    </div>
                    <div class="col-md-6">
                      <label for="beratGram" class="form-label">Berat (gram)</label>
                      <input type="number" class="form-control" id="beratGram" name="berat" value="0" min="0">
                      <small class="text-muted">Berat per unit satuan utama</small>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="tinggiCm" class="form-label">Tinggi (cm)</label>
                      <input type="number" class="form-control" id="tinggiCm" name="tinggi" value="0" min="0">
                    </div>
                    <div class="col-md-6">
                      <label for="tebalMm" class="form-label">Tebal (mm)</label>
                      <input type="number" class="form-control" id="tebalMm" name="tebal" value="0" min="0">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="gramasiDensitas" class="form-label">Gramasi/Densitas</label>
                      <input type="number" class="form-control" id="gramasiDensitas" name="gramasi_densitas" value="0" min="0">
                      <small class="text-muted">g/m² untuk kertas, g/cm³ untuk lainnya</small>
                    </div>
                    <div class="col-md-6">
                      <label for="volumeLiter" class="form-label">Volume (liter)</label>
                      <input type="number" class="form-control" id="volumeLiter" name="volume" value="0" min="0">
                      <small class="text-muted">Untuk bahan cair/tinta</small>
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
                    <h6 class="mb-0">Konversi Satuan</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="tambahKonversi"><i data-feather="plus" class="me-1 icon-sm"></i>Tambah Konversi</button>
                  </div>
                  <p class="text-muted mb-3" style="font-size: 0.85rem;">Konversi satuan membantu perhitungan otomatis antara satuan yang berbeda. Contoh: 1 Rim = 500 Lembar, 1 Roll = 50 Meter</p>
                  <div id="conversionUnitsContainer">
                    <!-- Dynamic conversion rows will be added here -->
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
                            <input type="number" class="form-control" id="stokMinimum" name="stok_minimum" value="0" min="0">
                            <span class="input-group-text" id="stokMinimumUnit">Unit</span>
                          </div>
                          <small class="text-muted">Jumlah stok minimum sebelum perlu re-order</small>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <label for="stokMaksimum" class="form-label">Stok Maksimum</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="stokMaksimum" name="stok_maksimum" value="0" min="0">
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
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="fotoProduk" class="form-label">Foto Produk</label>
                      <input type="file" class="form-control" id="fotoProduk" name="foto_produk_url" accept="image/*">
                      <small class="text-muted">Upload gambar produk (JPG, PNG, maksimal 2MB)</small>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="dokumenPendukung" class="form-label">Dokumen Pendukung</label>
                      <input type="file" class="form-control" id="dokumenPendukung" name="dokumen_pendukung_json" accept=".pdf,.doc,.docx,.xls,.xlsx">
                      <small class="text-muted">Upload dokumen terkait (PDF, Word, Excel, maksimal 5MB)</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer modal-footer-sticky">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Bahan Baku</button>
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