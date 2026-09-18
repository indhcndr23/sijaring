document.addEventListener("DOMContentLoaded", () => {
    const modalEl = document.getElementById('modalDetailOrg');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    
    let currentModalId = null; 

    const escapeHTML = (str) => {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>'"]/g, 
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag])
        );
    };

    const renderBadgeIndex = (nilai, status) => {
        const warna = {
            hijau:  { bg: '#d1fae5', text: '#065f46' },
            kuning: { bg: '#fef9c3', text: '#713f12' },
            merah:  { bg: '#fee2e2', text: '#991b1b' },
            abu:    { bg: '#f3f4f6', text: '#6b7280' },
        };
        const w = warna[status] || warna.abu;
        const nilaiTeks = escapeHTML(nilai);
        return `<span style="background:${w.bg}; color:${w.text}; padding:2px 8px; border-radius:20px; font-size:12px; font-weight:500">${nilaiTeks || '-'}</span>`;
    };

    // Fungsi Paginasi Barang Khusus Grafik
    window.changePageBarangGrafik = (direction) => {
        const container = document.getElementById('barang-pagination-container-grafik');
        if (!container) return;
        
        let currentPage = parseInt(container.getAttribute('data-page') || '1');
        const totalPages = parseInt(container.getAttribute('data-total-pages') || '1');
        
        currentPage += direction;
        if (currentPage < 1 || currentPage > totalPages) return;
        
        container.setAttribute('data-page', currentPage);
        
        document.querySelectorAll('.barang-page-item-grafik').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll(`.barang-page-grafik-${currentPage}`).forEach(el => el.classList.remove('d-none'));
        
        const pageInfo = document.getElementById('page-info-grafik');
        if (pageInfo) pageInfo.textContent = `${currentPage} / ${totalPages}`;
    };

    document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-detail');
        if (!btn) return;
        
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');

        currentModalId = id; 

        const labelEl = document.getElementById('modalDetailOrgLabel');
        const bodyEl  = document.getElementById('modalDetailOrgBody');

        if (labelEl) labelEl.textContent = `Detail — ${name || ''}`;
        if (bodyEl) bodyEl.innerHTML = '<div class="text-center py-3"><i class="bx bx-loader-alt bx-spin fs-3 text-primary"></i><br>Memuat data...</div>';

        modal?.show();

        const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
        const rootUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

        try {
            const fetchWithCheck = async (url) => {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Gagal memuat data (Status: ${response.status})`);
                return response.json();
            };

            const [detailResult, histResult] = await Promise.all([
                fetchWithCheck(`${rootUrl}dashboard/detail/${id}`),
                fetchWithCheck(`${rootUrl}dashboard/histori-index/${id}`)
            ]);

            if (currentModalId !== id) return; 

            const opd        = detailResult.opd || {};
            const barangList = detailResult.barang || [];
            const aktif      = histResult.aktif || {};
            const histori    = histResult.histori || [];

            // SUSUN TABEL BARANG DENGAN PAGINATION & FOTO BUKTI
            let barangRows = '';
            const itemsPerPage = 5;
            const totalPages = Math.ceil(barangList.length / itemsPerPage) || 1;

            if (barangList.length === 0) {
                barangRows = '<tr><td colspan="6" class="text-center text-muted py-3">Belum ada perangkat terdaftar</td></tr>';
            } else {
                barangList.forEach((b, index) => {
                    const pageNum = Math.floor(index / itemsPerPage) + 1;
                    const hideClass = pageNum > 1 ? 'd-none' : '';

                    const fotoBtn = b.foto_bukti
                        ? `<a href="${rootUrl}uploads/kepemilikan/${escapeHTML(b.foto_bukti)}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:11px"><i class="bx bx-image-alt"></i> Foto</a>`
                        : '<span class="text-muted" style="font-size:11px">-</span>';

                    barangRows += `
                        <tr class="barang-page-item-grafik barang-page-grafik-${pageNum} ${hideClass}">
                            <td><strong>${escapeHTML(b.jenis_barang)}</strong></td>
                            <td>${escapeHTML(b.merk || '-')}</td>
                            <td>${escapeHTML(b.tahun) || '-'}</td>
                            <td><span class="badge ${b.kondisi === 'Baik' ? 'bg-success' : 'bg-warning text-dark'}">${escapeHTML(b.kondisi) || '-'}</span></td>
                            <td>${escapeHTML(b.lat) || '-'}</td>
                            <td>${escapeHTML(b.lng) || '-'}</td>
                            <td>${escapeHTML(b.keterangan) || '-'}</td>
                            <td class="text-center">${fotoBtn}</td>
                        </tr>`;
                });
            }

            const rawNilaiAktif = aktif.nilai_index !== null && aktif.nilai_index !== undefined ? Number(aktif.nilai_index) : null;
            const nilaiAktif = rawNilaiAktif !== null ? Math.round(rawNilaiAktif) : '-';
            
            const statusAktif = nilaiAktif >= 80 ? 'hijau' : nilaiAktif >= 50 ? 'kuning' : 'merah';
            const badgeAktif  = renderBadgeIndex(nilaiAktif, statusAktif);

            let historiRows = '';
            if (histori.length === 0) {
                historiRows = '<tr><td colspan="4" class="text-center text-muted">Belum ada histori perubahan</td></tr>';
            } else {
                histori.forEach((h, i) => {
                    const nilaiH = Math.round(Number(h.nilai_index));
                    let nilaiNext = null;
                    if (i < histori.length - 1) {
                        nilaiNext = Math.round(Number(histori[i + 1].nilai_index));
                    }

                    const statusH = nilaiH >= 80 ? 'hijau' : nilaiH >= 50 ? 'kuning' : 'merah';
                    const badgeH  = renderBadgeIndex(nilaiH, statusH);

                    let perubahan = '';
                    if (nilaiNext !== null && !isNaN(nilaiNext) && !isNaN(nilaiH)) {
                        const selisih = nilaiH - nilaiNext;
                        if (selisih > 0)      perubahan = `<span style="color:#1a6b3a; font-weight:500">▲ +${selisih} dari ${nilaiNext}</span>`;
                        else if (selisih < 0) perubahan = `<span style="color:#dc3545; font-weight:500">▼ ${selisih} dari ${nilaiNext}</span>`;
                        else                  perubahan = `<span style="color:#6c757d">— sama</span>`;
                    } else {
                        perubahan = '<span style="color:#6c757d; font-size:11px">data awal</span>';
                    }

                    const tgl = h.created_at 
                        ? new Date(h.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                        : '-';

                    historiRows += `
                        <tr>
                            <td style="color:#6c757d; font-size:12px">${tgl}</td>
                            <td>${badgeH}</td>
                            <td>${escapeHTML(h.jenis_kabel || opd.jenis_kabel || '-')}</td>
                            <td>${perubahan}</td>
                        </tr>`;
                });
            }

            if (bodyEl) {
                bodyEl.innerHTML = `
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info-opd" type="button">Info OPD</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info-barang" type="button">Info Barang</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#tab-info-index" data-bs-toggle="tab" type="button">Info Index</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB 1: INFO OPD -->
                        <div class="tab-pane fade show active" id="tab-info-opd">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                    <tr><th style="width:35%; font-weight:600; color:#495057;">Nama OPD</th><td style="color:#212529;">${escapeHTML(opd.nama_opd) || '-'}</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Alamat</th><td style="color:#212529;">${escapeHTML(opd.alamat) || '-'}</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">PIC / No. HP</th><td style="color:#212529;">${escapeHTML(opd.nama_pic) || '-'} (${escapeHTML(opd.no_hp_pic) || '-'})</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Jumlah Pegawai</th><td style="color:#212529;">${opd.jumlah_pegawai ?? 0} Orang</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Jumlah Perangkat</th><td style="color:#212529;">${opd.jumlah_perangkat ?? 0} Unit</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Rata-rata Tamu</th><td style="color:#212529;">${opd.rata_tamu ?? 0} Orang / Hari</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Luas Bangunan</th><td style="color:#212529;">${opd.luas_ruangan ?? 0} m²</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Jenis Dinding</th><td style="color:#212529;">${escapeHTML(opd.jenis_dinding) || '-'}</td></tr>
                                    <tr><th style="font-weight:600; color:#495057;">Jumlah Lantai / Ruangan</th><td style="color:#212529;">${opd.jumlah_lantai ?? 0} Lantai / ${opd.jumlah_ruangan ?? 0} Ruangan</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- TAB 2: INFO BARANG -->
                        <div class="tab-pane fade" id="tab-info-barang">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Inventaris Perangkat</h6>
                                ${barangList.length > itemsPerPage ? `
                                <div id="barang-pagination-container-grafik" data-page="1" data-total-pages="${totalPages}" class="d-flex align-items-center gap-1">
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="changePageBarangGrafik(-1)">&laquo;</button>
                                    <span id="page-info-grafik" class="small fw-semibold px-1">1 / ${totalPages}</span>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="changePageBarangGrafik(1)">&raquo;</button>
                                </div>` : ''}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-custom align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Jenis Perangkat</th>
                                            <th>Merk</th>
                                            <th>Tahun</th>
                                            <th>Kondisi</th>
                                            <th>Lat</th>
                                            <th>Lng</th>
                                            <th>Keterangan</th>
                                            <th class="text-center">Bukti</th>
                                        </tr>
                                    </thead>
                                    <tbody>${barangRows}</tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 3: INFO INDEX -->
                        <div class="tab-pane fade" id="tab-info-index">
                            <table class="table table-sm align-middle mb-3">
                                <tbody>
                                    <tr><th style="width:30%; font-weight:600; color:#495057;">Index Saat Ini</th><td>${badgeAktif}</td></tr>
                                </tbody>
                            </table>

                            <h6 class="fw-bold mb-2">Riwayat Changes</h6>
                            <p class="text-muted mb-2" style="font-size:12px">
                                <i class="bx bx-info-circle"></i> Riwayat perubahan nilai index — tersimpan otomatis setiap kali data diedit.
                            </p>
                            <div class="table-responsive">
                                <table class="table table-sm table-custom align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th><th>Nilai Index</th><th>Jenis Koneksi</th><th>Perubahan</th>
                                        </tr>
                                    </thead>
                                    <tbody>${historiRows}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>`;
            }
        } catch (err) {
            if (currentModalId !== id) return;
            if (bodyEl) {
                bodyEl.innerHTML = `<div class="text-danger text-center py-3">Gagal memuat data: ${escapeHTML(err.message)}</div>`;
            }
        }
    });
});