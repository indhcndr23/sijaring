document.addEventListener("DOMContentLoaded", () => {
    const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
    const baseUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

    const masterBarang = window.masterBarangList || [];
    let mapPicker = null;
    let markerPicker = null;

    // Master Model Resmi Sesuai Spreadsheet Diskominfo
    const masterModelKatalog = {
        'access point': [
            { label: 'RAP2200(E) (Low-end)', val: 'RAP2200(E)' },
            { label: 'RAP2200(F) (Low-end)', val: 'RAP2200(F)' },
            { label: 'RAP2260(G) (Mid-end)', val: 'RAP2260(G)' },
            { label: 'RAP2266 (Mid-end)', val: 'RAP2266' },
            { label: 'AP720-L (Mid-end)', val: 'AP720-L' },
            { label: 'AP680(CD) Outdoor (High-end)', val: 'AP680(CD)' },
            { label: 'AP680-L Outdoor (High-end)', val: 'AP680-L' }
        ],
        'router': [
            { label: 'RB750Gr3 / hEX (Low-end)', val: 'RB750Gr3' },
            { label: 'RB760iGS / hEX S (Low-end)', val: 'RB760iGS' },
            { label: 'RB450Gx4 (Low-end)', val: 'RB450Gx4' },
            { label: 'RB962UiGS / hAP ac (Low-end)', val: 'RB962UiGS' },
            { label: 'L009UiGS-2Hax (Mid-end)', val: 'L009UiGS' },
            { label: 'RB3011UiAS (Mid-end)', val: 'RB3011UiAS' },
            { label: 'RB1100x4 (Mid-end)', val: 'RB1100x4' },
            { label: 'CCR2116-12G-4S+ (High-end)', val: 'CCR2116' },
            { label: 'CCR1016-12S (High-end)', val: 'CCR1016' },
            { label: 'CCR2004-1G-12S+ (High-end)', val: 'CCR2004' },
            { label: 'CCR1009-8G-1S (High-end)', val: 'CCR1009' }
        ],
        'switch': [
            { label: 'CRS106-1C-5S (Low-end)', val: 'CRS106-1C-5S' },
            { label: 'CRS125-24G-1S (Low-end)', val: 'CRS125-24G-1S' },
            { label: 'CRS326-24G-2S+ (Mid-end)', val: 'CRS326-24G-2S+' },
            { label: 'CRS354-48G-4S+ (High-end)', val: 'CRS354-48G-4S+' },
            { label: 'CRS328-24P-4S+RM (High-end)', val: 'CRS328-24P' },
            { label: 'CRS317-1G-16S+RM (High-end)', val: 'CRS317-1G' }
        ],
        'kabel': [
            { label: 'UTP Cat 5 (Low-end)', val: 'UTP Cat 5' },
            { label: 'UTP Cat 5e (Mid-end)', val: 'UTP Cat 5e' },
            { label: 'UTP Cat 6 (High-end)', val: 'UTP Cat 6' },
            { label: 'UTP Cat 6A (High-end)', val: 'UTP Cat 6A' }
        ]
    };

    const getKatalogOptions = (namaBarang, selectedVal = '') => {
        const nb = String(namaBarang).toLowerCase();
        let key = null;
        if (nb.includes('access point') || nb.includes('ap')) key = 'access point';
        else if (nb.includes('router')) key = 'router';
        else if (nb.includes('switch')) key = 'switch';
        else if (nb.includes('kabel')) key = 'kabel';

        if (!key) return null;

        let html = '<option value="">-- Pilih Tipe / Model --</option>';
        masterModelKatalog[key].forEach(item => {
            const sel = (selectedVal === item.val) ? 'selected' : '';
            html += `<option value="${item.val}" ${sel}>${item.label}</option>`;
        });
        return html;
    };

    // 1. Inisialisasi Peta Leaflet untuk Profil OPD
    const initMapPicker = (lat = -7.4285, lng = 110.2169) => {
        if (!mapPicker) {
            mapPicker = L.map('map-picker-opd').setView([lat, lng], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(mapPicker);

            markerPicker = L.marker([lat, lng], { draggable: true }).addTo(mapPicker);

            markerPicker.on('dragend', function () {
                const position = markerPicker.getLatLng();
                $('#field_latitude').val(position.lat.toFixed(7)).trigger('change');
                $('#field_longitude').val(position.lng.toFixed(7)).trigger('change');
            });
        } else {
            mapPicker.setView([lat, lng], 13);
            markerPicker.setLatLng([lat, lng]);
        }
        setTimeout(() => { mapPicker.invalidateSize(); }, 300);
    };

    // Fix ukuran peta ketika tab berganti
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.getAttribute('data-bs-target') === '#tab-info-umum' && mapPicker) {
            mapPicker.invalidateSize();
        }
    });

    // Cari lokasi di Peta
    $('#btn-cari-map').on('click', async function () {
        const query = $('#input-cari-map').val();
        if (!query) return;

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ' Magelang')}`);
            const results = await res.json();

            if (results && results.length > 0) {
                const first = results[0];
                const lat = parseFloat(first.lat);
                const lng = parseFloat(first.lon);

                mapPicker.setView([lat, lng], 16);
                markerPicker.setLatLng([lat, lng]);

                $('#field_latitude').val(lat.toFixed(7)).trigger('change');
                $('#field_longitude').val(lng.toFixed(7)).trigger('change');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pencarian Gagal',
                    text: 'Lokasi tidak ditemukan!',
                    confirmButtonColor: '#237845'
                });
            }
        } catch (err) {
            console.error('Gagal cari lokasi:', err);
        }
    });

    // 2. Pilih OPD
    $('#select-opd-survei').on('change', function () {
        const idOpd = $(this).val();
        if (!idOpd) {
            $('#wrapper-survei-form').addClass('d-none');
            return;
        }

        $('#wrapper-survei-form').removeClass('d-none');
        loadDataSurvei(idOpd);
    });

    // 3. Load Detail Data Survei
    const loadDataSurvei = async (idOpd) => {
        try {
            const res = await fetch(`${baseUrl}input/data-awal/detail/${idOpd}`);
            const data = await res.json();

            if (data.status === 'success') {
                const opd = data.opd || {};
                const barangList = data.barang || [];

                $('#field_nama_opd').val(opd.nama_opd || '');
                $('#field_alamat').val(opd.alamat || '');
                $('#field_nama_pic').val(opd.nama_pic || '');
                $('#field_no_hp_pic').val(opd.no_hp_pic || '');
                $('#field_jumlah_pegawai').val(opd.jumlah_pegawai ?? 0);
                $('#field_rata_tamu').val(opd.rata_tamu ?? 0);
                $('#field_luas_ruangan').val(opd.luas_ruangan ?? 0);
                $('#field_jumlah_lantai').val(opd.jumlah_lantai ?? 0);
                $('#field_jumlah_ruangan').val(opd.jumlah_ruangan ?? 0);
                $('#field_jenis_dinding').val(opd.jenis_dinding || '');
                
                $(`input[name="jenis_kabel"]`).prop('checked', false);
                if (opd.jenis_kabel && opd.jenis_kabel !== '-') {
                    $(`input[name="jenis_kabel"][value="${opd.jenis_kabel}"]`).prop('checked', true);
                }

                const defaultLat = parseFloat(opd.latitude) || -7.4285;
                const defaultLng = parseFloat(opd.longitude) || 110.2169;

                $('#field_latitude').val(opd.latitude || '');
                $('#field_longitude').val(opd.longitude || '');

                initMapPicker(defaultLat, defaultLng);
                renderBlockBarangPerKategori(barangList);
            }
        } catch (err) {
            console.error('Gagal memuat data survei:', err);
        }
    };

    // 4. Render Blok Kartu per Jenis Barang
    const renderBlockBarangPerKategori = (userBarangList = []) => {
        const container = $('#container-kategori-perangkat');
        container.empty();

        if (masterBarang.length === 0) {
            container.html('<div class="alert alert-warning">Master barang belum tersedia.</div>');
            return;
        }

        masterBarang.forEach(mb => {
            const isSwitch = mb.jenis_barang.toLowerCase().includes('switch');
            const filteredBarang = userBarangList.filter(b => parseInt(b.id_barang) === parseInt(mb.id_barang));
            const colCount = isSwitch ? 8 : 7;

            let rowsHtml = '';
            if (filteredBarang.length === 0) {
                rowsHtml = `<tr class="empty-row"><td colspan="${colCount}" class="text-center text-muted py-3 small">Belum ada data ${mb.jenis_barang}. Klik '+ Tambah'.</td></tr>`;
            } else {
                filteredBarang.forEach(b => {
                    rowsHtml += createRowBarangHtml(mb.id_barang, mb.jenis_barang, b);
                });
            }

            const cardHtml = `
            <div class="card p-4 shadow-sm border-0 mb-4 rounded-3" data-id-barang="${mb.id_barang}">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0 fs-6">
                        <i class="bx bx-cube me-1 text-success"></i> Aset: ${mb.jenis_barang}
                    </h6>
                    <button class="btn btn-sm btn-success btn-tambah-per-jenis px-3" data-id-barang="${mb.id_barang}" data-nama-barang="${mb.jenis_barang}" type="button">
                        <i class="bx bx-plus me-1"></i> Tambah
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom-green align-middle mb-0" id="table-jenis-${mb.id_barang}">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Model / Tipe Perangkat</th>
                                ${isSwitch ? '<th style="width: 10%;">Jumlah Port</th>' : ''}
                                <th style="width: 10%;">Tahun</th>
                                <th style="width: 12%;">Kondisi</th>
                                <th style="width: 12%;">Lat (Titik Barang)</th>
                                <th style="width: 12%;">Lng (Titik Barang)</th>
                                <th>Keterangan</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="tbody-jenis" data-id-barang="${mb.id_barang}">
                            ${rowsHtml}
                        </tbody>
                    </table>
                </div>
            </div>`;

            container.append(cardHtml);
        });
    };

    const createRowBarangHtml = (idBarang, namaBarang, b = {}) => {
        const isSwitch = String(namaBarang).toLowerCase().includes('switch');
        const katalogOptions = getKatalogOptions(namaBarang, b.merk || '');

        const modelInputHtml = katalogOptions
            ? `<select class="form-select form-select-sm input-save-barang f-merk">${katalogOptions}</select>`
            : `<input type="text" class="form-control form-control-sm input-save-barang f-merk" value="${b.merk || ''}" placeholder="Merk / Tipe">`;

        return `
        <tr data-id-opd-barang="${b.id_opd_barang || ''}" data-id-barang="${idBarang}" data-nama-barang="${namaBarang}">
            <td>${modelInputHtml}</td>
            ${isSwitch ? `<td><input type="number" min="0" class="form-control form-control-sm input-save-barang f-port" value="${b.jumlah_port ?? ''}" placeholder="24"></td>` : ''}
            <td><input type="number" class="form-control form-control-sm input-save-barang f-tahun" value="${b.tahun || ''}" placeholder="Tahun"></td>
            <td>
                <select class="form-select form-select-sm input-save-barang f-kondisi">
                    <option value="Baik" ${b.kondisi === 'Baik' ? 'selected' : ''}>Baik</option>
                    <option value="Rusak Ringan" ${b.kondisi === 'Rusak Ringan' ? 'selected' : ''}>Rusak Ringan</option>
                    <option value="Rusak Berat" ${b.kondisi === 'Rusak Berat' ? 'selected' : ''}>Rusak Berat</option>
                </select>
            </td>
            <td><input type="text" class="form-control form-control-sm input-save-barang f-lat" value="${b.latitude || ''}" placeholder="-7.xxxx"></td>
            <td><input type="text" class="form-control form-control-sm input-save-barang f-lng" value="${b.longitude || ''}" placeholder="110.xxxx"></td>
            <td><input type="text" class="form-control form-control-sm input-save-barang f-keterangan" value="${b.keterangan || ''}" placeholder="Keterangan"></td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-sm btn-warning p-1 px-2 btn-edit-barang" type="button" title="Simpan / Edit"><i class="bx bx-edit"></i></button>
                    <button class="btn btn-sm btn-danger p-1 px-2 btn-hapus-barang" type="button" title="Hapus"><i class="bx bx-trash"></i></button>
                </div>
            </td>
        </tr>`;
    };

    // 5. Auto-Save Blok I
    $(document).on('change blur', '.auto-save-opd', function () {
        const idOpd = $('#select-opd-survei').val();
        const field = $(this).data('field');
        const value = $(this).val();

        if (!idOpd || !field) return;

        $.post(`${baseUrl}input/data-awal/save-info-opd`, {
            id_opd: idOpd,
            field: field,
            value: value
        }, () => {
            triggerSaveBadge();
        });
    });

    $(document).on('change', '.auto-save-opd-radio', function () {
        const idOpd = $('#select-opd-survei').val();
        const field = $(this).attr('name');
        const value = $(this).val();

        if (!idOpd || !field) return;

        $.post(`${baseUrl}input/data-awal/save-info-opd`, {
            id_opd: idOpd,
            field: field,
            value: value
        }, () => {
            triggerSaveBadge();
        });
    });

    const triggerSaveBadge = () => {
        $('#badge-autosave').removeClass('d-none');
        setTimeout(() => { $('#badge-autosave').addClass('d-none'); }, 2000);
    };

    // 6. Tambah Baris Perangkat
    $(document).on('click', '.btn-tambah-per-jenis', function () {
        const idBarang = $(this).data('id-barang');
        const namaBarang = $(this).data('nama-barang');
        const tbody = $(`.tbody-jenis[data-id-barang="${idBarang}"]`);

        tbody.find('.empty-row').remove();
        tbody.append(createRowBarangHtml(idBarang, namaBarang));
    });

    // 7. Simpan Baris Perangkat
    const buildPayloadBarang = (tr, idOpd) => {
        return {
            id_opd_barang: tr.attr('data-id-opd-barang') || '',
            id_opd: idOpd,
            id_barang: tr.attr('data-id-barang'),
            jumlah: 1, // 1 baris = 1 unit
            jumlah_port: tr.find('.f-port').length ? tr.find('.f-port').val() : null,
            merk: tr.find('.f-merk').val(),
            tahun: tr.find('.f-tahun').val(),
            kondisi: tr.find('.f-kondisi').val(),
            latitude: tr.find('.f-lat').val(),
            longitude: tr.find('.f-lng').val(),
            keterangan: tr.find('.f-keterangan').val()
        };
    };

    $(document).on('click', '.btn-edit-barang', function () {
        const tr = $(this).closest('tr');
        const idOpd = $('#select-opd-survei').val();
        if (!idOpd) return;

        const payload = buildPayloadBarang(tr, idOpd);

        $.post(`${baseUrl}input/data-awal/save-barang`, payload, (res) => {
            if (res.status === 'success') {
                tr.attr('data-id-opd-barang', res.id_opd_barang);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: 'Data perangkat berhasil diperbarui.',
                    timer: 1200,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Gagal', res.message || 'Gagal menyimpan.', 'error');
            }
        });
    });

    $(document).on('change blur', '.input-save-barang', function () {
        const tr = $(this).closest('tr');
        const idOpd = $('#select-opd-survei').val();
        if (!idOpd) return;

        const payload = buildPayloadBarang(tr, idOpd);

        $.post(`${baseUrl}input/data-awal/save-barang`, payload, (res) => {
            if (res.status === 'success') {
                tr.attr('data-id-opd-barang', res.id_opd_barang);
                triggerSaveBadge();
            }
        });
    });

    // 8. Hapus Perangkat
    $(document).on('click', '.btn-hapus-barang', function () {
        const tr = $(this).closest('tr');
        const tbody = tr.closest('tbody');
        const idOpdBarang = tr.attr('data-id-opd-barang');
        const isSwitch = tr.attr('data-nama-barang')?.toLowerCase().includes('switch');
        const colCount = isSwitch ? 8 : 7;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data perangkat ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                if (idOpdBarang) {
                    $.post(`${baseUrl}input/data-awal/delete-barang/${idOpdBarang}`, (res) => {
                        tr.remove();
                        if (tbody.children('tr').length === 0) {
                            tbody.append(`<tr class="empty-row"><td colspan="${colCount}" class="text-center text-muted py-3 small">Belum ada data.</td></tr>`);
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Data perangkat berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });
                } else {
                    tr.remove();
                    if (tbody.children('tr').length === 0) {
                        tbody.append(`<tr class="empty-row"><td colspan="${colCount}" class="text-center text-muted py-3 small">Belum ada data.</td></tr>`);
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Baris berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        });
    });
});