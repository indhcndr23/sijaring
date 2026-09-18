document.addEventListener("DOMContentLoaded", () => {
    const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
    const baseUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

    const modalDimensiEl = document.getElementById('modalDimensi');
    const modalDimensi = modalDimensiEl ? new bootstrap.Modal(modalDimensiEl) : null;

    const modalVariabelEl = document.getElementById('modalVariabel');
    const modalVariabel = modalVariabelEl ? new bootstrap.Modal(modalVariabelEl) : null;

    const modalSubVariabelEl = document.getElementById('modalSubVariabel');
    const modalSubVariabel = modalSubVariabelEl ? new bootstrap.Modal(modalSubVariabelEl) : null;

    const modalSimulasiEl = document.getElementById('modalSimulasi');
    const modalSimulasi = modalSimulasiEl ? new bootstrap.Modal(modalSimulasiEl) : null;

    // ================== LOAD DATA UTAMA ==================
    const loadData = async () => {
        try {
            const res = await fetch(`${baseUrl}input/index/data`);
            const data = await res.json();
            renderGridDimensi(data.dimensi || []);
            renderTableVariabel(data.variabel || []);
        } catch (error) {
            console.error('Gagal memuat data index:', error);
        }
    };

    const loadDropdownDimensi = async (selectedId = null) => {
        try {
            const res = await fetch(`${baseUrl}input/index/dimensi/list`);
            const dimensiList = await res.json();

            const select = $('#var_dimensi').empty();
            select.append('<option value="">-- Pilih Dimensi --</option>');

            dimensiList.forEach(d => {
                const selected = (selectedId && selectedId == d.id_dimensi) ? 'selected' : '';
                select.append(`<option value="${d.id_dimensi}" ${selected}>${d.nama_dimensi}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat list dimensi:', error);
        }
    };

    const loadDropdownBarang = async (selectedId = null) => {
        try {
            const res = await fetch(`${baseUrl}input/barang/data`);
            const data = await res.json();

            const select = $('#sub_jenis_barang').empty();
            select.append('<option value="">-- Semua Jenis (tidak difilter) --</option>');

            (data.data || []).forEach(b => {
                const selected = (selectedId && selectedId == b.id) ? 'selected' : '';
                select.append(`<option value="${b.id}" ${selected}>${b.jenis}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat list barang:', error);
        }
    };

    // ================== GRID DIMENSI (TAB 1) ==================
    const renderGridDimensi = (dimensiList) => {
        const container = $('#grid-dimensi-container').empty();

        if (dimensiList.length === 0) {
            container.html('<div class="col-12 text-center text-muted py-4">Belum ada Dimensi terdaftar.</div>');
            return;
        }

        dimensiList.forEach(d => {
            const html = `
                <div class="col-md-6 col-lg-3">
                    <div class="card card-dimensi p-3 h-100 shadow-sm border-0">
                        <div class="d-flex justify-content-end mb-2">
                            <span class="badge bg-dark px-2 py-1">Bobot: ${d.bobot}</span>
                        </div>
                        <h6 class="fw-bold text-dark mt-1 mb-3">${d.nama_dimensi}</h6>
                        <div class="mt-auto">
                            <button type="button" class="btn btn-sm btn-warning w-100 btn-edit-dimensi" data-id="${d.id_dimensi}">
                                <i class="bx bx-edit"></i> Edit Dimensi
                            </button>
                        </div>
                    </div>
                </div>`;
            container.append(html);
        });
    };

    // ================== TABEL VARIABEL (TAB 2) DENGAN EXPANDABLE ROW ==================
const labelAcuanVariabel = (v) => {
    if (v.sumber_tipe === 'KOMBINASI') {
        // Menggunakan gaya code block agar rumus bersih, profesional, dan mudah dibaca
        return `<code class="bg-light text-dark px-2 py-1 rounded border d-inline-block font-monospace fs-7">${v.formula || 'Kombinasi'}</code>`;
    } else if (v.field_source === 'tahun') {
        return `<span class="badge bg-info-subtle text-info border border-info px-2 py-1">Usia Alat (Tahun)</span>`;
    } else if (v.field_source === 'jumlah') {
        return `<span class="badge bg-info-subtle text-info border border-info px-2 py-1">Total Kuantitas Alat</span>`;
    } else if (v.field_source === 'jumlah_pegawai') {
        return `<span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">Jumlah Pegawai</span>`;
    }
    return `<span class="text-secondary fw-medium">${v.field_source || '-'}</span>`;
};

    // Menyimpan mapping id_variabel -> id cell dimensi (dipakai saat toggle sub-panel
    // untuk menyesuaikan rowspan secara dinamis, karena baris sub-panel yang
    // disembunyikan dengan d-none TIDAK memakan tinggi/ruang di tabel).
    let variabelGroupCellId = {};

    const renderTableVariabel = (variabelList) => {
        const tbody = $('#table-variabel tbody').empty();
        variabelGroupCellId = {};

        if (!variabelList || variabelList.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center text-muted py-4">Belum ada variabel terdaftar.</td></tr>');
            return;
        }

        const grouped = {};
        variabelList.forEach(v => {
            if (!grouped[v.nama_dimensi]) grouped[v.nama_dimensi] = [];
            grouped[v.nama_dimensi].push(v);
        });

        let html = '';
        const dimensiKeys = Object.keys(grouped);

        dimensiKeys.forEach((dimensiName, groupIndex) => {
            const list = grouped[dimensiName];
            // rowspan HARUS sama dengan jumlah baris yang benar-benar tampil (visible),
            // bukan dikali 2. Baris sub-panel (.row-sub-panel) defaultnya d-none,
            // sehingga tidak dihitung browser sebagai baris tabel sampai ditampilkan.
            const rowspan = list.length;
            const cellId = `dimensi-cell-${groupIndex}`;

            list.forEach((v, index) => {
                const isFirstRow = index === 0;
                const isLastRow = index === rowspan - 1;
                const groupBorderClass = isLastRow ? 'style="border-bottom: 3px solid #cbd5e1 !important;"' : 'style="border-bottom: 1px solid #e2e8f0;"';

                variabelGroupCellId[v.id_variabel] = cellId;

                html += `<tr ${groupBorderClass}>`;

                if (isFirstRow) {
                    html += `
                        <td id="${cellId}" rowspan="${rowspan}" class="align-middle text-center bg-light border-end fw-bold" style="border-right: 2px solid #cbd5e1 !important; width: 160px;">
                            <span class="badge bg-dark fs-6 text-wrap px-3 py-2 shadow-sm">${dimensiName}</span>
                        </td>`;
                }

                const badgeTipe = v.sumber_tipe === 'KOMBINASI' ? 'bg-danger' : (v.sumber_tipe === 'OPD' ? 'bg-primary' : 'bg-success');
                const badgeArah = v.arah === 'MAX'
                    ? `<span class="badge bg-success"><i class="bx bx-up-arrow-alt fs-6 align-middle me-1"></i>MAX</span>`
                    : `<span class="badge bg-warning text-dark"><i class="bx bx-down-arrow-alt fs-6 align-middle me-1"></i>MIN</span>`;

                html += `
                    <td class="text-center align-middle" style="width:30px">
                        <i class="bx bx-chevron-right btn-toggle-sub" data-id="${v.id_variabel}" style="cursor:pointer;font-size:18px" role="button"></i>
                    </td>
                    <td class="fw-bold align-middle">${v.nama_variabel}</td>
                    <td class="text-center align-middle"><span class="badge ${badgeTipe}">${v.sumber_tipe}</span></td>
                    <td class="align-middle">${labelAcuanVariabel(v)}</td>
                    <td class="text-center align-middle">${v.nilai_min} - ${v.nilai_max}</td>
                    <td class="text-center align-middle">${badgeArah}</td>
                    <td class="text-center fw-bold align-middle">${v.bobot}</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-warning btn-edit-variabel" data-id="${v.id_variabel}">
                            <i class="bx bx-edit"></i>
                        </button>
                    </td>
                </tr>
                <tr class="row-sub-panel d-none" id="sub-panel-row-${v.id_variabel}">
                    <td colspan="8" class="p-0 bg-light">
                        <div class="p-3" id="sub-panel-body-${v.id_variabel}">
                            <div class="text-center text-muted py-2" style="font-size:12px">Memuat...</div>
                        </div>
                    </td>
                </tr>`;
            });
        });

        tbody.html(html);
    };

    // ================== PANEL SUB-VARIABEL (EXPANDABLE) ==================
const labelAcuanSub = (sv) => {
    if (sv.sumber_tipe === 'KOMBINASI') {
        return `<code class="bg-light text-dark px-2 py-1 rounded border d-inline-block font-monospace" style="font-size:11px">${sv.formula || 'Kombinasi'}</code>`;
    } else if (sv.field_source === 'kondisi_skor') {
        return `<span class="badge bg-info-subtle text-info" style="font-size:11px">Skor Kondisi</span>`;
    }
    return `<span class="text-secondary" style="font-size:11px">${sv.field_source || '-'}</span>`;
};

    const renderSubPanel = (idVariabel, subs, totalBobot) => {
        const target = parseFloat(totalBobot) || 0;
        const isBalanced = Math.abs(target - 1) < 0.011;
        const badgeBobot = isBalanced
            ? `<span class="badge bg-success">Total bobot: ${target.toFixed(2)} <i class="bx bx-check"></i></span>`
            : `<span class="badge bg-danger">Total bobot: ${target.toFixed(2)} <i class="bx bx-x"></i> (harus 1.00)</span>`;

        let rows = '';
        if (!subs || subs.length === 0) {
            rows = `<tr><td colspan="7" class="text-center text-muted py-2" style="font-size:12px">Belum ada sub-variabel &mdash; nilai variabel ini dihitung langsung seperti biasa.</td></tr>`;
        } else {
            subs.forEach(sv => {
                const jenisBadge = sv.jenis_barang
                    ? `<span class="badge bg-dark" style="font-size:11px">${sv.jenis_barang}</span>`
                    : `<span class="text-muted" style="font-size:11px">Semua jenis</span>`;

                rows += `
                    <tr>
                        <td style="font-size:12px">${sv.nama_sub_variabel}</td>
                        <td class="text-center">${jenisBadge}</td>
                        <td class="text-center">${labelAcuanSub(sv)}</td>
                        <td class="text-center" style="font-size:12px">${sv.nilai_min} - ${sv.nilai_max}</td>
                        <td class="text-center" style="font-size:12px">${sv.arah}</td>
                        <td class="text-center fw-bold" style="font-size:12px">${sv.bobot}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-warning btn-edit-sub py-0 px-2" data-id="${sv.id_sub_variabel}" data-idvar="${idVariabel}" style="font-size:11px">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger btn-hapus-sub py-0 px-2" data-id="${sv.id_sub_variabel}" data-idvar="${idVariabel}" style="font-size:11px">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>`;
            });
        }

        return `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="fw-bold mb-0" style="font-size:13px">Sub-variabel</p>
                ${badgeBobot}
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-sm table-bordered align-middle mb-0 bg-white">
                    <thead>
                        <tr style="font-size:11px">
                            <th>Nama Sub-Variabel</th>
                            <th class="text-center">Jenis Barang</th>
                            <th class="text-center">Acuan / Rumus</th>
                            <th class="text-center">Min-Max</th>
                            <th class="text-center">Arah</th>
                            <th class="text-center">Bobot</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success btn-tambah-sub" data-idvar="${idVariabel}">
                <i class="bx bx-plus"></i> Tambah Sub-Variabel
            </button>`;
    };

    const refreshSubPanel = async (idVariabel) => {
        try {
            const res = await fetch(`${baseUrl}input/index/sub-variabel/list/${idVariabel}`);
            const data = await res.json();
            $(`#sub-panel-body-${idVariabel}`).html(renderSubPanel(idVariabel, data.data, data.total_bobot));
        } catch (error) {
            $(`#sub-panel-body-${idVariabel}`).html('<div class="text-danger small">Gagal memuat sub-variabel.</div>');
        }
    };

    $(document).on('click', '.btn-toggle-sub', function () {
        const id = $(this).data('id');
        const $icon = $(this);
        const $row = $(`#sub-panel-row-${id}`);
        const cellId = variabelGroupCellId[id];
        const $dimensiCell = cellId ? $(`#${cellId}`) : $();

        const isOpen = !$row.hasClass('d-none');
        if (isOpen) {
            $row.addClass('d-none');
            $icon.removeClass('bx-chevron-down').addClass('bx-chevron-right');
            // Baris sub-panel ditutup -> kembali tidak memakan ruang, rowspan dikurangi 1.
            if ($dimensiCell.length) {
                const current = parseInt($dimensiCell.attr('rowspan'), 10) || 1;
                $dimensiCell.attr('rowspan', Math.max(1, current - 1));
            }
            return;
        }

        $icon.removeClass('bx-chevron-right').addClass('bx-chevron-down');
        $row.removeClass('d-none');
        // Baris sub-panel dibuka -> ikut memakan ruang baris, rowspan ditambah 1
        // supaya sel dimensi tetap menutupi seluruh tinggi grup dengan benar.
        if ($dimensiCell.length) {
            const current = parseInt($dimensiCell.attr('rowspan'), 10) || 1;
            $dimensiCell.attr('rowspan', current + 1);
        }
        refreshSubPanel(id);
    });

    // ================== TOGGLE FORM FIELD - VARIABEL ==================
    $('#var_sumber_tipe').on('change', function () {
        const val = $(this).val();
        const fieldSelect = $('#var_field_source').empty();

        if (val === 'OPD') {
            $('#wrapper-field-source').removeClass('d-none');
            $('#wrapper-formula').addClass('d-none');
            fieldSelect.append('<option value="jumlah_pegawai">[OPD] Jumlah Pegawai</option>');
            fieldSelect.append('<option value="jumlah_perangkat">[OPD] Jumlah Perangkat</option>');
        } else if (val === 'KEPEMILIKAN') {
            $('#wrapper-field-source').removeClass('d-none');
            $('#wrapper-formula').addClass('d-none');
            fieldSelect.append('<option value="tahun">[Kepemilikan] Usia Perangkat</option>');
            fieldSelect.append('<option value="jumlah">[Kepemilikan] Jumlah Barang</option>');
        } else {
            $('#wrapper-field-source').addClass('d-none');
            $('#wrapper-formula').removeClass('d-none');
        }
    });

    // ================== TOGGLE FORM FIELD - SUB-VARIABEL ==================
    $('#sub_sumber_tipe').on('change', function () {
        const val = $(this).val();
        const fieldSelect = $('#sub_field_source').empty();

        if (val === 'OPD') {
            $('#wrapper-sub-field-source').removeClass('d-none');
            $('#wrapper-sub-formula').addClass('d-none');
            fieldSelect.append('<option value="jumlah_pegawai">[OPD] Jumlah Pegawai</option>');
            fieldSelect.append('<option value="jumlah_perangkat">[OPD] Jumlah Perangkat</option>');
        } else if (val === 'KEPEMILIKAN') {
            $('#wrapper-sub-field-source').removeClass('d-none');
            $('#wrapper-sub-formula').addClass('d-none');
            fieldSelect.append('<option value="kondisi_skor">[Kepemilikan] Skor Kondisi (Baik=5 / Ringan=3 / Berat=1)</option>');
            fieldSelect.append('<option value="tahun">[Kepemilikan] Usia Perangkat</option>');
            fieldSelect.append('<option value="jumlah">[Kepemilikan] Jumlah Barang</option>');
        } else {
            $('#wrapper-sub-field-source').addClass('d-none');
            $('#wrapper-sub-formula').removeClass('d-none');
        }
    });

    // ================== MODAL DIMENSI (TAB 1) ==================
    window.modalTambahDimensi = () => {
        $('#modalDimensiTitle').text('Tambah Dimensi Baru');
        $('#form-dimensi')[0].reset();
        $('#dimensi_id').val('');
        modalDimensi?.show();
    };

    $(document).on('click', '.btn-edit-dimensi', async function () {
        const id = $(this).data('id');
        try {
            const res = await fetch(`${baseUrl}input/index/dimensi/detail/${id}`);
            const data = await res.json();

            $('#modalDimensiTitle').text('Edit Dimensi');
            $('#dimensi_id').val(data.id_dimensi);
            $('#dimensi_nama').val(data.nama_dimensi);
            $('#dimensi_bobot').val(data.bobot);
            modalDimensi?.show();
        } catch (error) {
            Swal.fire('Error', 'Gagal memuat detail dimensi', 'error');
        }
    });

    $('#form-dimensi').on('submit', async function (e) {
        e.preventDefault();
        try {
            const res = await fetch(`${baseUrl}input/index/dimensi/store`, {
                method: 'POST',
                body: new FormData(this)
            });
            const data = await res.json();

            modalDimensi?.hide();
            Swal.fire('Berhasil', data.message, 'success');
            loadData();
        } catch (error) {
            Swal.fire('Error', 'Gagal menyimpan dimensi', 'error');
        }
    });

    // ================== MODAL VARIABEL (TAB 2) ==================
    window.modalTambahVariabel = async () => {
        $('#modalVariabelTitle').text('Tambah Variabel Baru');
        $('#form-variabel')[0].reset();
        $('#var_id').val('');
        $('#var_dimensi').prop('disabled', false);
        $('#var_min').val(0);
        $('#var_max').val(100);
        $('#var_arah').val('MAX');
        $('#var_sumber_tipe').val('OPD').trigger('change');

        await loadDropdownDimensi();
        modalVariabel?.show();
    };

    $(document).on('click', '.btn-edit-variabel', async function () {
        const id = $(this).data('id');
        try {
            const res = await fetch(`${baseUrl}input/index/variabel/detail/${id}`);
            const data = await res.json();

            $('#modalVariabelTitle').text('Edit Variabel Penilaian');
            $('#var_id').val(data.id_variabel);
            $('#var_nama').val(data.nama_variabel);
            $('#var_sumber_tipe').val(data.sumber_tipe).trigger('change');

            if (data.sumber_tipe !== 'KOMBINASI') {
                $('#var_field_source').val(data.field_source);
            } else {
                $('#var_formula').val(data.formula);
            }
            $('#var_min').val(data.nilai_min);
            $('#var_max').val(data.nilai_max);
            $('#var_arah').val(data.arah);
            $('#var_bobot').val(data.bobot);

            await loadDropdownDimensi(data.id_dimensi);
            $('#var_dimensi').prop('disabled', true);
            modalVariabel?.show();
        } catch (error) {
            Swal.fire('Error', 'Gagal memuat detail variabel', 'error');
        }
    });

    $('#form-variabel').on('submit', async function (e) {
        e.preventDefault();
        $('#var_dimensi').prop('disabled', false);

        try {
            const res = await fetch(`${baseUrl}input/index/variabel/store`, {
                method: 'POST',
                body: new FormData(this)
            });
            const data = await res.json();

            modalVariabel?.hide();
            Swal.fire('Berhasil', data.message, 'success');
            loadData();
        } catch (error) {
            Swal.fire('Error', 'Gagal menyimpan variabel', 'error');
        }
    });

    // ================== MODAL SUB-VARIABEL ==================
    window.modalTambahSub = async (idVariabel) => {
        $('#modalSubVariabelTitle').text('Tambah Sub-Variabel');
        $('#form-sub-variabel')[0].reset();
        $('#sub_id').val('');
        $('#sub_id_variabel').val(idVariabel);
        $('#sub_min').val(0);
        $('#sub_max').val(5);
        $('#sub_arah').val('MIN');
        $('#sub_sumber_tipe').val('KEPEMILIKAN').trigger('change');
        await loadDropdownBarang();
        modalSubVariabel?.show();
    };

    $(document).on('click', '.btn-tambah-sub', function () {
        window.modalTambahSub($(this).data('idvar'));
    });

    $(document).on('click', '.btn-edit-sub', async function () {
        const id = $(this).data('id');
        const idVar = $(this).data('idvar');
        try {
            const res = await fetch(`${baseUrl}input/index/sub-variabel/detail/${id}`);
            const data = await res.json();

            $('#modalSubVariabelTitle').text('Edit Sub-Variabel');
            $('#sub_id').val(data.id_sub_variabel);
            $('#sub_id_variabel').val(idVar);
            $('#sub_nama').val(data.nama_sub_variabel);
            $('#sub_sumber_tipe').val(data.sumber_tipe).trigger('change');

            if (data.sumber_tipe !== 'KOMBINASI') {
                $('#sub_field_source').val(data.field_source);
            } else {
                $('#sub_formula').val(data.formula);
            }
            $('#sub_min').val(data.nilai_min);
            $('#sub_max').val(data.nilai_max);
            $('#sub_arah').val(data.arah);
            $('#sub_bobot').val(data.bobot);

            await loadDropdownBarang(data.jenis_barang_id);
            modalSubVariabel?.show();
        } catch (error) {
            Swal.fire('Error', 'Gagal memuat detail sub-variabel', 'error');
        }
    });

    $('#form-sub-variabel').on('submit', async function (e) {
        e.preventDefault();
        const idVar = $('#sub_id_variabel').val();

        try {
            const res = await fetch(`${baseUrl}input/index/sub-variabel/store`, {
                method: 'POST',
                body: new FormData(this)
            });
            const data = await res.json();

            modalSubVariabel?.hide();
            Swal.fire('Berhasil', data.message, 'success');
            refreshSubPanel(idVar);
        } catch (error) {
            Swal.fire('Error', 'Gagal menyimpan sub-variabel', 'error');
        }
    });

    $(document).on('click', '.btn-hapus-sub', function () {
        const id = $(this).data('id');
        const idVar = $(this).data('idvar');

        Swal.fire({
            title: 'Hapus Sub-Variabel?',
            text: 'Data ini akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (!result.isConfirmed) return;
            try {
                await fetch(`${baseUrl}input/index/sub-variabel/hapus/${id}`, { method: 'POST' });
                refreshSubPanel(idVar);
                Swal.fire('Berhasil', 'Sub-variabel dihapus', 'success');
            } catch (error) {
                Swal.fire('Error', 'Gagal menghapus data.', 'error');
            }
        });
    });

    // ================== MODAL SIMULASI PERHITUNGAN ==================
    const loadDropdownOpdSimulasi = async () => {
        try {
            const res = await fetch(`${baseUrl}input/index/simulasi/opd-list`);
            const list = await res.json();
            const select = $('#simulasi_opd').empty();
            select.append('<option value="">-- Pilih OPD --</option>');
            list.forEach(o => select.append(`<option value="${o.id_opd}">${o.nama_opd}</option>`));
        } catch (error) {
            console.error('Gagal memuat list OPD:', error);
        }
    };

    window.bukaModalSimulasi = async () => {
        $('#simulasi-hasil').html('<p class="text-muted small mb-0">Pilih OPD lalu klik Hitung.</p>');
        await loadDropdownOpdSimulasi();
        modalSimulasi?.show();
    };

    $('#btn-hitung-simulasi').on('click', async function () {
        const idOpd = $('#simulasi_opd').val();
        if (!idOpd) {
            Swal.fire('Peringatan', 'Pilih OPD terlebih dahulu', 'warning');
            return;
        }

        $('#simulasi-hasil').html('<div class="text-center py-3"><i class="bx bx-loader-alt bx-spin fs-3"></i></div>');

        try {
            const res = await fetch(`${baseUrl}input/index/simulasi/${idOpd}`);
            const data = await res.json();

            if (!data.success) {
                $('#simulasi-hasil').html(`<p class="text-danger small mb-0">${data.message}</p>`);
                return;
            }

            let html = `<p class="fw-bold mb-3">Index Final ${data.nama_opd}: <span class="badge bg-dark fs-6">${data.index_final}</span></p>`;

            data.breakdown.forEach(dim => {
                html += `<div class="mb-3 p-2 border rounded">
                    <p class="fw-bold mb-1" style="font-size:13px">${dim.nama_dimensi} (bobot ${dim.bobot_dimensi}) &rarr; skor ${dim.skor_100}</p>`;
                dim.variabel.forEach(v => {
                    html += `<p class="mb-1 ps-3" style="font-size:12px">&bull; ${v.nama} (bobot ${v.bobot}) &rarr; skor ${v.skor_100 ?? '-'}</p>`;
                    if (v.subs && v.subs.length > 0) {
                        v.subs.forEach(s => {
                            html += `<p class="mb-1 ps-5 text-muted" style="font-size:11px">- ${s.nama} (bobot ${s.bobot}): nilai mentah ${s.raw ?? '-'} &rarr; skor ${s.skor_100 ?? '-'}</p>`;
                        });
                    }
                });
                html += `</div>`;
            });

            $('#simulasi-hasil').html(html);
        } catch (error) {
            $('#simulasi-hasil').html('<p class="text-danger small mb-0">Gagal menghitung simulasi.</p>');
        }
    });

    loadData();
});