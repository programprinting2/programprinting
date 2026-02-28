@extends('layout.master')
@section('content')
<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Pembayaran SPK</h2>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
  </div>

  <div class="row">
    {{-- FORM PEMBAYARAN --}}
    <div class="col-lg-7 mb-3">
      <div class="card p-4">
        <h5 class="fw-bold mb-1">Form Pembayaran</h5>
        <p class="text-muted mb-4">
          Masukkan detail pembayaran untuk {{ $spk->nomor_spk }}
        </p>

        <form id="form-payment-spk" action="{{ route('kasir.spk.payment.store', ['spk' => $spk->id]) }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Jumlah Pembayaran</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input
                type="number"
                class="form-control @error('jumlah') is-invalid @enderror"
                name="jumlah"
                required
                min="0.01"
                step="0.01"
                placeholder="0"
                value="{{ old('jumlah', number_format($spk->sisa_pembayaran, 2, ',', '.')) }}"
              >
            </div>
            <div class="form-text">Masukkan jumlah pembayaran tanpa titik atau koma</div>
            @error('jumlah')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Metode Pembayaran</label>
            <select
              class="form-select @error('metode') is-invalid @enderror"
              name="metode"
              required
            >
              <option value="">Pilih metode pembayaran...</option>
              @php
                $metodeList = ['Transfer Bank', 'Tunai', 'QRIS', 'Debit', 'Credit Card'];
              @endphp
              @foreach($metodeList as $metode)
                <option value="{{ $metode }}" {{ old('metode') === $metode ? 'selected' : '' }}>
                  {{ $metode }}
                </option>
              @endforeach
            </select>
            @error('metode')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Pembayaran</label>
            <input
              type="date"
              class="form-control @error('tanggal') is-invalid @enderror"
              name="tanggal"
              required
              value="{{ old('tanggal', now()->toDateString()) }}"
            >
            @error('tanggal')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Referensi</label>
            <input
              type="text"
              class="form-control @error('referensi') is-invalid @enderror"
              name="referensi"
              value="{{ old('referensi') }}"
              placeholder="Contoh: TRX123456"
            >
            <div class="form-text">Nomor referensi/transaksi dari pembayaran (opsional)</div>
            @error('referensi')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Catatan</label>
            <textarea
              class="form-control @error('catatan') is-invalid @enderror"
              name="catatan"
              rows="3"
              placeholder="Catatan tambahan untuk pembayaran ini"
            >{{ old('catatan') }}</textarea>
            @error('catatan')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('spk.show', $spk) }}" class="btn btn-outline-secondary" id="btn-batal-payment">Batal</a>
            <button type="submit" class="btn btn-primary" id="btn-submit-payment">
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="payment-spinner"></span>
              <span id="payment-btn-text">Simpan Pembayaran</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- INFORMASI SPK + RINGKASAN PEMBAYARAN --}}
    <div class="col-lg-5 mb-3">
      <div class="card p-4">
        <h5 class="fw-bold mb-3">Informasi SPK</h5>

        <div class="mb-3">
          <div class="text-muted mb-1">Nomor SPK</div>
          <div class="fw-bold fs-5">{{ $spk->nomor_spk }}</div>
        </div>

        <div class="mb-3">
          <div class="text-muted mb-1">Pelanggan</div>
          <div class="fw-bold">{{ $spk->pelanggan?->nama ?? '-' }}</div>
        </div>

        <div class="mb-3">
          <div class="text-muted mb-1">Tanggal SPK</div>
          <div class="fw-bold">
            {{ optional($spk->tanggal_spk)->format('d/m/Y') ?? '-' }}
          </div>
        </div>

        <div class="mb-3">
          <div class="text-muted mb-1">Status Pembayaran</div>
          @php
            $statusPembayaranList = \App\Models\SPK::pembayaranStatusList();
            $labelStatus = $statusPembayaranList[$spk->status_pembayaran] ?? $spk->status_pembayaran;
          @endphp
          <span class="badge bg-white border-1 text-dark px-3">
            {{ $labelStatus }}
          </span>
        </div>

        <hr class="my-3">

        <div class="d-flex justify-content-between mb-2">
          <div class="text-muted">Total SPK</div>
          <div class="fw-bold">
            Rp {{ number_format($spk->total_biaya ?? 0, 0, ',', '.') }}
          </div>
        </div>

        <div class="d-flex justify-content-between mb-2">
          <div class="text-muted">Sudah Dibayar</div>
          <div class="fw-bold text-success">
            Rp {{ number_format($spk->total_dibayar ?? 0, 0, ',', '.') }}
          </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
          <div class="text-muted">Sisa Pembayaran</div>
          <div class="fw-bold text-danger">
            Rp {{ number_format($spk->sisa_pembayaran ?? 0, 0, ',', '.') }}
          </div>
        </div>

        <button
          class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2"
          type="button"
          onclick="document.querySelector('input[name=jumlah]').value='{{ $spk->sisa_pembayaran }}'"
        >
          <i class="fas fa-credit-card"></i> Bayar Sisa
        </button>
      </div>

      {{-- Opsional: ringkasan riwayat pembayaran --}}
      @if($spk->pembayaran->isNotEmpty())
        <div class="card p-4 mt-3">
          <h6 class="fw-bold mb-3">Riwayat Pembayaran</h6>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Metode</th>
                  <th class="text-end">Jumlah</th>
                </tr>
              </thead>
              <tbody>
                @foreach($spk->pembayaran as $pay)
                  <tr>
                    <td>{{ optional($pay->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $pay->metode }}</td>
                    <td class="text-end">Rp {{ number_format($pay->jumlah, 0, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  (function () {
    var form = document.getElementById('form-payment-spk');
    if (!form) return;
    form.addEventListener('submit', function () {
      var btn = document.getElementById('btn-submit-payment');
      var text = document.getElementById('payment-btn-text');
      var spinner = document.getElementById('payment-spinner');
      var batal = document.getElementById('btn-batal-payment');
      if (btn) btn.disabled = true;
      if (batal) {
        batal.classList.add('disabled');
        batal.style.pointerEvents = 'none';
      }
      if (text) text.textContent = 'Menyimpan...';
      if (spinner) spinner.classList.remove('d-none');
    });
  })();
</script>

@endsection