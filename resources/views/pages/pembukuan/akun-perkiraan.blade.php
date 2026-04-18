@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('pembukuan.index') }}">Pembukuan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Akun Perkiraan</li>
    </ol>
  </nav>

  @php
    $formatBalance = function (float|int $amount): string {
      $formatted = number_format(abs((float) $amount), 0, ',', '.');

      return $amount < 0 ? '(' . $formatted . ')' : $formatted;
    };
  @endphp

  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card w-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
              <h6 class="card-title mb-1">Akun Perkiraan</h6>
              <p class="text-muted mb-0">Tampilan akun perkiraan dummy dengan gaya standar aplikasi.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambahAkun" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
              <i data-feather="plus" class="icon-sm me-1"></i> Tambah Akun
            </button>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-12 col-md-3">
              <select class="form-select form-select-sm" disabled>
                <option>Non Aktif: Semua</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <select class="form-select form-select-sm" disabled>
                <option>Tipe Akun: Semua</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Cari akun..." disabled>
                <span class="input-group-text"><i data-feather="search" class="icon-xs"></i></span>
              </div>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-md-end gap-2">
              <button class="btn btn-outline-primary btn-sm" disabled>
                <i data-feather="refresh-cw" class="icon-xs me-1"></i> Refresh
              </button>
              <button class="btn btn-outline-secondary btn-sm" disabled>
                <i data-feather="download" class="icon-xs me-1"></i> Export
              </button>
            </div>
          </div>

          <div class="table-responsive mb-4">
            <table class="table table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 160px;">Kode Perkiraan</th>
                  <th>Nama</th>
                  <th style="width: 180px;">Tipe Akun</th>
                  <th class="text-end" style="width: 170px;">Saldo</th>
                </tr>
              </thead>
              <tbody>
                @foreach($accounts as $account)
                  <tr class="account-row" style="cursor: pointer;" tabindex="0" role="button"
                    data-kode="{{ $account['kode'] }}"
                    data-nama="{{ $account['nama'] }}"
                    data-tipe="{{ $account['tipe'] }}"
                    data-saldo="{{ $account['saldo'] ?? 0 }}"
                  >
                    <td class="fw-semibold">{{ $account['kode'] }}</td>
                    <td>
                      <span style="padding-left: {{ ($account['level'] ?? 0) * 16 }}px; display: inline-block;">
                        {{ $account['nama'] }}
                      </span>
                    </td>
                    <td>{{ $account['tipe'] }}</td>
                    <td class="text-end {{ ($account['saldo'] ?? 0) < 0 ? 'text-danger' : '' }}">
                      Rp {{ $formatBalance($account['saldo'] ?? 0) }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <small class="text-muted d-block mb-0">Klik salah satu baris akun untuk membuka window entry dengan data akun terpilih.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalTambahAkun" tabindex="-1" aria-labelledby="modalTambahAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahAkunLabel">Entry Akun (Dummy)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="row g-3">
              <div class="col-12 col-lg-8">
                <label class="form-label">Tipe Akun</label>
                <input type="text" class="form-control" id="entryTipeAkun" value="{{ $selectedAccount['tipe'] }}" disabled>
              </div>

              <div class="col-12 col-lg-4 d-flex align-items-end">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" value="1" id="subAkun" disabled>
                  <label class="form-check-label" for="subAkun">Sub Akun</label>
                </div>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Kode Perkiraan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="entryKode" value="{{ $selectedAccount['kode'] }}" disabled>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="entryNama" value="{{ $selectedAccount['nama'] }}" disabled>
                <small class="text-muted">Contoh: BCA a/c XXX-XXX, dll</small>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Saldo</label>
                <input
                  type="text"
                  class="form-control {{ ($selectedAccount['saldo'] ?? 0) < 0 ? 'text-danger' : '' }}"
                  id="entrySaldo"
                  value="Rp {{ $formatBalance($selectedAccount['saldo'] ?? 0) }}"
                  disabled
                >
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary" disabled>Simpan</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('custom-scripts')
<script>
  feather.replace();

  (function () {
    const initialAccount = @json($selectedAccount);
    const modalElement = document.getElementById('modalTambahAkun');
    const addButton = document.getElementById('btnTambahAkun');
    const accountRows = document.querySelectorAll('.account-row');
    const inputTipe = document.getElementById('entryTipeAkun');
    const inputKode = document.getElementById('entryKode');
    const inputNama = document.getElementById('entryNama');
    const inputSaldo = document.getElementById('entrySaldo');

    let selectedAccount = {
      kode: initialAccount.kode ?? '',
      nama: initialAccount.nama ?? '',
      tipe: initialAccount.tipe ?? '',
      saldo: Number(initialAccount.saldo ?? 0)
    };

    const formatBalance = (amount) => {
      const absolute = Math.abs(Number(amount || 0));
      const formatted = new Intl.NumberFormat('id-ID').format(absolute);

      return Number(amount) < 0 ? `(${formatted})` : formatted;
    };

    const renderEntry = (account) => {
      inputTipe.value = account.tipe || '';
      inputKode.value = account.kode || '';
      inputNama.value = account.nama || '';
      inputSaldo.value = `Rp ${formatBalance(account.saldo)}`;

      if (Number(account.saldo) < 0) {
        inputSaldo.classList.add('text-danger');
      } else {
        inputSaldo.classList.remove('text-danger');
      }
    };

    addButton.addEventListener('click', function () {
      renderEntry(selectedAccount);
    });

    accountRows.forEach(function (row) {
      const openRowSelection = function () {
        selectedAccount = {
          kode: row.dataset.kode || '',
          nama: row.dataset.nama || '',
          tipe: row.dataset.tipe || '',
          saldo: Number(row.dataset.saldo || 0)
        };

        renderEntry(selectedAccount);

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();
      };

      row.addEventListener('click', openRowSelection);
      row.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openRowSelection();
        }
      });
    });
  })();
</script>
@endpush
