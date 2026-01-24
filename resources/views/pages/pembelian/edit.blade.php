@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Pembelian</li>
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

  <h4 class="fw-bold">Edit Pembelian</h4>
  <p class="text-muted mb-4">Edit transaksi pembelian bahan baku.</p>

  <form id="editForm" action="{{ route('pembelian.update', $pembelian->kode_pembelian) }}" method="POST">
    @csrf
    @method('PUT')
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
                <div class="col-md-4">
                  <label class="form-label small mb-1">Bahan Baku</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="namaBahanBakuInput" placeholder="Pilih bahan baku..." readonly>
                    <input type="hidden" id="bahanbakuIdInput">
                    <input type="hidden" id="kodeBahanBakuInput">
                    <button type="button" class="btn btn-outline-secondary" id="btnCariBahanBaku"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                <div class="col-md-2">
                  <label for="jumlahInput" class="form-label small mb-1 d-block text-end">Jumlah</label>
                  <div class="input-group">
                    <input type="number" class="form-control text-end" id="jumlahInput" placeholder="Masukkan jumlah" min="1" value="1">
                    <select class="form-select" id="satuanInput" style="min-width:90px" disabled></select>
                  </div>
                </div>
                <div class="col-md-2">
                  <label for="hargaInput" class="form-label small mb-1 d-block text-end">Harga Satuan (Rp)</label>
                  <input type="text" class="form-control text-end" id="hargaInput" placeholder="Masukkan harga satuan" value="0">
                </div>
                <div class="col-md-2">
                  <label for="diskonInput" class="form-label small mb-1 d-block text-end">Diskon per Item (%)</label>
                  <input type="number" class="form-control text-end" id="diskonInput" min="0" max="100" value="0">
                </div>
                <div class="col-md-2 text-end">
                  <span class="fw-semibold">Total : </span>
                  <input type="text" id="previewTotalItem" class="form-control text-primary fw-bold text-end" value="Rp 0" readonly tabindex="-1" style="box-shadow:none;pointer-events:none;" />
                </div>
              </div>
              <div class="mb-2" id="konversiSatuanInfo" style="display:none"></div>
              
              <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-primary" id="btnTambahItem"><i class="fa fa-plus"></i> Tambah Item</button>
                <div class="text-center">
                    <!-- <label class="form-label small mb-1">Warna</label> -->
                    <div id="warnaPreview" class="warna-preview-box mx-auto"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
          <table class="table table-bordered align-middle" id="tabelItemPembelian">
            <thead class="table-light">
            <tr>
              <th>Kode Bahan</th>
              <th>Material</th>
              <th>Jumlah</th>
              <th>Satuan</th>
              <th>Harga Satuan (Rp)</th>
              <th>Diskon (%)</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
            </thead>
            <tbody id="itemBody">
            @forelse($pembelian->items as $index => $item)
              <tr>
                <td>{{ $item->bahanBaku->kode_bahan ?? '-' }}<input type="hidden" name="items[{{ $index }}][bahanbaku_id]" value="{{ $item->bahanbaku_id }}"></td>
                <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
                <td class="item-jumlah">{{ $item->jumlah }}<input type="hidden" name="items[{{ $index }}][jumlah]" value="{{ $item->jumlah }}"></td>
                <td class="item-satuan satuan-label-prefill" data-satuan-id="{{ $item->satuan ?? '' }}">{{ $item->satuan ?? '-' }}<input type="hidden" name="items[{{ $index }}][satuan]" value="{{ $item->satuan }}"></td>
                <td class="item-harga text-end">{{ number_format($item->harga, 0, ',', '.') }}<input type="hidden" name="items[{{ $index }}][harga]" value="{{ $item->harga }}"></td>
                <td class="item-diskon text-end">{{ $item->diskon_persen }}%<input type="hidden" name="items[{{ $index }}][diskon_persen]" value="{{ $item->diskon_persen }}"></td>
                <td class="item-total text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
                  <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted">Belum ada item yang ditambahkan</td>
              </tr>
            @endforelse
            </tbody>
          </table>
          </div>
        </div>
        <div class="tab-pane fade" id="biayaTambahan" role="tabpanel">
          <div class="row g-3 p-3">
          <div class="col-md-4">
            <label class="form-label d-block text-end">Diskon (%)</label>
            <input type="number" class="form-control text-end" value="{{ $pembelian->diskon_persen ?? 0 }}" name="diskon_persen" min="0" max="100" step="0.01">
          </div>
          <div class="col-md-4">
            <label class="form-label d-block text-end"> Diskon (Rp)</label>
            <input type="text" class="form-control text-end" value="0" name="jumlah_diskon">
          </div>
          <div class="col-md-4">
            <label class="form-label d-block text-end">Tarif Pajak (%)</label>
            <input type="number" class="form-control text-end" value="{{ $pembelian->tarif_pajak ?? 0 }}" name="tarif_pajak">
          </div>
          <div class="col-md-4">
            <label class="form-label d-block text-end">Nota Kredit (Rp)</label>
            <input type="text" class="form-control text-end" value="{{ number_format($pembelian->nota_kredit ?? 0, 0, ',', '.') }}" name="nota_kredit">
          </div>
          <div class="col-md-4">
            <label class="form-label d-block text-end">Biaya Pengiriman (Rp)</label>
            <input type="text" class="form-control text-end" value="{{ number_format($pembelian->biaya_pengiriman ?? 0, 0, ',', '.') }}" name="biaya_pengiriman">
          </div>
          <div class="col-md-4">
            <label class="form-label d-block text-end">Biaya Lain (Rp) <span class="text-muted small">(Opsional)</label>
            <input type="text" class="form-control text-end" value="{{ number_format($pembelian->biaya_lain ?? 0, 0, ',', '.') }}" name="biaya_lain">
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
      <button type="submit" class="btn btn-primary" id="btnUpdatePembelian">
        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerUpdatePembelian" role="status" aria-hidden="true"></span>
        <span class="label-update">Update Pembelian</span>
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
          <input type="text" class="form-control" id="namaPemasokInput" placeholder="Pilih pemasok..." readonly style="background:#fff;cursor:pointer;" value="{{ $pembelian->pemasok->nama ?? '' }} [{{ $pembelian->pemasok->kode_pemasok ?? '' }}]">
          <input type="hidden" id="pemasokIdInput" name="pemasok_id" value="{{ $pembelian->pemasok_id }}">
          <input type="hidden" id="kodePemasokInput" value="{{ $pembelian->pemasok->kode_pemasok ?? '' }}">
          <button class="btn btn-outline-secondary" type="button" id="btnCariPemasok"><i class="fa fa-search"></i></button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Pembelian</label>
        <input type="date" class="form-control" name="tanggal_pembelian" id="tanggalPembelian" value="{{ $pembelian->tanggal_pembelian }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Jatuh Tempo (Hari)</label>
        <input type="number" class="form-control" id="jatuhTempoHari" name="jatuh_tempo_hari" min="1" placeholder="Isi jumlah hari jatuh tempo">
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Jatuh Tempo <span class="text-muted small">(Opsional)</span></label>
        <input type="date" class="form-control" name="jatuh_tempo" id="tanggalJatuhTempo" value="{{ $pembelian->jatuh_tempo }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Gunakan Nomor Form <span class="text-muted small">(Opsional)</span></label>
        <input type="text" class="form-control" id="nomorFormInput" name="nomor_form" placeholder="Nomor form" value="{{ $pembelian->nomor_form }}">
      </div>
      </div>
      <div class="mb-4 p-3 border rounded bg-light">
      <div class="fw-semibold mb-2"><i class="fa fa-sticky-note me-1"></i> Catatan</div>
      <textarea class="form-control" name="catatan" rows="3"
        placeholder="Tambahkan catatan untuk pembelian ini">{{ $pembelian->catatan }}</textarea>
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
  </form>

  <style>
    /* Style untuk input field yang sedang difokuskan */
    .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .warna-preview-box {
      width: 40px;
      height: 40px;
      border: 2px solid #ced4da;
      border-radius: 4px;
      background-color: #f8f9fa;
    }

    .warna-preview-box:hover {
        border-color: #6c757d;
        transform: scale(1.1);
        transition: all 0.2s ease;
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
<script src="/js/pembelian/form-edit.js"></script>
<script>
  window.satuanList = @json($satuanList);
  window.bahanBakuList = @json($bahan_baku);
  
  document.getElementById('btnBatalPembelian').addEventListener('click', function() {
    window.history.back();
    });
    
  @if(session('success'))
    Swal.fire({
      title: 'Berhasil!',
      text: '{{ session('success') }}',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    });
  @endif

  @if(session('error'))
    Swal.fire({
      title: 'Error!',
      text: '{{ session('error') }}',
      icon: 'error',
      confirmButtonText: 'OK'
    });
  @endif

  // Mapping satuan id ke label pada prefill tabel
  document.addEventListener('DOMContentLoaded', function() {
    const satuanList = window.satuanList || [];
    document.querySelectorAll('.satuan-label-prefill').forEach(function(td) {
      const id = td.getAttribute('data-satuan-id');
      const found = satuanList.find(s => String(s.id) === String(id));
      if (found) {
        td.childNodes[0].nodeValue = found.nama_sub_detail_parameter + ' ';
      }
    });
  });
</script>
@endpush 