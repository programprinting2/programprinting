$(document).ready(function() {
    // Perubahan tipe mesin untuk edit
    $('.edit-tipe-mesin').change(function() {
        var tipeMesin = $(this).val();
        var itemId = $(this).data('id');
        var printerSpecs = $('#edit_printer_specs' + itemId);
        var currentLebarMedia = $('#edit_lebar_media_maksimum' + itemId).val() || '';
        
        // Reset container spesifikasi
        printerSpecs.html('');
        
        if (tipeMesin === 'Printer Large Format' || tipeMesin === 'Digital Printer A3+') {
            // Tambahkan field untuk printer
            printerSpecs.html(`
                <div class="col-md-12 mb-3">
                    <label for="edit_lebar_media_maksimum${itemId}" class="form-label">Lebar Media Maksimum (cm)</label>
                    <input type="number" step="0.1" class="form-control" id="edit_lebar_media_maksimum${itemId}" name="lebar_media_maksimum" value="${currentLebarMedia}" placeholder="0">
                </div>
            `);
        } else if (tipeMesin === 'Mesin Finishing') {
            // Tambahkan field untuk mesin finishing
            printerSpecs.html(`
                <div class="col-md-6 mb-3">
                    <label for="edit_dimensi${itemId}" class="form-label">Dimensi Mesin (cm)</label>
                    <input type="text" class="form-control" id="edit_dimensi${itemId}" name="dimensi" value="${getDetailValue(itemId, 'Dimensi')}" placeholder="P x L x T">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_daya_listrik${itemId}" class="form-label">Daya Listrik</label>
                    <input type="text" class="form-control" id="edit_daya_listrik${itemId}" name="daya_listrik" value="${getDetailValue(itemId, 'Daya Listrik')}" placeholder="contoh: 220V/380V">
                </div>
            `);
        }
    });
    
    // Fungsi helper untuk mendapatkan nilai detail
    function getDetailValue(itemId, detailName) {
        var detailMesin = $('#edit_detail_mesin_json' + itemId).val();
        if (detailMesin) {
            try {
                var details = JSON.parse(detailMesin);
                var detail = details.find(d => d.nama === detailName);
                return detail ? detail.nilai : '';
            } catch (e) {
                console.error('Error parsing detail_mesin_json:', e);
                return '';
            }
        }
        return '';
    }
}); 