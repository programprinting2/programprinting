@extends('layout.master')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold">Daftar Produk</h2>
            <div class="text-muted">Kelola produk dan layanan yang Anda tawarkan</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            + Tambah Produk
        </button>
    </div>
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Cari produk berdasarkan nama, kode, atau kategori...">
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Kode agus</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($produk as $item)
                <tr>
                    <td>{{ $item['kode'] }}</td>
                    <td>
                        <div class="fw-bold">{{ $item['nama'] }}</div>
                        <div class="text-muted small">{{ $item['deskripsi'] }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $item['kategori'] }}</span></td>
                    <td>Rp {{ number_format($item['harga'],0,',','.') }}</td>
                    <td><span class="text-success">{{ $item['stok'] }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-light"><i class="bi bi-three-dots-vertical"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalTambahProdukLabel">Tambah Produk Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3" id="tabProduk" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-dasar" data-bs-toggle="tab" data-bs-target="#dasar" type="button" role="tab">Informasi Dasar</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-detail" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Detail Produk</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-cetak" data-bs-toggle="tab" data-bs-target="#cetak" type="button" role="tab">Detail Cetak</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-inventory" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">Inventory & Harga</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-alur" data-bs-toggle="tab" data-bs-target="#alur" type="button" role="tab">Alur Produksi</button>
          </li>
        </ul>
        <div class="tab-content" id="tabProdukContent">
          <!-- Tab 1: Informasi Dasar -->
          <div class="tab-pane fade show active" id="dasar" role="tabpanel">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Nama Produk</label>
                <input type="text" class="form-control" placeholder="Nama Produk">
              </div>
              <div class="col-md-6">
                <label class="form-label">Kode Produk</label>
                <input type="text" class="form-control" placeholder="Kode Produk">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control" rows="2" placeholder="Deskripsi produk"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Kategori</label>
              <input type="text" class="form-control" placeholder="Kategori">
            </div>
          </div>
          <!-- Tab 2: Detail Produk -->
          <div class="tab-pane fade" id="detail" role="tabpanel">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Dimensi Produk</label>
                <input type="text" class="form-control" placeholder="Dimensi Produk">
              </div>
              <div class="col-md-6">
                <label class="form-label">Berat Kertas (gsm)</label>
                <input type="number" class="form-control" placeholder="80">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Jenis Kertas</label>
                <input type="text" class="form-control" placeholder="Jenis Kertas">
              </div>
              <div class="col-md-6">
                <label class="form-label">Sisi Cetak</label>
                <select class="form-select">
                  <option>Satu Sisi</option>
                  <option>Dua Sisi</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Tab 3: Detail Cetak -->
          <div class="tab-pane fade" id="cetak" role="tabpanel">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Jenis Cetak</label>
                <input type="text" class="form-control" placeholder="Jenis Cetak">
              </div>
              <div class="col-md-6">
                <label class="form-label">Jenis Warna</label>
                <select class="form-select">
                  <option>Full Color</option>
                  <option>Hitam Putih</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Spesifikasi Warna</label>
              <input type="text" class="form-control" placeholder="cmyk">
            </div>
            <div class="mb-3">
              <label class="form-label">Opsi Finishing</label>
              <input type="text" class="form-control" placeholder="Pilih opsi finishing">
            </div>
            <div class="mb-3">
              <label class="form-label">Estimasi Waktu Produksi (Jam)</label>
              <input type="number" class="form-control" placeholder="24">
            </div>
          </div>
          <!-- Tab 4: Inventory & Harga -->
          <div class="tab-pane fade" id="inventory" role="tabpanel">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Harga Jual</label>
                <input type="number" class="form-control" placeholder="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Harga Modal</label>
                <input type="number" class="form-control" placeholder="0">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Satuan</label>
                <input type="text" class="form-control" placeholder="lembar">
              </div>
              <div class="col-md-6">
                <label class="form-label">Minimum Order</label>
                <input type="number" class="form-control" placeholder="1">
              </div>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="produkTersedia" checked>
              <label class="form-check-label" for="produkTersedia">Produk Tersedia</label>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Jumlah Stok</label>
                <input type="number" class="form-control" placeholder="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Level Stok Minimum</label>
                <input type="number" class="form-control" placeholder="10">
              </div>
            </div>
          </div>
          <!-- Tab 5: Alur Produksi -->
          <div class="tab-pane fade" id="alur" role="tabpanel">
            <div class="mb-3">
              <label class="form-label">Langkah 1</label>
              <div class="row mb-2">
                <div class="col-md-4">
                  <select class="form-select">
                    <option>Roland XR-640 Printer</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <input type="number" class="form-control" placeholder="Waktu (menit)" value="30">
                </div>
                <div class="col-md-2">
                  <input type="number" class="form-control" placeholder="Urutan" value="1">
                </div>
                <div class="col-md-4">
                  <input type="text" class="form-control" placeholder="Jelaskan proses yang dilakukan pada langkah ini">
                </div>
              </div>
              <button class="btn btn-outline-secondary btn-sm">+ Tambah Langkah Produksi Baru</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary">Tambah Produk</button>
      </div>
    </div>
  </div>
</div>
@endsection 