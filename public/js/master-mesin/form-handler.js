$(document).ready(function() {
    // Tambah spesifikasi dinamis (untuk modal tambah)
    $('#tambah_detail').click(function() {
        tambahDetailBaris('#detail_mesin_container');
        $('#no_specs_message').hide();
    });
    
    // Tambah spesifikasi dinamis (untuk modal edit)
    $('.tambah-detail-edit').click(function() {
        var id = $(this).data('id');
        tambahDetailBaris('#edit_detail_mesin_container' + id);
        $('#edit_no_specs_message' + id).hide();
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
                        <i data-feather="trash-2" class="icon-sm"></i>
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
            
            // Cek apakah masih ada detail yang tersisa
            var container = $(this).closest('#detail_mesin_container, [id^="edit_detail_mesin_container"]');
            if (container.find('.detail-item').length === 0) {
                // Jika tidak ada detail yang tersisa, tampilkan pesan kosong
                if (container.attr('id') === 'detail_mesin_container') {
                    $('#no_specs_message').show();
                } else {
                    var id = container.attr('id').replace('edit_detail_mesin_container', '');
                    $('#edit_no_specs_message' + id).show();
                }
            }
        });
    }
    
    // Inisialisasi setup tombol hapus
    setupRemoveDetailButton();
    
    // Form submit handling untuk tambah mesin
    $('#submitFormMesin').click(function() {
        // Kumpulkan detail mesin 
        var detailMesin = kumpulkanDetailMesin('formTambahMesin');
        $('#detail_mesin_json').val(JSON.stringify(detailMesin));
        
        // Submit form
        $('#formTambahMesin').submit();
    });
    
    // Form submit handling untuk edit mesin
    $('.btn-submit-edit').click(function() {
        var id = $(this).data('id');
        
        // Kumpulkan detail mesin
        var detailMesin = kumpulkanDetailMesin('formEditMesin' + id);
        $('#edit_detail_mesin_json' + id).val(JSON.stringify(detailMesin));
        
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
        
        return detailMesin;
    }
    
    // Fungsi untuk mengumpulkan data biaya tambahan
    function kumpulkanBiayaTambahan(containerId) {
        var biayaTambahan = [];
        
        $(containerId + ' .biaya-item').each(function() {
            var nama = $(this).find('.biaya-nama').val()?.trim();
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
    
    // Saat modal edit mesin dibuka, cek dan atur pesan kosong spesifikasi
    $(document).on('shown.bs.modal', function (e) {
        if ($(e.target).is('[id^="editMesinModal"]')) {
            var mesinId = $(e.target).attr('id').replace('editMesinModal', '');
            var container = $('#edit_detail_mesin_container' + mesinId);
            var noSpecsMsg = $('#edit_no_specs_message' + mesinId);
            // Hapus duplikasi baris spesifikasi jika ada (hanya biarkan baris dari blade atau hasil load json)
            // (Jika ingin lebih advance, bisa clear dan render ulang dari JSON, tapi untuk sekarang cukup pastikan tidak ada duplikasi)
            if (container.find('.detail-item').length > 0) {
                noSpecsMsg.hide();
            } else {
                noSpecsMsg.show();
            }
        }
    });
}); 