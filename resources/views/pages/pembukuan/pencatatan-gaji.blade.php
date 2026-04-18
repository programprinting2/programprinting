@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('pembukuan.index') }}">Pembukuan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Pencatatan Gaji</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
              <h6 class="card-title mb-1">Pencatatan Gaji</h6>
              <p class="text-muted mb-0">Periode {{ $periode }} - mode dummy review.</p>
            </div>
            <button class="btn btn-primary btn-sm" disabled>Generate Jurnal Gaji</button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Nama Karyawan</th>
                  <th>Jabatan</th>
                  <th class="text-end">Gaji Pokok</th>
                  <th class="text-end">Tunjangan</th>
                  <th class="text-end">Potongan</th>
                  <th class="text-end">Take Home Pay</th>
                </tr>
              </thead>
              <tbody>
                @php($totalNet = 0)
                @foreach($payrollDraft as $item)
                  @php($net = $item['gaji_pokok'] + $item['tunjangan'] - $item['potongan'])
                  @php($totalNet += $net)
                  <tr>
                    <td class="fw-semibold">{{ $item['nama'] }}</td>
                    <td>{{ $item['jabatan'] }}</td>
                    <td class="text-end">Rp {{ number_format($item['gaji_pokok'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item['tunjangan'], 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($item['potongan'], 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($net, 0, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="5" class="text-end">Total Take Home Pay</th>
                  <th class="text-end text-primary">Rp {{ number_format($totalNet, 0, ',', '.') }}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
