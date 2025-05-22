$(document).ready(function() {
    // Tambah spesifikasi dinamis (untuk modal tambah)
    $('#tambah_detail').click(function() {
        tambahDetailBaris('#detail_mesin_container');
    });
    
    // Tambah spesifikasi dinamis (untuk modal edit)
    $('.tambah-detail-edit').click(function() {
        var id = $(this).data('id');
        tambahDetailBaris('#edit_detail_mesin_container' + id);
    });
    
    // Fungsi untuk menambah baris detail
    function tambahDetailBaris(containerId) {
        var newDetail = `
            <div class="row mb-2 detail-item">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="detail_nama[]" placeholder="Nama Spesifikasi">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="detail_nilai[]" placeholder="Nilai">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="detail_satuan[]" placeholder="Satuan">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-detail">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
        `;
        $(containerId).append(newDetail);
        feather.replace();
        
        // Tambahkan event listener untuk tombol hapus
        setupRemoveDetailButton();
    }
    
    // Fungsi untuk setup tombol hapus detail
    function setupRemoveDetailButton() {
        $('.remove-detail').off('click').on('click', function() {
            $(this).closest('.detail-item').remove();
        });
    }
    
    // Inisialisasi setup tombol hapus
    setupRemoveDetailButton();
    
    // Perubahan tipe mesin untuk tambah
    $('#tipe_mesin').change(function() {
        var tipeMesin = $(this).val();
        var printerSpecs = $('#printer-specs');
        
        // Reset container spesifikasi
        printerSpecs.html('');
        
        if (tipeMesin === 'Printer Large Format' || tipeMesin === 'Digital Printer A3+') {
            // Tambahkan field untuk printer
            printerSpecs.html(`
                <div class="col-md-6 mb-3">
                    <label for="lebar_media_maksimum" class="form-label">Lebar Media Maksimum (cm)</label>
                    <input type="number" step="0.1" class="form-control" id="lebar_media_maksimum" name="lebar_media_maksimum" placeholder="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="resolusi" class="form-label">Resolusi Default</label>
                    <input type="text" class="form-control" id="resolusi" name="resolusi" placeholder="contoh: 1440 x 1440 dpi">
                </div>
            `);
        } else if (tipeMesin === 'Mesin Finishing') {
            // Tambahkan field untuk mesin finishing
            printerSpecs.html(`
                <div class="col-md-6 mb-3">
                    <label for="dimensi" class="form-label">Dimensi Mesin (cm)</label>
                    <input type="text" class="form-control" id="dimensi" name="dimensi" placeholder="P x L x T">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="daya_listrik" class="form-label">Daya Listrik</label>
                    <input type="text" class="form-control" id="daya_listrik" name="daya_listrik" placeholder="contoh: 220V/380V">
                </div>
            `);
        }
    });
    
    // Form submit handling untuk tambah mesin
    $('#submitFormMesin').click(function() {
        // Kumpulkan detail mesin 
        var detailMesin = kumpulkanDetailMesin('formTambahMesin');
        $('#detail_mesin_json').val(JSON.stringify(detailMesin));
        
        // Kumpulkan biaya tambahan
        var biayaTambahan = kumpulkanBiayaTambahan('#biaya_tambahan_container');
        $('#biaya_tambahan_json').val(JSON.stringify(biayaTambahan));
        
        // Submit form
        $('#formTambahMesin').submit();
    });
    
    // Form submit handling untuk edit mesin
    $('.btn-submit-edit').click(function() {
        var id = $(this).data('id');
        
        // Kumpulkan detail mesin
        var detailMesin = kumpulkanDetailMesin('formEditMesin' + id);
        $('#edit_detail_mesin_json' + id).val(JSON.stringify(detailMesin));
        
        // Kumpulkan biaya tambahan
        var biayaTambahan = kumpulkanBiayaTambahan('#edit_biaya_tambahan_container' + id);
        $('#edit_biaya_tambahan_json' + id).val(JSON.stringify(biayaTambahan));
        
        // Submit form
        $('#formEditMesin' + id).submit();
    });
    
    // Fungsi untuk mengumpulkan detail mesin
    function kumpulkanDetailMesin(formId) {
        var form = $('#' + formId);
        var detailMesin = [];
        
        // Kumpulkan semua detail mesin dari form
        form.find('.detail-item').each(function() {
            var nama = $(this).find('input[name="detail_nama[]"]').val();
            var nilai = $(this).find('input[name="detail_nilai[]"]').val();
            var satuan = $(this).find('input[name="detail_satuan[]"]').val();
            
            // Tambahkan hanya jika nama dan nilai diisi
            if (nama && nilai) {
                detailMesin.push({
                    nama: nama,
                    nilai: nilai,
                    satuan: satuan || ''
                });
            }
        });
        
        // Tambahkan field resolusi, dimensi, dan daya_listrik ke detail mesin jika ada
        form.find('input[name="resolusi"]').each(function() {
            if ($(this).val()) {
                detailMesin.push({
                    nama: 'Resolusi',
                    nilai: $(this).val(),
                    satuan: 'dpi'
                });
            }
        });
        
        form.find('input[name="dimensi"]').each(function() {
            if ($(this).val()) {
                detailMesin.push({
                    nama: 'Dimensi',
                    nilai: $(this).val(),
                    satuan: 'cm'
                });
            }
        });
        
        form.find('input[name="daya_listrik"]').each(function() {
            if ($(this).val()) {
                detailMesin.push({
                    nama: 'Daya Listrik',
                    nilai: $(this).val(),
                    satuan: ''
                });
            }
        });
        
        return detailMesin;
    }
    
    // Fungsi untuk mengumpulkan data biaya tambahan
    function kumpulkanBiayaTambahan(containerId) {
        var biayaTambahan = [];
        
        $(containerId + ' .biaya-item').each(function() {
            var nama = $(this).find('.biaya-nama').val();
            var nilai = parseFloat($(this).find('.biaya-nilai').val()) || 0;
            
            if (nama && nilai > 0) {
                biayaTambahan.push({
                    nama: nama,
                    nilai: nilai
                });
            }
        });
        
        return biayaTambahan;
    }
    
    // Fungsi untuk preview gambar
    window.previewImage = function(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
}); 