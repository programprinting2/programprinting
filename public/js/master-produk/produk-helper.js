/**
 * produk Helper - Fungsi-fungsi umum untuk manajemen produk
 */

// Fungsi untuk mengupdate opsi sub-kategori di form modal
function updateSubKategoriOptions(selectedKategoriId) {
  const subKategoriSelect = $('#sub_kategori_id_produk');
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategoriId && window.subKategoriList) {
    const filtered = window.subKategoriList.filter(sub => sub.detail_parameter_id == selectedKategoriId);
    filtered.forEach(sub => {
      subKategoriSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) subKategoriSelect.prop('disabled', false);
  }
}