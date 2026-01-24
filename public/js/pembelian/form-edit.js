// public/js/pembelian/form-edit.js
// Script modular untuk form edit pembelian
(function() {
  // Inisialisasi helper pembelian
  PembelianHelper.init();

  // --- Helper Functions ---
  function updateWarnaPreview(hexCode) {
    const warnaPreview = document.getElementById('warnaPreview');
    if (hexCode && /^#[0-9A-F]{6}$/i.test(hexCode)) {
        warnaPreview.style.backgroundColor = hexCode;
        warnaPreview.style.display = 'block';
        warnaPreview.title = `Warna: ${hexCode}`;
    } else {
        warnaPreview.style.display = 'none';
        warnaPreview.style.backgroundColor = '';
        warnaPreview.title = 'No Color';
    }
  }
  
  // Fungsi untuk mengecek duplikasi bahan baku
  function checkBahanBakuDuplikat(bahanbakuId) {
    const existingBahanBakuIds = [];
    const itemBody = document.getElementById('itemBody');
    
    // Ambil semua bahan baku yang sudah ada (prefill + baru ditambahkan)
    for (let row of itemBody.children) {
      const bahanbakuInput = row.querySelector('input[name$="[bahanbaku_id]"]');
      if (bahanbakuInput && bahanbakuInput.value) {
        existingBahanBakuIds.push(bahanbakuInput.value);
      }
    }
    
    return existingBahanBakuIds.includes(bahanbakuId.toString());
  }
  
  // --- Modal Bahan Baku ---
  document.getElementById('btnCariBahanBaku').addEventListener('click', function() {
    $('#modalCariBahanBakuPembelian').modal('show');
  });
  document.getElementById('namaBahanBakuInput').addEventListener('click', function() {
    $('#modalCariBahanBakuPembelian').modal('show');
  });
  // Untuk mencegah duplikasi event listener pada hargaInput
  let hargaInputListener = null;
  window.hargaSatuanUtama = 0; // Tambah variabel global untuk harga satuan utama

  window.addEventListener('bahanBakuDipilih', function(e) {
    const data = e.detail;
    
    // Cek duplikasi menggunakan fungsi helper
    if (checkBahanBakuDuplikat(data.id)) {
      Swal.fire({
        title: 'Bahan Baku Sudah Ada',
        text: `Bahan baku "${data.nama}" sudah ada dalam daftar pembelian.`,
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    // Jika tidak duplikat, isi form input
    document.getElementById('bahanbakuIdInput').value = data.id;
    document.getElementById('namaBahanBakuInput').value = data.nama;
    // Format harga dengan pemisah ribuan
    document.getElementById('hargaInput').value = PembelianHelper.formatNumber(data.harga);
    window.hargaSatuanUtama = parseInt(data.harga) || 0; // Simpan harga satuan utama
    document.getElementById('kodeBahanBakuInput') && (document.getElementById('kodeBahanBakuInput').value = data.kode);

    const warnaHex = data.warna_detail?.keterangan || null;
    updateWarnaPreview(warnaHex);

    var satuanInput = document.getElementById('satuanInput');
    satuanInput.innerHTML = '';
    satuanInput.disabled = false; // Aktifkan dropdown setelah bahan baku dipilih
    let konv = data.konversi_satuan;
    if (typeof konv === 'string') {
      try { konv = JSON.parse(konv); } catch { konv = []; }
    }
    let satuanOptions = '';
    if (Array.isArray(konv) && konv.length > 0) {
      konv.forEach(k => {
        if (k.satuan_dari && k.jumlah) {
          const namaKonversi = getNamaSatuanById(k.satuan_dari);
          satuanOptions += `<option value="${k.satuan_dari}" data-konversi="${k.jumlah}">${namaKonversi}</option>`;
        }
      });
    }
    satuanInput.innerHTML = satuanOptions;
    updateKonversiSatuanInfo(data);
    // Cegah duplikasi event listener pada hargaInput
    const hargaInput = document.getElementById('hargaInput');
    if (hargaInputListener) {
      hargaInput.removeEventListener('input', hargaInputListener);
    }
    hargaInputListener = function() {
      updateKonversiSatuanInfo(data);
    };
    hargaInput.addEventListener('input', hargaInputListener);
    updatePreviewTotalItem();
    if (!window.bahanBakuCache) window.bahanBakuCache = {};
    window.bahanBakuCache[data.id] = data;
  });

  // --- Preview Total Item ---
  function updatePreviewTotalItem() {
    const jumlah = parseInt(document.getElementById('jumlahInput').value) || 1;
    const harga = PembelianHelper.getNumericValue($('#hargaInput'));
    const diskon = parseFloat(document.getElementById('diskonInput').value) || 0;
    // Tidak perlu konversi lagi, harga sudah sesuai satuan yang dipilih
    let total = jumlah * harga * (1 - diskon / 100);
    if (isNaN(total) || total < 0) total = 0;
    document.getElementById('previewTotalItem').value = PembelianHelper.formatNumber('Rp ' + (total ? total : 0));
  }

  document.getElementById('jumlahInput').addEventListener('input', updatePreviewTotalItem);
  document.getElementById('hargaInput').addEventListener('input', function() {
    // Jika user mengubah harga pada satuan konversi, update harga satuan utama
    const satuanSelect = document.getElementById('satuanInput');
    const konversi = parseInt(satuanSelect.options[satuanSelect.selectedIndex].getAttribute('data-konversi')) || 1;
    const hargaInput = document.getElementById('hargaInput');
    let hargaSekarang = PembelianHelper.getNumericValue($(hargaInput));
    if (konversi > 0) {
      window.hargaSatuanUtama = Math.round(hargaSekarang / konversi);
    } else {
      window.hargaSatuanUtama = hargaSekarang;
    }
    // Tidak perlu update hargaInput di sini, biarkan user input manual
    updatePreviewTotalItem();
  });
  document.getElementById('diskonInput').addEventListener('input', updatePreviewTotalItem);
  document.getElementById('satuanInput').addEventListener('change', function() {
    // Ambil harga satuan utama dari variabel global
    const satuanSelect = document.getElementById('satuanInput');
    const hargaInput = document.getElementById('hargaInput');
    const konversi = parseInt(satuanSelect.options[satuanSelect.selectedIndex].getAttribute('data-konversi')) || 1;
    // Jika satuan utama, harga = harga satuan utama
    // Jika satuan konversi, harga = harga satuan utama x jumlah konversi
    let hargaBaru = window.hargaSatuanUtama * konversi;
    hargaInput.value = PembelianHelper.formatNumber(hargaBaru);
    updatePreviewTotalItem();
    // Optional: update info konversi satuan jika perlu
    // Jika ingin update info konversi, panggil updateKonversiSatuanInfo dengan data bahan baku terakhir
  });

  // --- Tambah Item ---
  document.getElementById('btnTambahItem').addEventListener('click', function () {
    const bahanbakuId = document.getElementById('bahanbakuIdInput').value;
    const bahanbakuNama = document.getElementById('namaBahanBakuInput').value;
    const jumlahInput = document.getElementById('jumlahInput');
    const hargaInput = document.getElementById('hargaInput');
    const diskonInput = document.getElementById('diskonInput');
    const satuanSelect = document.getElementById('satuanInput');
    const itemBody = document.getElementById('itemBody');
    const hargaSatuan = PembelianHelper.getNumericValue($(hargaInput));
    const jumlah = parseInt(jumlahInput.value) || 1;
    const diskonPersen = parseFloat(diskonInput.value) || 0;
    
    if (!bahanbakuId) return Swal.fire({icon: 'error', title: 'Pilih bahan baku terlebih dahulu!'});
    if (jumlah < 1) return Swal.fire({icon: 'error', title: 'Jumlah minimal 1!'});
    if (hargaSatuan < 1) return Swal.fire({icon: 'error', title: 'Harga minimal 1!'});
    if (diskonPersen < 0 || diskonPersen > 100) return Swal.fire({icon: 'error', title: 'Diskon harus antara 0-100%'});
    
    // Perhitungan: harga per item sesuai satuan yang dipilih
    const itemHarga = hargaSatuan; // Hanya harga satuan, bukan total
    const itemTotal = itemHarga * jumlah * (1 - diskonPersen / 100);
    
    if (itemBody.children.length === 1 && itemBody.children[0].children.length === 1) {
      itemBody.innerHTML = '';
    }
    let index = 0;
    for (let row of itemBody.children) {
      if (row.querySelector('input[name^="items"]')) index++;
    }
    const row = document.createElement('tr');
    const kodeBahan = document.getElementById('kodeBahanBakuInput').value;
    const satuanValue = satuanSelect.value;
    const satuanLabel = satuanSelect.options[satuanSelect.selectedIndex].text;
    
    row.innerHTML = `
      <td>${kodeBahan}<input type="hidden" name="items[${index}][bahanbaku_id]" value="${bahanbakuId}"></td>
      <td>${bahanbakuNama}</td>
      <td class="item-jumlah">${jumlah}<input type="hidden" name="items[${index}][jumlah]" value="${jumlah}"></td>
      <td class="item-satuan">${satuanLabel}<input type="hidden" name="items[${index}][satuan]" value="${satuanValue}"></td>
      <td class="item-harga text-end">${PembelianHelper.formatNumber(itemHarga)}<input type="hidden" name="items[${index}][harga]" value="${itemHarga}"></td>
      <td class="item-diskon text-end">${diskonPersen}%<input type="hidden" name="items[${index}][diskon_persen]" value="${diskonPersen}"></td>
      <td class="item-total text-end">${PembelianHelper.formatNumber(itemTotal)}</td>
      <td>
      <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
      <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
      </td>
      `;
    itemBody.appendChild(row);
    reindexItemInputs();
    updatePreviewTotalItem();
    updateRingkasanBiaya();
    syncDiskonSaatSubtotalBerubah();
    document.getElementById('bahanbakuIdInput').value = '';
    document.getElementById('namaBahanBakuInput').value = '';
    jumlahInput.value = 1;
    hargaInput.value = '0';
    diskonInput.value = 0;
    document.getElementById('previewTotalItem').value = 'Rp 0';
    var satuanInput = document.getElementById('satuanInput');
    satuanInput.innerHTML = '';
    satuanInput.disabled = true; 
    document.getElementById('konversiSatuanInfo').style.display = 'none';
    document.getElementById('konversiSatuanInfo').innerHTML = '';
    updateWarnaPreview();
  });

  // --- Edit & Hapus Item ---
  document.getElementById('itemBody').addEventListener('click', function (e) {
    if (e.target.closest('.btn-hapus-item')) {
      e.target.closest('tr').remove();
      if (itemBody.children.length === 0) {
        itemBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Belum ada item yang ditambahkan</td></tr>';
      }
      reindexItemInputs();
      updateRingkasanBiaya();
      syncDiskonSaatSubtotalBerubah();
    }
    if (e.target.closest('.btn-edit-item')) {
      const row = e.target.closest('tr');
      const jumlah = row.querySelector('.item-jumlah').textContent;
      const harga = row.querySelector('.item-harga').textContent;
      let diskon = row.querySelector('.item-diskon').textContent;
      if (diskon && diskon.includes('%')) diskon = diskon.replace('%','').trim();
      if (diskon === '' || diskon === null || diskon === undefined) diskon = 0;
      // Ambil data bahan baku dari row
      const bahanbakuId = row.querySelector('input[name$="[bahanbaku_id]"]').value;
      const satuanId = row.querySelector('input[name$="[satuan]"]').value;
      row.querySelector('.item-jumlah').innerHTML = `<input type='number' class='form-control form-control-sm input-edit-jumlah' value='${jumlah}' min='1'>`;
      let hargaSatuan = parseInt(harga.replace(/\./g, '')) || 0;
      row.querySelector('.item-harga').innerHTML = `<input type='text' class='form-control form-control-sm input-edit-harga' value='${PembelianHelper.formatNumber(hargaSatuan)}'>`;
      row.querySelector('.item-diskon').innerHTML = `<input type='number' class='form-control form-control-sm input-edit-diskon' value='${diskon}' min='0' max='100' step='0.01'>`;
      let satuanOptions = '';
      if (window.bahanBakuCache && window.bahanBakuCache[bahanbakuId]) {
        const bahanBakuData = window.bahanBakuCache[bahanbakuId];
        let konv = bahanBakuData.konversi_satuan;
        if (typeof konv === 'string') {
          try { konv = JSON.parse(konv); } catch { konv = []; }
        }
        if (Array.isArray(konv) && konv.length > 0) {
          konv.forEach(k => {
            if (k.satuan_dari && k.jumlah) {
              const namaKonversi = getNamaSatuanById(k.satuan_dari);
              const selected = String(k.satuan_dari) === String(satuanId) ? 'selected' : '';
              satuanOptions += `<option value="${k.satuan_dari}" data-konversi="${k.jumlah}" ${selected}>${namaKonversi}</option>`;
            }
          });
        }
      }
      row.querySelector('.item-satuan').innerHTML = `
        <select class='form-control form-control-sm input-edit-satuan'>
          ${satuanOptions}
        </select>
        <input type='hidden' name='items[][satuan]' value='${satuanId}'>
      `;
      row.querySelector('td:last-child').innerHTML = `
      <button type="button" class="btn btn-sm btn-success btn-simpan-edit me-1"><i class="fa fa-check"></i></button>
      <button type="button" class="btn btn-sm btn-secondary btn-batal-edit"><i class="fa fa-times"></i></button>
      `;
      // --- LIVE CALCULATE ---
      const jumlahInput = row.querySelector('.input-edit-jumlah');
      const hargaInput = row.querySelector('.input-edit-harga');
      const diskonInput = row.querySelector('.input-edit-diskon');
      const satuanSelect = row.querySelector('.input-edit-satuan');
      function updateItemTotalLive() {
        const jumlahVal = parseInt(jumlahInput.value) || 0;
        const hargaVal = PembelianHelper.getNumericValue($(hargaInput));
        const diskonVal = parseFloat(diskonInput.value) || 0;
        const total = hargaVal * jumlahVal * (1 - diskonVal/100);
        row.querySelector('.item-total').textContent = PembelianHelper.formatNumber(total);
        updateRingkasanBiaya();
      }
      jumlahInput.addEventListener('input', updateItemTotalLive);
      hargaInput.addEventListener('input', updateItemTotalLive);
      diskonInput.addEventListener('input', updateItemTotalLive);
      if (satuanSelect) {
        satuanSelect.addEventListener('change', function() {
          const konversi = parseInt(this.options[this.selectedIndex].getAttribute('data-konversi')) || 1;
          // Update harga berdasarkan satuan yang dipilih
          if (window.bahanBakuCache && window.bahanBakuCache[bahanbakuId]) {
            const bahanBakuData = window.bahanBakuCache[bahanbakuId];
            const hargaSatuanUtama = parseInt(bahanBakuData.harga) || 0;
            const hargaBaru = hargaSatuanUtama * konversi;
            hargaInput.value = PembelianHelper.formatNumber(hargaBaru);
          }
          updateItemTotalLive();
        });
      }
      // --- END LIVE CALCULATE ---
    }
    if (e.target.closest('.btn-batal-edit')) {
      const row = e.target.closest('tr');
      const jumlah = row.querySelector('.input-edit-jumlah').defaultValue;
      const hargaSatuan = PembelianHelper.getNumericValue($(row.querySelector('.input-edit-harga')));
      const diskon = row.querySelector('.input-edit-diskon').defaultValue;
      const satuanId = row.querySelector('input[name$="[satuan]"]').value;
      
      const itemHarga = hargaSatuan; // Hanya harga satuan
      const itemTotal = itemHarga * parseInt(jumlah) * (1 - (parseFloat(diskon) || 0) / 100);
      
      // Ambil nama satuan dari satuanList
      let satuanLabel = satuanId;
      if (window.satuanList) {
        const found = window.satuanList.find(s => String(s.id) === String(satuanId));
        if (found) {
          satuanLabel = found.nama_sub_detail_parameter;
        }
      }
      
      row.querySelector('.item-jumlah').innerHTML = `${jumlah}<input type="hidden" name="items[][jumlah]" value="${jumlah}">`;
      row.querySelector('.item-harga').innerHTML = `${PembelianHelper.formatNumber(itemHarga)}<input type="hidden" name="items[][harga]" value="${itemHarga}">`;
      row.querySelector('.item-diskon').innerHTML = `${diskon}<input type="hidden" name="items[][diskon_persen]" value="${diskon}">`;
      row.querySelector('.item-satuan').innerHTML = `${satuanLabel}<input type="hidden" name="items[][satuan]" value="${satuanId}">`;
      row.querySelector('.item-total').textContent = PembelianHelper.formatNumber(itemTotal);
      row.querySelector('td:last-child').innerHTML = `
      <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
      <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
      `;
      reindexItemInputs();
      updateRingkasanBiaya();
      syncDiskonSaatSubtotalBerubah();
    }
    if (e.target.closest('.btn-simpan-edit')) {
      const row = e.target.closest('tr');
      const jumlah = row.querySelector('.input-edit-jumlah').value;
      const hargaSatuan = PembelianHelper.getNumericValue($(row.querySelector('.input-edit-harga')));
      const diskon = row.querySelector('.input-edit-diskon').value;
      const satuanSelect = row.querySelector('.input-edit-satuan');
      const satuanId = satuanSelect ? satuanSelect.value : row.querySelector('input[name$="[satuan]"]').value;
      
      if (jumlah < 1) return alert('Jumlah minimal 1!');
      if (hargaSatuan < 1) return alert('Harga minimal 1!');
      if (diskon === '' || diskon === null || diskon === undefined) {
        return alert('Diskon tidak boleh dikosongkan!');
      }
      if (diskon < 0) return alert('Diskon tidak boleh negatif!');
      if (diskon > 100) return alert('Diskon tidak boleh lebih dari 100%!');
      
      const itemHarga = hargaSatuan; // Hanya harga satuan
      const itemTotal = itemHarga * parseInt(jumlah) * (1 - (parseFloat(diskon) || 0) / 100);
      
      // Ambil nama satuan dari satuanList
      let satuanLabel = satuanId;
      if (window.satuanList) {
        const found = window.satuanList.find(s => String(s.id) === String(satuanId));
        if (found) {
          satuanLabel = found.nama_sub_detail_parameter;
        }
      }
      
      row.querySelector('.item-jumlah').innerHTML = `${jumlah}<input type="hidden" name="items[][jumlah]" value="${jumlah}">`;
      row.querySelector('.item-harga').innerHTML = `${PembelianHelper.formatNumber(itemHarga)}<input type="hidden" name="items[][harga]" value="${itemHarga}">`;
      row.querySelector('.item-diskon').innerHTML = `${diskon}<input type="hidden" name="items[][diskon_persen]" value="${diskon}">`;
      row.querySelector('.item-satuan').innerHTML = `${satuanLabel}<input type="hidden" name="items[][satuan]" value="${satuanId}">`;
      row.querySelector('.item-total').textContent = PembelianHelper.formatNumber(itemTotal);
      row.querySelector('td:last-child').innerHTML = `
      <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
      <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
      `;
      reindexItemInputs();
      updateRingkasanBiaya();
      syncDiskonSaatSubtotalBerubah();
    }
  });

  // Fungsi untuk reindex semua input hidden item
  function reindexItemInputs() {
    const itemBody = document.getElementById('itemBody');
    let idx = 0;
    for (let row of itemBody.children) {
      // Lewati baris kosong
      if (!row.querySelector('input[name^="items"]')) continue;
      // bahanbaku_id
      let input = row.querySelector('input[name$="[bahanbaku_id]"]');
      if (input) input.name = `items[${idx}][bahanbaku_id]`;
      // jumlah
      input = row.querySelector('input[name$="[jumlah]"]');
      if (input) input.name = `items[${idx}][jumlah]`;
      // satuan
      input = row.querySelector('input[name$="[satuan]"]');
      if (input) input.name = `items[${idx}][satuan]`;
      // harga
      input = row.querySelector('input[name$="[harga]"]');
      if (input) input.name = `items[${idx}][harga]`;
      // diskon_persen
      input = row.querySelector('input[name$="[diskon_persen]"]');
      if (input) input.name = `items[${idx}][diskon_persen]`;
      idx++;
    }
  }

  // --- Ringkasan Biaya ---
  function updateRingkasanBiaya() {
    // 1. Hitung subtotal: total semua item setelah diskon per item
    let subtotal = 0;
    const itemBody = document.getElementById('itemBody');
    for (let row of itemBody.children) {
      if (row.children.length < 7) continue;
      // Ambil item-total dari kolom
      const itemTotal = parseFloat(row.querySelector('.item-total').textContent.replace(/\./g, '').replace(/Rp\s?/g, '')) || 0;
      subtotal += itemTotal;
    }
    // 2. Diskon total pembelian: HANYA dari field jumlah_diskon (Rp)
    //    Field diskon_persen hanya untuk sinkronisasi input, tidak ikut dijumlahkan
    const jumlahDiskon = PembelianHelper.getNumericValue($('[name="jumlah_diskon"]'));
    // 3. DPP = subtotal - diskon total pembelian
    const dpp = subtotal - jumlahDiskon;
    // 4. Pajak
    const tarifPajak = parseFloat(document.querySelector('[name="tarif_pajak"]').value) || 0;
    const pajak = dpp * (tarifPajak / 100);
    // 5. Biaya pengiriman, biaya lain, nota kredit
    const biayaPengiriman = PembelianHelper.getNumericValue($('[name="biaya_pengiriman"]'));
    const biayaLain = PembelianHelper.getNumericValue($('[name="biaya_lain"]'));
    const notaKredit = PembelianHelper.getNumericValue($('[name="nota_kredit"]'));
    // 6. Total akhir
    const total = dpp + pajak + biayaPengiriman + biayaLain - notaKredit;
    // Format rupiah
    function formatRupiah(val) {
      return 'Rp ' + (Math.round(val)||0).toLocaleString('id-ID');
    }
    // Update tampilan
    document.querySelectorAll('.ringkasan-subtotal').forEach(e => e.textContent = formatRupiah(subtotal));
    document.querySelectorAll('.ringkasan-diskon').forEach(e => e.textContent = '- ' + formatRupiah(jumlahDiskon));
    document.querySelectorAll('.ringkasan-pengiriman').forEach(e => e.textContent = formatRupiah(biayaPengiriman));
    document.querySelectorAll('.ringkasan-biayalain').forEach(e => e.textContent = formatRupiah(biayaLain));
    document.querySelectorAll('.ringkasan-notakredit').forEach(e => e.textContent = '- ' + formatRupiah(notaKredit));
    document.querySelectorAll('.ringkasan-pajak').forEach(e => e.textContent = formatRupiah(pajak));
    document.querySelectorAll('.ringkasan-total').forEach(e => e.textContent = formatRupiah(total));
  }

  // Pastikan updateRingkasanBiaya dipanggil setiap field biaya berubah
  document.querySelectorAll('[name="diskon_persen"], [name="jumlah_diskon"], [name="tarif_pajak"], [name="biaya_pengiriman"], [name="biaya_lain"], [name="nota_kredit"]').forEach(function(input) {
    input.addEventListener('input', updateRingkasanBiaya);
  });

  // --- Sinkronisasi Diskon (%) <-> Jumlah Diskon (Rp) ---
  let isSyncingDiskon = false;
  const diskonPersenInput = document.querySelector('[name="diskon_persen"]');
  const jumlahDiskonInput = document.querySelector('[name="jumlah_diskon"]');

  function getSubtotalItemSetelahDiskonPerItem() {
    let subtotal = 0;
    const itemBody = document.getElementById('itemBody');
    for (let row of itemBody.children) {
      if (row.children.length < 7) continue;
      // Ambil item-total dari kolom
      const itemTotal = parseFloat(row.querySelector('.item-total').textContent.replace(/\./g, '').replace(/Rp\s?/g, '')) || 0;
      subtotal += itemTotal;
    }
    return subtotal;
  }

  // Fungsi untuk sinkronisasi diskon saat subtotal berubah
  function syncDiskonSaatSubtotalBerubah() {
    if (isSyncingDiskon) return;
    
    const subtotal = getSubtotalItemSetelahDiskonPerItem();
    const diskonPersen = parseFloat(diskonPersenInput.value) || 0;
    
    // Hitung ulang jumlah diskon berdasarkan persentase yang ada
    const jumlahDiskonBaru = Math.round(subtotal * (diskonPersen / 100));
    
    // Update field diskon (Rp) dengan nilai baru
    jumlahDiskonInput.value = PembelianHelper.formatNumber(jumlahDiskonBaru);
    
    // Update ringkasan biaya
    updateRingkasanBiaya();
  }

  diskonPersenInput.addEventListener('input', function() {
    if (isSyncingDiskon) return;
    isSyncingDiskon = true;
    const persen = parseFloat(this.value) || 0;
    const subtotal = getSubtotalItemSetelahDiskonPerItem();
    const jumlah = Math.round(subtotal * (persen / 100));
    jumlahDiskonInput.value = PembelianHelper.formatNumber(jumlah);
    isSyncingDiskon = false;
    updateRingkasanBiaya();
  });

  jumlahDiskonInput.addEventListener('input', function() {
    if (isSyncingDiskon) return;
    isSyncingDiskon = true;
    const jumlah = PembelianHelper.getNumericValue($(this));
    const subtotal = getSubtotalItemSetelahDiskonPerItem();
    let persen = 0;
    if (subtotal > 0) {
      persen = (jumlah / subtotal) * 100;
    }
    diskonPersenInput.value = Math.round(persen * 100) / 100; // 2 digit desimal
    isSyncingDiskon = false;
    updateRingkasanBiaya();
  });

  // --- Otomatisasi tanggal jatuh tempo ---
  function updateJatuhTempo() {
    const hari = parseInt(document.getElementById('jatuhTempoHari').value) || 0;
    const tanggalPembelian = document.getElementById('tanggalPembelian').value;
    if (hari > 0 && tanggalPembelian) {
      const tgl = new Date(tanggalPembelian);
      tgl.setDate(tgl.getDate() + hari);
      const yyyy = tgl.getFullYear();
      const mm = String(tgl.getMonth() + 1).padStart(2, '0');
      const dd = String(tgl.getDate()).padStart(2, '0');
      document.getElementById('tanggalJatuhTempo').value = `${yyyy}-${mm}-${dd}`;
    }
  }

  document.getElementById('jatuhTempoHari').addEventListener('input', updateJatuhTempo);
  document.getElementById('tanggalPembelian').addEventListener('change', updateJatuhTempo);

  // --- Modal Pemasok ---
  document.getElementById('btnCariPemasok').addEventListener('click', function() {
    $('#modalCariPemasokPembelian').modal('show');
  });
  document.getElementById('namaPemasokInput').addEventListener('click', function() {
    $('#modalCariPemasokPembelian').modal('show');
  });
  window.addEventListener('pemasokDipilih', function(e) {
    const data = e.detail;
    document.getElementById('pemasokIdInput').value = data.id;
    document.getElementById('namaPemasokInput').value = data.nama + ' [' + data.kode + ']';
    document.getElementById('kodePemasokInput').value = data.kode;
  });

  // --- Validasi sebelum submit form ---
  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    console.log('=== FORM SUBMIT DEBUG ===');
    for (let [key, value] of formData.entries()) {
      if (key.includes('[jumlah]')) {
        console.log(`Field: ${key}, Value: ${value}`);
      }
    }
    console.log('=== END FORM SUBMIT DEBUG ===');
    
    // Validasi pemasok
    const pemasokId = document.getElementById('pemasokIdInput').value;
    if (!pemasokId) {
      Swal.fire({icon: 'warning', title: 'Pilih pemasok terlebih dahulu!'});
      return false;
    }
    // Validasi tanggal pembelian
    const tanggalPembelian = document.getElementById('tanggalPembelian').value;
    if (!tanggalPembelian) {
      Swal.fire({icon: 'warning', title: 'Tanggal pembelian wajib diisi!'});
      return false;
    }
    // Validasi minimal 1 item
    const itemBody = document.getElementById('itemBody');
    const itemRows = Array.from(itemBody.children).filter(row => row.querySelector('input[name^="items"]'));
    if (itemRows.length === 0) {
      Swal.fire({icon: 'warning', title: 'Minimal 1 item pembelian harus diisi!'});
      return false;
    }
    // Validasi setiap item: jumlah dan harga tidak boleh kosong/0
    for (let i = 0; i < itemRows.length; i++) {
      const jumlahInput = itemRows[i].querySelector('input[name$="[jumlah]"]');
      const hargaInput = itemRows[i].querySelector('input[name$="[harga]"]');
      const diskonInput = itemRows[i].querySelector('input[name$="[diskon_persen]"]');
      if (!jumlahInput || !hargaInput || !diskonInput) continue; // Lewati baris yang tidak valid

      const jumlah = jumlahInput.value;
      const harga = hargaInput.value;
      const diskon = diskonInput.value;
      if (!jumlah || parseInt(jumlah) < 1) {
        Swal.fire({icon: 'warning', title: `Jumlah item ke-${i+1} wajib diisi dan minimal 1!`});
        return false;
      }
      if (!harga || parseInt(harga) < 1) {
        Swal.fire({icon: 'warning', title: `Harga item ke-${i+1} wajib diisi dan minimal 1!`});
        return false;
      }
      if (diskon === '' || diskon === null || diskon === undefined) {
        Swal.fire({icon: 'warning', title: `Diskon item ke-${i+1} tidak boleh dikosongkan!`});
        return false;
      }
    }
    // Validasi total tidak boleh minus
    let total = 0;
    const totalEl = document.querySelector('.ringkasan-total');
    if (totalEl) {
      const totalStr = totalEl.textContent.replace(/[^\d-]/g, '');
      total = parseInt(totalStr) || 0;
    }
    if (total < 0) {
      Swal.fire({icon: 'error', title: 'Total pembelian tidak boleh minus!'});
      return false;
    }
    
    // Konfirmasi sebelum update
    Swal.fire({
      title: 'Update Pembelian?',
      text: 'Data pembelian akan diupdate. Lanjutkan?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, Update!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        // Lolos semua validasi, tampilkan spinner dan disable tombol
        const btnUpdate = document.getElementById('btnUpdatePembelian');
        const spinner = document.getElementById('spinnerUpdatePembelian');
        const label = btnUpdate.querySelector('.label-update');
        btnUpdate.disabled = true;
        spinner.classList.remove('d-none');
        label.textContent = 'Mengupdate...';
        
        // Submit form
        document.getElementById('editForm').submit();
      }
    });
  });

  // --- Initialize existing items for edit mode ---
  document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi cache bahan baku untuk dropdown satuan dinamis
    initializeBahanBakuCache();
    
    // Konversi jumlah tersimpan ke jumlah asli input user
    convertStoredQuantitiesToOriginalInput();
    
    // Hitung jumlah diskon dari data existing
    const diskonPersen = parseFloat(document.querySelector('[name="diskon_persen"]').value) || 0;
    const subtotal = getSubtotalItemSetelahDiskonPerItem();
    const jumlahDiskon = Math.round(subtotal * (diskonPersen / 100));
    document.querySelector('[name="jumlah_diskon"]').value = PembelianHelper.formatNumber(jumlahDiskon);
    
    // Update ringkasan biaya saat halaman dimuat
    updateRingkasanBiaya();
    
    // Hitung jatuh tempo hari jika ada tanggal jatuh tempo
    const tanggalPembelian = document.getElementById('tanggalPembelian').value;
    const tanggalJatuhTempo = document.getElementById('tanggalJatuhTempo').value;
    if (tanggalPembelian && tanggalJatuhTempo) {
      const tglPembelian = new Date(tanggalPembelian);
      const tglJatuhTempo = new Date(tanggalJatuhTempo);
      const diffTime = tglJatuhTempo - tglPembelian;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      if (diffDays > 0) {
        document.getElementById('jatuhTempoHari').value = diffDays;
      }
    }
  });
  
  // Fungsi untuk mengisi cache bahan baku
  function initializeBahanBakuCache() {
    if (!window.bahanBakuCache) window.bahanBakuCache = {};
    
    // Transformasi data bahan baku yang sudah ada
    if (window.bahanBakuList) {
      window.bahanBakuList.forEach(bahan => {
        window.bahanBakuCache[bahan.id] = {
          id: bahan.id,
          kode: bahan.kode_bahan,
          nama: bahan.nama_bahan,
          harga: bahan.harga_terakhir || 0,
          konversi_satuan: bahan.konversi_satuan_json || [],
          satuan_utama: bahan.satuan_utama || '-'
        };
      });
    }
  }
  
  // Fungsi untuk mengkonversi jumlah tersimpan ke jumlah asli input user
  function convertStoredQuantitiesToOriginalInput() {
    const itemBody = document.getElementById('itemBody');
    const rows = itemBody.querySelectorAll('tr');
    
    rows.forEach(row => {
      // Cek keberadaan input sebelum akses .value
      const inputBahanbaku = row.querySelector('input[name$="[bahanbaku_id]"]');
      const inputSatuan = row.querySelector('input[name$="[satuan]"]');
      const inputJumlah = row.querySelector('input[name$="[jumlah]"]');
      if (!inputBahanbaku || !inputSatuan || !inputJumlah) return;

      const bahanbakuId = inputBahanbaku.value;
      const satuanId = inputSatuan.value;
      const jumlahTersimpan = parseInt(inputJumlah.value) || 0;
      let jumlahAsli = jumlahTersimpan;
      // Jika ada satuan yang dipilih, konversi jumlah tersimpan ke jumlah asli
      if (satuanId && window.bahanBakuCache && window.bahanBakuCache[bahanbakuId]) {
        const bahanBakuData = window.bahanBakuCache[bahanbakuId];
        let konv = bahanBakuData.konversi_satuan;
        if (typeof konv === 'string') {
          try { konv = JSON.parse(konv); } catch { konv = []; }
        }
        if (Array.isArray(konv) && konv.length > 0) {
          for (let k of konv) {
            if (k.satuan_dari && k.jumlah && String(k.satuan_dari) === String(satuanId)) {
              // Konversi: jumlah_asli = jumlah_tersimpan ÷ faktor_konversi
              const faktorKonversi = parseFloat(k.jumlah) || 1;
              jumlahAsli = Math.round(jumlahTersimpan / faktorKonversi);
              break;
            }
          }
        }
      }
      // Update cell jumlah agar selalu ada input hidden
      const jumlahCell = row.querySelector('.item-jumlah');
      if (jumlahCell) {
        jumlahCell.innerHTML = `${jumlahAsli}<input type="hidden" name="items[][jumlah]" value="${jumlahAsli}">`;
      }
      // Konversi ID satuan ke nama satuan untuk tampilan dan pastikan input hidden tetap ada
      if (satuanId && window.satuanList) {
        const satuanFound = window.satuanList.find(s => String(s.id) === String(satuanId));
        if (satuanFound) {
          const satuanCell = row.querySelector('.item-satuan');
          if (satuanCell) {
            satuanCell.innerHTML = `${satuanFound.nama_sub_detail_parameter}<input type="hidden" name="items[][satuan]" value="${satuanId}">`;
          }
        }
      }
    });
    reindexItemInputs();
  }

  // Helper: mapping id satuan ke nama
  function getNamaSatuanById(id) {
    if (!window.satuanList) return id;
    const found = window.satuanList.find(s => s.id == id || s.id == String(id) || String(s.id) == String(id));
    return found ? found.nama_sub_detail_parameter : id;
  }

  function updateKonversiSatuanInfo(data) {
    const konversiInfo = document.getElementById('konversiSatuanInfo');
    konversiInfo.style.display = 'none';
    konversiInfo.innerHTML = '';
    let konv = data.konversi_satuan;
    if (typeof konv === 'string') {
      try { konv = JSON.parse(konv); } catch { konv = []; }
    }
    if (Array.isArray(konv) && konv.length > 0) {
      let html = '<div class="small text-muted">Konversi Satuan:</div><div class="mb-1">';
      const satuanUtama = data.satuan_utama || data.satuan || '-';
      const hargaUtama = window.hargaSatuanUtama || 0;
      let arrKonversi = [];
      konv.forEach((k, idx) => {
        if (k.satuan_dari && k.jumlah) {
          const namaKonversi = getNamaSatuanById(k.satuan_dari);
          const hargaKonversi = k.jumlah > 0 ? Math.round(hargaUtama * k.jumlah) : 0;
          if (idx === 0) {
            arrKonversi.push(`1 ${namaKonversi} (Rp ${PembelianHelper.formatNumber(hargaKonversi)}/${namaKonversi})`);
          } else {
            arrKonversi.push(`1 ${namaKonversi} = ${k.jumlah} ${satuanUtama} (Rp ${PembelianHelper.formatNumber(hargaKonversi)}/${namaKonversi})`);
          }
        }
      });
      html += arrKonversi.join(' &nbsp;|&nbsp; ');
      html += '</div>';
      konversiInfo.innerHTML = html;
      konversiInfo.style.display = '';
    }
  }
})(); 