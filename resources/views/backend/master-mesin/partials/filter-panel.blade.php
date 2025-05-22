<div class="card">
    <div class="card-body">
        <h5>Filter</h5>
        <div class="mb-3">
            <label class="form-label">Cari Mesin</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Nama, model, merek...">
        </div>
        <div class="mb-3">
            <label class="form-label">Tipe Mesin</label>
            <div class="form-check">
                <input class="form-check-input filter-type" type="radio" name="tipe_mesin" id="semua_tipe" value="semua" checked>
                <label class="form-check-label" for="semua_tipe">
                    Semua Tipe
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input filter-type" type="radio" name="tipe_mesin" id="printer_large_format" value="Printer Large Format">
                <label class="form-check-label" for="printer_large_format">
                    Printer Large Format
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input filter-type" type="radio" name="tipe_mesin" id="digital_printer_a3" value="Digital Printer A3+">
                <label class="form-check-label" for="digital_printer_a3">
                    Digital Printer A3+
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input filter-type" type="radio" name="tipe_mesin" id="mesin_finishing" value="Mesin Finishing">
                <label class="form-check-label" for="mesin_finishing">
                    Mesin Finishing
                </label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select filter-status" id="filterStatus">
                <option value="semua">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Maintenance">Maintenance</option>
                <option value="Rusak">Rusak</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
        </div>
    </div>
</div> 