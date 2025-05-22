$(document).ready(function() {
    // Filter pencarian
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.mesin-item').filter(function() {
            var name = $(this).data('name').toLowerCase();
            var brand = $(this).data('brand') ? $(this).data('brand').toLowerCase() : '';
            var model = $(this).data('model') ? $(this).data('model').toLowerCase() : '';
            var matches = name.indexOf(value) > -1 || brand.indexOf(value) > -1 || model.indexOf(value) > -1;
            
            $(this).toggle(matches);
        });
        
        // Reinisialisasi ikon status setelah pencarian
        setTimeout(function() {
            feather.replace('.icon-status', {
                width: 14,
                height: 14
            });
        }, 100);
    });
    
    // Filter tipe mesin
    $('.filter-type').change(function() {
        var value = $(this).val();
        
        if (value === 'semua') {
            $('.mesin-item').show();
        } else {
            $('.mesin-item').hide();
            $('.mesin-item').filter(function() {
                return $(this).data('type') === value;
            }).show();
        }
        
        // Reinisialisasi ikon status setelah filter
        setTimeout(function() {
            feather.replace('.icon-status', {
                width: 14,
                height: 14
            });
        }, 100);
    });
    
    // Filter status
    $('#filterStatus').change(function() {
        var value = $(this).val();
        
        if (value === 'semua') {
            $('.mesin-item').show();
        } else {
            $('.mesin-item').hide();
            $('.mesin-item').filter(function() {
                return $(this).data('status') === value;
            }).show();
        }
        
        // Reinisialisasi ikon status setelah filter
        setTimeout(function() {
            feather.replace('.icon-status', {
                width: 14,
                height: 14
            });
        }, 100);
    });
}); 