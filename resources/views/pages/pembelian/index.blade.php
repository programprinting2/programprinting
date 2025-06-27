@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" /> -->
@endpush

@section('content')

  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pembelian</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="row">
          <h6 class="card-title mb-0">Data Pembelian</h6>
          <p class="text-muted mb-3">Kelola data pembelian bahan baku dan barang lainnya</p>
        </div>
        <div class="d-flex align-items-center gap-3">
        <a href="{{ route('pembelian.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
          <i class="fa fa-plus"></i> Tambah Pembelian
        </a>
        </div>
      </div>
      <div class="mb-3">
        <input type="text" class="form-control" placeholder="Cari data pembelian berdasarkan nomor faktur, supplier, atau status..." id="searchInput" onkeyup="filterTable()">
      </div>
      <div class="table-responsive">
        <table class="table align-middle">
        <thead>
          <tr>
          <th style="width: 5%;">No</th>
          <th style="width: 15%;">Kode Pembelian</th>
          <th style="width: 15%;">Tanggal</th>
          <th style="width: 20%;">Pemasok</th>
          <th style="width: 10%;">Total</th>
          <th style="width: 10%;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($data_pembelian as $i => $item)
            <tr>
              <td>{{ $data_pembelian->firstItem() + $i }}</td>
              <td>{{ $item->kode_pembelian }}</td>
              <td>{{ tanggal_indo($item->tanggal) }}</td>
              <td>{{ $item->pemasok->nama ?? '-' }}</td>
              <td>Rp {{ number_format($item->items->sum('subtotal'),0,',','.') }}</td>
              <td>
                <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-sm btn-light" title="Detail"><i class="fa fa-eye"></i></a>
                <a href="{{ route('pembelian.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" class="d-inline-block form-hapus-pembelian">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="btn btn-sm btn-danger btn-hapus-pembelian" title="Hapus"><i class="fa fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Tidak ada data pembelian ditemukan</td>
            </tr>
          @endforelse
        </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $data_pembelian->links() }}
      </div>
      </div>
    </div>
    </div>
  </div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
function filterTable() {
  var input, filter, table, tr, td, i, j, txtValue, show;
  input = document.getElementById('searchInput');
  filter = input.value.toUpperCase();
  table = document.getElementById('pembelianTableBody');
  tr = table.getElementsByTagName('tr');
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName('td');
    show = false;
    for (j = 0; j < td.length-1; j++) { // -1 agar kolom action tidak ikut dicari
      if (td[j]) {
        txtValue = td[j].textContent || td[j].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          show = true;
          break;
        }
      }
    }
    tr[i].style.display = show ? '' : 'none';
  }
}

document.querySelectorAll('.btn-hapus-pembelian').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'Hapus Data?',
      text: 'Data pembelian yang dihapus tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        btn.closest('form').submit();
      }
    });
  });
});
  </script>
@endpush