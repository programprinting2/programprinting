// produk-helper.js
// Fungsi utilitas umum produk

function updateSubKategoriOptions(selectedKategoriId, selector) {
  const subKategoriSelect = $(selector);
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategoriId && window.subKategoriList) {
    const filtered = window.subKategoriList.filter(sub => sub.detail_parameter_id == selectedKategoriId);
    filtered.forEach(sub => {
      subKategoriSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) subKategoriSelect.prop('disabled', false);
    else subKategoriSelect.prop('disabled', true);
  } else {
    subKategoriSelect.prop('disabled', true);
  }
}

// Fungsi untuk mengupdate opsi sub-kategori di edit modal
function updateEditSubKategoriOptions(selectedKategoriId, currentSubKategoriId = null) {
  const subKategoriSelect = $('#edit_sub_kategori_id_produk');
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategoriId && window.subKategoriList) {
    const filtered = window.subKategoriList.filter(sub => sub.detail_parameter_id == selectedKategoriId);
    filtered.forEach(sub => {
      subKategoriSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) subKategoriSelect.prop('disabled', false);
    if (currentSubKategoriId) {
      subKategoriSelect.val(currentSubKategoriId);
    }
  }
}