// produk-helper.js
// Fungsi utilitas umum produk

function updateDetailSatuanOptions(selectedSatuanId, selector) {
  const detailSatuanSelect = $(selector);
  detailSatuanSelect.empty();
  detailSatuanSelect.prop('disabled', true);
  detailSatuanSelect.append('<option value="" selected disabled>Pilih detail satuan</option>');

  if (selectedSatuanId && window.satuanDetailList) {
    const filtered = window.satuanDetailList.filter(sub => sub.detail_parameter_id == selectedSatuanId);
    filtered.forEach(sub => {
      detailSatuanSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) {
      detailSatuanSelect.prop('disabled', false);
    } else {
      detailSatuanSelect.prop('disabled', true);
    }
  } else {
    detailSatuanSelect.prop('disabled', true);
  }
}

function updateEditDetailSatuanOptions(selectedSatuanId, currentSubSatuanId = null) {
  const detailSatuanSelect = $('#edit_detail_satuan');
  detailSatuanSelect.empty();
  detailSatuanSelect.prop('disabled', true);
  detailSatuanSelect.append('<option value="" selected disabled>Pilih detail satuan</option>');

  if (selectedSatuanId && window.satuanDetailList) {
    const filtered = window.satuanDetailList.filter(sub => sub.detail_parameter_id == selectedSatuanId);
    filtered.forEach(sub => {
      detailSatuanSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) {
      detailSatuanSelect.prop('disabled', false);
      if (currentSubSatuanId) {
        detailSatuanSelect.val(currentSubSatuanId);
      }
    }
  }
}

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

function toggleFinishingTab(selectedKategoriId, isEdit = false) {
  const prefix = isEdit ? 'edit-' : '';
  const finishingTab = $(`#${prefix}tab-finishing`);
  
  const isFinishingCategory = window.kategoriList?.some(kategori => 
      kategori.id == selectedKategoriId && 
      kategori.nama_detail_parameter?.toUpperCase() === 'FINISHING'
  );
  
  if (isFinishingCategory) {
      finishingTab.hide();
      if (finishingTab.hasClass('active')) {
          $(`#${prefix}tab-umum`).tab('show');
      }
  } else {
      finishingTab.show();
  }
}