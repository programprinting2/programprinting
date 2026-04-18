@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('pembukuan.index') }}">Pembukuan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Pencatatan Beban</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-4 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title mb-3">Form Dummy Beban</h6>
          <p class="text-muted small">Input berikut hanya tampilan, belum menyimpan data.</p>

          <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" class="form-control" value="2026-04-12" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Beban</label>
            <input type="text" class="form-control" value="Internet Kantor" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Nominal</label>
            <input type="text" class="form-control" value="450000" disabled>
          </div>
          <button class="btn btn-primary" disabled>Simpan Draft</button>
        </div>
      </div>
    </div>

    <div class="col-lg-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">Draft Beban</h6>
            <span class="badge bg-light text-dark border">Dummy Data</span>
          </div>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>No Bukti</th>
                  <th>Jenis</th>
                  <th class="text-end">Nominal</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bebanDraft as $row)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $row['nomor'] }}</td>
                    <td>
                      <div>{{ $row['jenis'] }}</div>
                      <small class="text-muted">{{ $row['akun_beban'] }} -> {{ $row['akun_bayar'] }}</small>
                    </td>
                    <td class="text-end">Rp {{ number_format($row['nominal'], 0, ',', '.') }}</td>
                    <td>
                      @if($row['status'] === 'Posted')
                        <span class="badge bg-success">Posted</span>
                      @else
                        <span class="badge bg-warning text-dark">Draft</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
