document.addEventListener("DOMContentLoaded", () => {
    const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
    const baseUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

    let chartOpdInstance = null;
    let chartUmurInstance = null;

    const loadDataStatistik = async () => {
        try {
            const res = await fetch(`${baseUrl}statistik/get-data`);
            if (!res.ok) throw new Error(`HTTP Status ${res.status}`);
            
            const data = await res.json();

            if (data.status === "success") {
                renderCards(data.summary || {});
                renderChartOpd(data.barang_opd || []);
                renderChartUmur(data.umur_stat || {});
                renderTablePerangkatTua(data.perangkat_tua || []);
                renderTablePerangkatRusak(data.perangkat_rusak || []);

                // Render 4 Tabel Index Evaluasi
                renderFasilitasTerburuk(data.fasilitas_terburuk || []);
                renderKapasitasTerburuk(data.kapasitas_terburuk || []);
                renderKinerjaTerburuk(data.kinerja_terburuk || []);
                renderTotalIndexTerburuk(data.total_index_terburuk || []);
            }
        } catch (err) {
            console.error("Gagal mengambil data statistik:", err);
        }
    };

    const renderCards = (summary = {}) => {
        $('#lbl-total-opd').text(summary.total_opd || 0);
        $('#lbl-total-aset').html(`${summary.total_aset || 0} <small class="fs-6">Unit</small>`);
        $('#lbl-status-device').html(`${summary.kondisi_rusak || 0} <small class="fs-6 text-danger">Unit Rusak</small>`);
        $('#lbl-peremajaan').html(`${summary.perlu_peremajaan || 0} <small class="fs-6">Unit</small>`);
    };

    const renderChartOpd = (barangOpd = []) => {
        const topOpd = barangOpd.slice(0, 10);
        const labels = topOpd.length ? topOpd.map(item => item.nama_opd) : ['Belum Ada Data'];
        const dataValues = topOpd.length ? topOpd.map(item => item.total_barang) : [0];

        const canvas = document.getElementById('chartOpd');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (chartOpdInstance) chartOpdInstance.destroy();

        chartOpdInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Unit Barang',
                    data: dataValues,
                    backgroundColor: '#1a6b3a',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    };

    const renderChartUmur = (umurStat = {}) => {
        const canvas = document.getElementById('chartUmur');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (chartUmurInstance) chartUmurInstance.destroy();

        const v1 = parseInt(umurStat.kurang_2_tahun || 0, 10);
        const v2 = parseInt(umurStat['2_sampai_5_tahun'] || 0, 10);
        const v3 = parseInt(umurStat.lebih_5_tahun || 0, 10);

        chartUmurInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['< 2 Tahun', '2 - 5 Tahun', '> 5 Tahun (Tua)'],
                datasets: [{
                    data: (v1 === 0 && v2 === 0 && v3 === 0) ? [0, 0, 1] : [v1, v2, v3],
                    backgroundColor: (v1 === 0 && v2 === 0 && v3 === 0) 
                        ? ['#e2e8f0', '#e2e8f0', '#cbd5e1'] 
                        : ['#34c38f', '#50a5f1', '#f46a6a']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    };

    const renderTablePerangkatTua = (perangkatTua = []) => {
        const tbody = $('#table-perangkat-tua tbody');

        if (perangkatTua.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada perangkat tua.</td></tr>');
            return;
        }

        const html = perangkatTua.map(pt => `
            <tr>
                <td class="fw-semibold">${pt.nama_opd || '-'}</td>
                <td>${pt.nama_barang || '-'} <small class="text-muted">(${pt.merk || '-'})</small></td>
                <td>${pt.tahun || '-'}</td>
                <td><span class="badge bg-danger">${pt.umur_tahun || 0} Thn</span></td>
                <td>${pt.kondisi || '-'}</td>
            </tr>`
        ).join('');

        tbody.html(html);
    };

    const renderTablePerangkatRusak = (perangkatRusak = []) => {
        const tbody = $('#table-network tbody');

        if (perangkatRusak.length === 0) {
            tbody.html('<tr><td colspan="3" class="text-center text-muted py-3">Seluruh perangkat dalam kondisi baik.</td></tr>');
            return;
        }

        const html = perangkatRusak.map(pr => {
            const badgeClass = pr.kondisi === 'Rusak Berat' ? 'bg-danger' : 'bg-warning text-dark';
            return `
            <tr>
                <td class="fw-semibold">${pr.nama_opd || '-'}</td>
                <td>${pr.nama_barang || '-'} <small class="text-muted">(${pr.merk || '-'})</small></td>
                <td><span class="badge ${badgeClass}">${pr.kondisi}</span></td>
            </tr>`;
        }).join('');

        tbody.html(html);
    };

    // --- 4 Renderer Tabel Index Baru ---

    const renderFasilitasTerburuk = (data = []) => {
        const tbody = $('#table-fasilitas-terburuk tbody');
        if (!data.length) {
            tbody.html('<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data fasilitas terburuk.</td></tr>');
            return;
        }
        tbody.html(data.map(item => `
            <tr>
                <td class="fw-semibold">${item.nama_opd}</td>
                <td class="text-center"><span class="badge bg-danger fs-6">${item.total_rusak} Unit</span></td>
                <td class="text-center"><span class="badge bg-danger-subtle text-danger">Perlu Perbaikan</span></td>
            </tr>
        `).join(''));
    };

    const renderKapasitasTerburuk = (data = []) => {
        const tbody = $('#table-kapasitas-terburuk tbody');
        if (!data.length) {
            tbody.html('<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data kapasitas terburuk.</td></tr>');
            return;
        }
        tbody.html(data.map(item => `
            <tr>
                <td class="fw-semibold">${item.nama_opd}</td>
                <td class="text-center">${item.jumlah_pegawai || 0} Orang</td>
                <td class="text-center">${(parseInt(item.jumlah_port||0) + parseInt(item.jumlah_access_point||0))} Titik</td>
                <td class="text-center"><span class="badge bg-warning text-dark">${item.rasio_kapasitas || 0}</span></td>
            </tr>
        `).join(''));
    };

    const renderKinerjaTerburuk = (data = []) => {
        const tbody = $('#table-kinerja-terburuk tbody');
        if (!data.length) {
            tbody.html('<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data kinerja terburuk.</td></tr>');
            return;
        }
        tbody.html(data.map(item => `
            <tr>
                <td class="fw-semibold">${item.nama_opd}</td>
                <td class="text-center"><span class="badge bg-warning fs-6 text-dark">${item.total_perangkat_tua} Unit</span></td>
                <td class="text-center"><span class="badge bg-warning-subtle text-warning">Peremajaan</span></td>
            </tr>
        `).join(''));
    };

const renderTotalIndexTerburuk = (data = []) => {
        const tbody = $('#table-total-index-terburuk tbody');
        if (!data.length) {
            tbody.html('<tr><td colspan="4" class="text-center text-muted py-3">Seluruh OPD dalam kondisi baik (Index &ge; 50).</td></tr>');
            return;
        }
        tbody.html(data.map(item => `
            <tr>
                <td class="fw-semibold">${item.nama_opd}</td>
                <td class="text-center text-danger fw-bold">${item.total_rusak}</td>
                <td class="text-center text-warning fw-bold">${item.total_tua}</td>
                <td class="text-center"><span class="badge bg-danger fs-6">${item.skor_index}</span></td>
            </tr>
        `).join(''));
    };

    loadDataStatistik();
});