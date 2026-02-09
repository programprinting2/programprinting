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
  const finishingTabBtn = $(`#${prefix}tab-finishing`);

  const isFinishingCategory = window.kategoriList?.some(kategori =>
    kategori.id == selectedKategoriId &&
    kategori.nama_detail_parameter?.toUpperCase() === 'FINISHING'
  );

  if (isFinishingCategory) {
    finishingTabBtn.addClass('disabled').attr('aria-disabled', 'true');
    if (finishingTabBtn.hasClass('active')) {
      $(`#${prefix}tab-umum`).tab('show');
    }
  } else {
    finishingTabBtn.removeClass('disabled').removeAttr('aria-disabled');
    finishingTabBtn.off('click.disabled');
  }
}

function calculateBahanBakuTotal(harga, jumlah, panjang, lebar) {
  let total = harga * jumlah;
  if (panjang > 0) total *= panjang;
  if (lebar > 0) total *= lebar;
  return total;
}

function produkConvertMetricUnit(value, fromUnit, toUnit, isArea = false) {
  const conversionFactors = {
    'cm_to_mm': 10,    // 1 cm = 10 mm
    'cm_to_m': 0.01,   // 1 cm = 0.01 m
    'mm_to_cm': 0.1,   // 1 mm = 0.1 cm
    'mm_to_m': 0.001,  // 1 mm = 0.001 m
    'm_to_cm': 100,    // 1 m = 100 cm
    'm_to_mm': 1000    // 1 m = 1000 mm
  };

  const key = `${fromUnit}_to_${toUnit}`;

  if (conversionFactors[key]) {
    if (isArea) {
      return value * (conversionFactors[key] * conversionFactors[key]);
    } else {
      return value * conversionFactors[key];
    }
  }
  return value;
}

// Update dimensi produk saat metric unit berubah
function produkUpdateMetricDimensions(newUnit, oldUnit, selectors) {
  // Update label satuan
  const unitMap = {
    'cm': { label: 'cm', area: 'cm²' },
    'mm': { label: 'mm', area: 'mm²' },
    'm':  { label: 'm',  area: 'm²' },
  };

  const meta = unitMap[newUnit] || unitMap['cm'];

  $(selectors.labelLebar).text(meta.label);
  $(selectors.labelPanjang).text(meta.label);
  $(selectors.labelLuas).text(meta.area);

  // Jika unit berubah, konversi nilai lebar & panjang
  if (oldUnit && oldUnit !== newUnit) {
    const currentLebar = parseFloat($(selectors.lebar).val()) || 0;
    if (currentLebar > 0) {
      const newLebar = produkConvertMetricUnit(currentLebar, oldUnit, newUnit);
      $(selectors.lebar).val(newLebar.toFixed(2));
    }

    const currentPanjang = parseFloat($(selectors.panjang).val()) || 0;
    if (currentPanjang > 0) {
      const newPanjang = produkConvertMetricUnit(currentPanjang, oldUnit, newUnit);
      $(selectors.panjang).val(newPanjang.toFixed(2));
    }

    // Hitung ulang luas
    const newLebarVal = parseFloat($(selectors.lebar).val()) || 0;
    const newPanjangVal = parseFloat($(selectors.panjang).val()) || 0;
    const newLuas = newLebarVal * newPanjangVal;
    $(selectors.luas).val(newLuas.toFixed(2));
  }
}