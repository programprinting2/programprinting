/**
 * File: production-cost-handler.js
 * Deskripsi: Menangani perhitungan biaya produksi untuk mesin pada form
 */

$(document).ready(function() {
    // ===== Biaya Tinta =====
    // Update kalkulasi saat input berubah di form tambah
    $('#harga_tinta_per_liter, #konsumsi_tinta_per_m2').on('input', function() {
        hitungBiayaProduksi();
    });
    
    // Update kalkulasi saat input berubah di form edit
    $('input[id^="harga_tinta_per_liter"], input[id^="konsumsi_tinta_per_m2"]').on('input', function() {
        // Cek apakah ini form edit (id memiliki angka di akhir)
        var id = $(this).attr('id').match(/\d+$/);
        if (id) {
            hitungBiayaProduksiEdit(id[0]);
        }
    });
    
    // ===== Biaya Tambahan =====
    // Tambah biaya tambahan pada form tambah
    $('#tambah_biaya').click(function() {
        tambahBiayaTambahan('#biaya_tambahan_container', null);
    });
    
    // Tambah biaya tambahan pada form edit
    $('.tambah-biaya-edit').click(function() {
        var id = $(this).data('id');
        tambahBiayaTambahan('#edit_biaya_tambahan_container' + id, id);
    });
    
    // ===== Function Handlers =====
    // Fungsi untuk menambah form biaya tambahan
    function tambahBiayaTambahan(containerId, mesinId) {
        var rowId = 'biaya_row_' + Math.floor(Math.random() * 1000);
        var newBiaya = `
            <div class="card mb-2 biaya-item" id="${rowId}" ${mesinId ? 'data-mesin-id="' + mesinId + '"' : ''}>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Nama Biaya</label>
                            <input type="text" class="form-control biaya-nama" placeholder="Contoh: Biaya Operator">
                        </div>
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Nilai (Rp/m²)</label>
                            <input type="number" class="form-control biaya-nilai" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-2 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-biaya" data-row="${rowId}">
                                <i data-feather="trash-2" class="icon-sm"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Sembunyikan pesan "belum ada biaya"
        $(containerId + ' #no_biaya_message').hide();
        
        // Tambahkan form biaya
        $(containerId).append(newBiaya);
        feather.replace();
        
        // Setup tombol hapus
        $('.remove-biaya').off('click').on('click', function() {
            var rowId = $(this).data('row');
            var mesinId = $('#' + rowId).data('mesin-id');
            
            $('#' + rowId).remove();
            
            // Jika tidak ada biaya lagi, tampilkan pesan
            if ($(containerId + ' .biaya-item').length === 0) {
                $(containerId + ' #no_biaya_message').show();
            }
            
            // Update kalkulasi
            if (mesinId) {
                hitungBiayaProduksiEdit(mesinId);
            } else {
                hitungBiayaProduksi();
            }
        });
        
        // Tambahkan event listener untuk update kalkulasi saat nilai berubah
        $('.biaya-item#' + rowId + ' .biaya-nilai').on('input', function() {
            if (mesinId) {
                hitungBiayaProduksiEdit(mesinId);
            } else {
                hitungBiayaProduksi();
            }
        });
        
        // Update kalkulasi
        if (mesinId) {
            hitungBiayaProduksiEdit(mesinId);
        } else {
            hitungBiayaProduksi();
        }
    }
    
    // Fungsi untuk menghitung biaya produksi di form tambah
    function hitungBiayaProduksi() {
        var hargaTinta = parseFloat($('#harga_tinta_per_liter').val()) || 0;
        var konsumsiTinta = parseFloat($('#konsumsi_tinta_per_m2').val()) || 0;
        
        // Hitung biaya tinta per m²
        var biayaTintaPerM2 = 0;
        if (hargaTinta > 0 && konsumsiTinta > 0) {
            // Konversi mL ke L (1L = 1000mL)
            var konsumsiLiter = konsumsiTinta / 1000;
            biayaTintaPerM2 = hargaTinta * konsumsiLiter;
        }
        
        // Hitung total biaya tambahan
        var biayaTambahanPerM2 = 0;
        $('#biaya_tambahan_container .biaya-nilai').each(function() {
            var nilai = parseFloat($(this).val()) || 0;
            biayaTambahanPerM2 += nilai;
        });
        
        // Hitung total biaya
        var totalBiayaPerM2 = biayaTintaPerM2 + biayaTambahanPerM2;
        var totalBiayaPerCm2 = totalBiayaPerM2 / 10000; // 1m² = 10000cm²
        
        // Update tampilan
        $('#biaya_tinta_per_m2').text('Rp ' + formatRupiah(biayaTintaPerM2));
        $('#biaya_tambahan_per_m2').text('Rp ' + formatRupiah(biayaTambahanPerM2));
        $('#total_biaya_per_m2').text('Rp ' + formatRupiah(totalBiayaPerM2));
        $('#total_biaya_per_cm2').text('Rp ' + formatRupiah(totalBiayaPerCm2));
        
        // Update total biaya tambahan
        if (biayaTambahanPerM2 > 0) {
            $('#total_biaya_tambahan').text('Rp ' + formatRupiah(biayaTambahanPerM2));
            $('#total_biaya_tambahan_container').show();
        } else {
            $('#total_biaya_tambahan_container').hide();
        }
    }
    
    // Fungsi untuk menghitung biaya produksi di form edit
    function hitungBiayaProduksiEdit(id) {
        var hargaTinta = parseFloat($('#harga_tinta_per_liter' + id).val()) || 0;
        var konsumsiTinta = parseFloat($('#konsumsi_tinta_per_m2' + id).val()) || 0;
        
        // Hitung biaya tinta per m²
        var biayaTintaPerM2 = 0;
        if (hargaTinta > 0 && konsumsiTinta > 0) {
            // Konversi mL ke L (1L = 1000mL)
            var konsumsiLiter = konsumsiTinta / 1000;
            biayaTintaPerM2 = hargaTinta * konsumsiLiter;
        }
        
        // Hitung total biaya tambahan
        var biayaTambahanPerM2 = 0;
        $('#edit_biaya_tambahan_container' + id + ' .biaya-nilai').each(function() {
            var nilai = parseFloat($(this).val()) || 0;
            biayaTambahanPerM2 += nilai;
        });
        
        // Hitung total biaya
        var totalBiayaPerM2 = biayaTintaPerM2 + biayaTambahanPerM2;
        var totalBiayaPerCm2 = totalBiayaPerM2 / 10000; // 1m² = 10000cm²
        
        // Update tampilan
        $('#biaya_tinta_per_m2' + id).text('Rp ' + formatRupiah(biayaTintaPerM2));
        $('#biaya_tambahan_per_m2' + id).text('Rp ' + formatRupiah(biayaTambahanPerM2));
        $('#total_biaya_per_m2' + id).text('Rp ' + formatRupiah(totalBiayaPerM2));
        $('#total_biaya_per_cm2' + id).text('Rp ' + formatRupiah(totalBiayaPerCm2));
        
        // Update total biaya tambahan
        $('#total_biaya_tambahan' + id).text('Rp ' + formatRupiah(biayaTambahanPerM2));
    }
    
    // Format angka sebagai string dengan format rupiah
    function formatRupiah(angka) {
        return angka.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // ===== Inisialisasi =====
    // Jalankan perhitungan awal untuk form tambah
    hitungBiayaProduksi();
    
    // Jalankan perhitungan awal untuk setiap form edit
    $('.edit-biaya-produksi-tab').each(function() {
        var id = $(this).data('mesin-id');
        if (id) {
            hitungBiayaProduksiEdit(id);
        }
    });
    
    // Setup event listener untuk nilai biaya pada form edit yang sudah ada
    $('.biaya-item[data-mesin-id] .biaya-nilai').on('input', function() {
        var mesinId = $(this).closest('.biaya-item').data('mesin-id');
        if (mesinId) {
            hitungBiayaProduksiEdit(mesinId);
        }
    });
}); 