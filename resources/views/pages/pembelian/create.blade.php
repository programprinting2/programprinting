@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Pembelian</li>
    </ol>
  </nav>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
  @endif
  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  <h4 class="fw-bold">Buat Pembelian Baru</h4>
  <p class="text-muted mb-4">Buat transaksi pembelian bahan baku baru.</p>

  <form id="addForm" action="{{ route('pembelian.store') }}" method="POST">
    @csrf
    <div class="row">

    {{-- Konten kiri: item pembelian --}}
    <div class="col-md-9">
      <div class="card border-0 shadow-none">
      <div class="card-body p-0">
        <ul class="nav nav-tabs mb-3" id="tabPembelian" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="item-tab" data-bs-toggle="tab" data-bs-target="#itemPembelian"
          type="button" role="tab">Item Pembelian</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="biaya-tab" data-bs-toggle="tab" data-bs-target="#biayaTambahan" type="button"
          role="tab">Biaya Tambahan</button>
        </li>
        </ul>
        <div class="tab-content" id="tabPembelianContent">
        <div class="tab-pane fade show active" id="itemPembelian" role="tabpanel">
          <div class="card mb-3 border-primary">
            <div class="card-body">
              <div class="mb-2">
                <label class="form-label mb-0">Tambah Bahan baku ke Daftar Pembelian</label>
                <small class="text-muted d-block mb-2">Diskon di bawah ini hanya berlaku untuk item/material ini saja. Untuk diskon total pembelian, gunakan tab "Biaya Tambahan".</small>
              </div>
              <div class="row g-2 mb-2 align-items-end">
                <div class="col-md-5">
                  <label class="form-label small mb-1">Bahan Baku</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="namaBahanBakuInput" placeholder="Pilih bahan baku..." readonly>
                    <input type="hidden" id="bahanbakuIdInput">
                    <input type="hidden" id="kodeBahanBakuInput">
                    <button type="button" class="btn btn-outline-secondary" id="btnCariBahanBaku"><i class="fa fa-search"></i></button>
                  </div>
          </div>
                <div class="col-md-2">
                  <label for="jumlahInput" class="form-label small mb-1">Jumlah</label>
                  <input type="number" class="form-control" id="jumlahInput" placeholder="Masukkan jumlah" min="1" value="1">
            </div>
            <div class="col-md-3">
                  <label for="hargaInput" class="form-label small mb-1">Harga Satuan (Rp)</label>
                  <input type="text" class="form-control" id="hargaInput" placeholder="Masukkan harga satuan" value="0">
                </div>
                <div class="col-md-2">
                  <label for="diskonInput" class="form-label small mb-1">Diskon per Item (%)</label>
                  <input type="number" class="form-control" id="diskonInput" min="0" max="100" value="0">
                </div>
            </div>
              <button type="button" class="btn btn-outline-primary" id="btnTambahItem"><i class="fa fa-plus"></i> Tambah Item</button>
            </div>
          </div>
          <div class="table-responsive">
          <table class="table table-bordered align-middle" id="tabelItemPembelian">
            <thead class="table-light">
            <tr>
              <th>Kode Bahan</th>
              <th>Material</th>
              <th>Jumlah</th>
              <th>Harga</th>
              <th>Diskon (%)</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
            </thead>
            <tbody id="itemBody">
            <tr>
              <td colspan="7" class="text-center text-muted">Belum ada item yang ditambahkan</td>
            </tr>
            </tbody>
          </table>
          </div>
        </div>
        <div class="tab-pane fade" id="biayaTambahan" role="tabpanel">
          <div class="row g-3 p-3">
          <div class="col-md-4">
            <label class="form-label">Diskon (%)</label>
            <input type="number" class="form-control" value="0" name="diskon_persen" min="0" max="100" step="0.01">
          </div>
          <div class="col-md-4">
            <label class="form-label"> Diskon (Rp)</label>
            <input type="text" class="form-control" value="0" name="jumlah_diskon">
          </div>
          <div class="col-md-4">
            <label class="form-label">Tarif Pajak (%)</label>
            <input type="number" class="form-control" value="0" name="tarif_pajak">
          </div>
          <div class="col-md-4">
            <label class="form-label">Nota Kredit (Rp)</label>
            <input type="text" class="form-control" value="0" name="nota_kredit">
          </div>
          <div class="col-md-4">
            <label class="form-label">Biaya Pengiriman (Rp)</label>
            <input type="text" class="form-control" value="0" name="biaya_pengiriman">
          </div>
          <div class="col-md-4">
            <label class="form-label">Biaya Lain (Rp) <span class="text-muted small">(Opsional)</label>
            <input type="text" class="form-control" value="0" name="biaya_lain">
          </div>
          </div>
          <div class="p-3 border rounded bg-light mt-3">
          <div class="fw-bold mb-2">Ringkasan Biaya</div>
          <div class="d-flex justify-content-between small mb-1"><span>Subtotal:</span><span
            class="ringkasan-subtotal">Rp 0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Diskon:</span><span
            class="ringkasan-diskon">- Rp 0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Biaya Pengiriman:</span><span
            class="ringkasan-pengiriman">Rp 0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Biaya Lain:</span><span class="ringkasan-biayalain">Rp 0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Nota Kredit:</span><span class="ringkasan-notakredit">- Rp 0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Pajak (%):</span><span
            class="ringkasan-pajak">Rp 0</span></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span class="ringkasan-total">Rp
            0</span></div>
          </div>
        </div>
        </div>
      </div>
      </div>
      <div class="d-flex justify-content-end gap-2 mt-4">
      <button type="button" class="btn btn-light" id="btnBatalPembelian">Batal</button>
      <button type="submit" class="btn btn-primary" id="btnSimpanPembelian">
        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerSimpanPembelian" role="status" aria-hidden="true"></span>
        <span class="label-simpan">Simpan Pembelian</span>
      </button>
      </div>
    </div>

    {{-- Sidebar kanan: informasi pemasok --}}
    <div class="col-md-3">
      <div class="mb-4 p-3 border rounded bg-light">
      <div class="fw-semibold mb-2"><i class="fa fa-user me-1"></i> Informasi Pemasok</div>
      <div class="mb-3">
        <label class="form-label">Pemasok</label>
        <div class="input-group">
          <input type="text" class="form-control" id="namaPemasokInput" placeholder="Pilih pemasok..." readonly style="background:#fff;cursor:pointer;">
          <input type="hidden" id="pemasokIdInput" name="pemasok_id">
          <input type="hidden" id="kodePemasokInput">
          <button class="btn btn-outline-secondary" type="button" id="btnCariPemasok"><i class="fa fa-search"></i></button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Pembelian</label>
        <input type="date" class="form-control" name="tanggal_pembelian" id="tanggalPembelian" value="{{ date('Y-m-d') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Jatuh Tempo (Hari)</label>
        <input type="number" class="form-control" id="jatuhTempoHari" name="jatuh_tempo_hari" min="1" placeholder="Isi jumlah hari jatuh tempo">
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Jatuh Tempo <span class="text-muted small">(Opsional)</span></label>
        <input type="date" class="form-control" name="jatuh_tempo" id="tanggalJatuhTempo">
      </div>

      <div class="mb-3">
        <label class="form-label">Gunakan Nomor Form <span class="text-muted small">(Opsional)</span></label>
        <input type="text" class="form-control" id="nomorFormInput" name="nomor_form" placeholder="Nomor form">
      </div>
      </div>
      <div class="mb-4 p-3 border rounded bg-light">
      <div class="fw-semibold mb-2"><i class="fa fa-sticky-note me-1"></i> Catatan</div>
      <textarea class="form-control" name="catatan" rows="3"
        placeholder="Tambahkan catatan untuk pembelian ini"></textarea>
      </div>
      <div class="p-3 border rounded bg-light">
      <div class="fw-semibold mb-2"><i class="fa fa-dollar-sign me-1"></i> Ringkasan Biaya</div>
      <div class="d-flex justify-content-between small mb-1"><span>Subtotal:</span><span class="ringkasan-subtotal">Rp
        0</span></div>
      <div class="d-flex justify-content-between small mb-1"><span>Diskon:</span><span class="ringkasan-diskon">- Rp
        0</span></div>
      <div class="d-flex justify-content-between small mb-1"><span>Biaya Pengiriman:</span><span
        class="ringkasan-pengiriman">Rp 0</span></div>
      <div class="d-flex justify-content-between small mb-1"><span>Biaya Lain:</span><span class="ringkasan-biayalain">Rp 0</span></div>
      <div class="d-flex justify-content-between small mb-1"><span>Nota Kredit:</span><span class="ringkasan-notakredit">- Rp 0</span></div>
      <div class="d-flex justify-content-between small mb-1"><span>Pajak (%):</span><span class="ringkasan-pajak">Rp
        0</span></div>
      <hr class="my-2">
      <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span class="ringkasan-total">Rp 0</span>
      </div>
      </div>
    </div>

    </div>

    <!-- <div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('pembelian.index') }}" class="btn btn-light">Batal</a>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-save me-1"></i> Simpan Pembelian
    </button>
    </div> -->
  </form>

  <style>
    /* Style untuk input field yang sedang difokuskan */
    .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
  </style>
@endsection

@push('custom-scripts')
@include('backend.general-form.cari-bahanbaku', [
  'modalId' => 'modalCariBahanBakuPembelian',
  'inputId' => 'searchBahanBakuPembelian',
  'tableId' => 'tabelCariBahanBakuPembelian',
  'paginationId' => 'paginationBahanBakuPembelian',
  'clearBtnId' => 'clearSearchBahanBakuPembelian',
])
@include('backend.general-form.cari-pemasok', [
  'modalId' => 'modalCariPemasokPembelian',
  'inputId' => 'searchPemasokPembelian',
  'tableId' => 'tabelCariPemasokPembelian',
  'paginationId' => 'paginationCariPemasokPembelian',
  'clearBtnId' => 'clearSearchPemasokPembelian',
])
<script src="/js/pembelian/pembelian-helper.js"></script>
<script src="/js/pembelian/form-create.js"></script>
  <script>
  document.getElementById('btnBatalPembelian').addEventListener('click', function() {
    window.history.back();
    });
  </script>
@endpush