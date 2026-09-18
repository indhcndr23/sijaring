document.addEventListener("DOMContentLoaded", () => {
    const data = window.petaData || { opd: [], jaringan: [], geojsonUrl: null, kecamatanFolderUrl: null };

    const warnaStatus = {
        hijau:  '#2bb35e',
        kuning: '#d4b106',
        merah:  '#e14343',
        abu:    '#9e9e9e'
    };

    const pusatMagelang = [-7.4285, 110.2169];
    const ZOOM_AWAL = 11;

    const map = L.map('peta-map', { zoomControl: true }).setView(pusatMagelang, ZOOM_AWAL);
    window.dashboardMap = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    map.setMinZoom(9);
    map.setMaxZoom(18);

    const clusterGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        iconCreateFunction: (cluster) => {
            const count = cluster.getChildCount();
            return L.divIcon({
                html: `<div style="background:#3b82f6;color:#fff;border-radius:50%;width:44px;height:44px;display:flex;flex-direction:column;align-items:center;justify-content:center;font-weight:bold;box-shadow:0 0 0 4px rgba(59,130,246,0.3);line-height:1.1;">
                          <span style="font-size:13px;">${count}</span>
                          <i class="bx bx-buildings" style="font-size:12px;opacity:0.9;"></i>
                       </div>`,
                className: '',
                iconSize: [44, 44]
            });
        }
    });

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

    // Fungsi Paginasi Barang Khusus Peta
    window.changePageBarangPeta = (direction) => {
        const container = document.getElementById('barang-pagination-container-peta');
        if (!container) return;
        
        let currentPage = parseInt(container.getAttribute('data-page') || '1');
        const totalPages = parseInt(container.getAttribute('data-total-pages') || '1');
        
        currentPage += direction;
        if (currentPage < 1 || currentPage > totalPages) return;
        
        container.setAttribute('data-page', currentPage);
        
        document.querySelectorAll('.barang-page-item-peta').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll(`.barang-page-peta-${currentPage}`).forEach(el => el.classList.remove('d-none'));
        
        const pageInfo = document.getElementById('page-info-peta');
        if (pageInfo) pageInfo.textContent = `${currentPage} / ${totalPages}`;
    };

    let currentModalId = null;

    const bukaDetailPeta = async (marker, idOpd, namaOpd) => {
        currentModalId = idOpd;

        const modalEl = document.getElementById('modalDetailPeta');
        const labelEl = document.getElementById('modalDetailPetaLabel');
        const bodyEl  = document.getElementById('modalDetailPetaBody');

        if (labelEl) labelEl.textContent = `Detail — ${namaOpd || ''}`;
        if (bodyEl)  bodyEl.innerHTML = '<div class="text-center py-3"><i class="bx bx-loader-alt bx-spin fs-3 text-primary"></i><br>Memuat data...</div>';

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();

        const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
        const rootUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

        try {
            const fetchWithCheck = async (url) => {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Gagal memuat data (Status: ${response.status})`);
                return response.json();
            };

            const [detailResult, histResult] = await Promise.all([
                fetchWithCheck(`${rootUrl}dashboard/detail/${idOpd}`),
                fetchWithCheck(`${rootUrl}dashboard/histori-index/${idOpd}`)
            ]);

            if (currentModalId !== idOpd) return; 

            const opd        = detailResult.opd || {};
            const barangList = detailResult.barang || [];
            const aktif      = histResult.aktif || {};
            const histori    = histResult.histori || [];

            let barangRows = '';
            const itemsPerPage = 5;
            const totalPages = Math.ceil(barangList.length / itemsPerPage) || 1;

            if (barangList.length === 0) {
                barangRows = '<tr><td colspan="8" class="text-center text-muted py-3">Belum ada perangkat terdaftar</td></tr>';
            } else {
                barangList.forEach((b, index) => {
                    const pageNum = Math.floor(index / itemsPerPage) + 1;
                    const hideClass = pageNum > 1 ? 'd-none' : '';

                    const fotoBtn = b.foto_bukti
                        ? `<a href="${rootUrl}uploads/kepemilikan/${escapeHTML(b.foto_bukti)}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:11px"><i class="bx bx-image-alt"></i> Foto</a>`
                        : '<span class="text-muted" style="font-size:11px">-</span>';

                    barangRows += `
                        <tr class="barang-page-item-peta barang-page-peta-${pageNum} ${hideClass}">
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
                                <div id="barang-pagination-container-peta" data-page="1" data-total-pages="${totalPages}" class="d-flex align-items-center gap-1">
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="changePageBarangPeta(-1)">&laquo;</button>
                                    <span id="page-info-peta" class="small fw-semibold px-1">1 / ${totalPages}</span>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="changePageBarangPeta(1)">&raquo;</button>
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
            if (currentModalId !== idOpd) return;
            if (bodyEl) {
                bodyEl.innerHTML = `<div class="text-danger text-center py-3">Gagal memuat data: ${escapeHTML(err.message)}</div>`;
            }
        }
    };

    const renderMarkerOpd = () => {
        data.opd.forEach(opd => {
            if (!opd.latitude || !opd.longitude) return;

            const warna = warnaStatus[opd.status] || warnaStatus.abu;

            const customIcon = L.divIcon({
                className: 'custom-opd-icon',
                html: `<div style="background-color:${warna};width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2.5px solid #ffffff;box-shadow:0 2px 6px rgba(0,0,0,0.3);color:#ffffff;font-size:16px;">
                          <i class="bx bxs-institution"></i>
                       </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            const marker = L.marker([opd.latitude, opd.longitude], { icon: customIcon });
            marker.bindTooltip(opd.nama_opd, { direction: 'top', offset: [0, -12] });
            marker.on('click', () => bukaDetailPeta(marker, opd.id_opd, opd.nama_opd));

            clusterGroup.addLayer(marker);
        });

        map.addLayer(clusterGroup);

        // KEMBALIKAN RENDER GARIS JARINGAN (FO & BROADBAND)
        data.jaringan.forEach(j => {
            if (!j.asal_lat || !j.tujuan_lat) return;

            const isFO = j.jenis_kabel === 'FO';
            L.polyline(
                [[j.asal_lat, j.asal_lng], [j.tujuan_lat, j.tujuan_lng]],
                { color: '#4e73df', weight: 3, dashArray: isFO ? null : '6, 6' }
            ).addTo(map);
        });
    };

    const renderBatasKabMagelang = async () => {
        if (!data.geojsonUrl) return;

        try {
            const res = await fetch(data.geojsonUrl);
            if (!res.ok) throw new Error(`Status ${res.status}`);
            const geo = await res.json();

            L.geoJSON(geo, {
                style: { color: '#4e73df', weight: 2, fillOpacity: 0, dashArray: '4, 4' },
                interactive: false
            }).addTo(map);

            const outerRing = [[-85, -180], [-85, 180], [85, 180], [85, -180]];
            const holes = [];

            geo.features.forEach(f => {
                const geom = f.geometry;
                const polys = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates;
                polys.forEach(poly => {
                    poly.forEach(ring => holes.push(ring.map(c => [c[1], c[0]])));
                });
            });

            L.polygon([outerRing].concat(holes), {
                stroke: false, fillColor: '#ffffff', fillOpacity: 1, interactive: false
            }).addTo(map);
        } catch (err) {
            console.error('Gagal load batas Kab. Magelang:', err);
        }
    };

    const renderKecamatan = async () => {
        if (!data.kecamatanFolderUrl) return;

        const daftarKecamatan = [
            'bandongan', 'borobudur', 'candimulyo', 'dukun', 'grabag', 'kajoran',
            'kaliangkrik', 'mertoyudan', 'mungkid', 'muntilan', 'ngablak', 'ngluwar',
            'pakis', 'salam', 'salaman', 'sawangan', 'secang', 'srumbung',
            'tegalrejo', 'tempuran', 'windusari'
        ];

        const kecamatanLayerGroup = L.layerGroup();
        const capitalize = (str) => str.replace(/(^|\s)\S/g, (t) => t.toUpperCase());

        const semuaFetch = daftarKecamatan.map(async (slug) => {
            try {
                const res = await fetch(`${data.kecamatanFolderUrl}${slug}.geojson`);
                if (!res.ok) throw new Error(`status ${res.status}`);
                const geo = await res.json();
                return { slug, geo };
            } catch (err) {
                return null;
            }
        });

        const results = await Promise.all(semuaFetch);
        results.forEach(item => {
            if (!item) return;

            let namaKecamatan = capitalize(item.slug);
            const props = item.geo.features?.[0]?.properties;
            if (props && (props.name || props.NAMOBJ || props.nama)) {
                namaKecamatan = props.name || props.NAMOBJ || props.nama;
            }

            const layer = L.geoJSON(item.geo, {
                style: { color: '#8a8a8a', weight: 1, fillColor: '#8a8a8a', fillOpacity: 0.03 },
                interactive: true
            });

            layer.on('mouseover', (e) => e.target.setStyle({ fillOpacity: 0.15, weight: 2 }));
            layer.on('mouseout', (e) => e.target.setStyle({ fillOpacity: 0.03, weight: 1 }));
            layer.bindTooltip(namaKecamatan, { sticky: true });

            kecamatanLayerGroup.addLayer(layer);
        });

        const toggleKecamatanVisibility = () => {
            if (map.getZoom() >= 11) {
                if (!map.hasLayer(kecamatanLayerGroup)) map.addLayer(kecamatanLayerGroup);
            } else {
                if (map.hasLayer(kecamatanLayerGroup)) map.removeLayer(kecamatanLayerGroup);
            }
        };

        map.on('zoomend', toggleKecamatanVisibility);
        toggleKecamatanVisibility();
    };

    renderMarkerOpd();
    renderBatasKabMagelang();
    renderKecamatan();

    window.addEventListener('resize', () => map?.invalidateSize());

    const petaContainer = document.getElementById('peta-map');
    if (petaContainer && window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(() => map?.invalidateSize());
        resizeObserver.observe(petaContainer);
    }
});