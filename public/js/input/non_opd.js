document.addEventListener("DOMContentLoaded", () => {
    const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
    const baseUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

    $.fn.dataTable.ext.errMode = 'none';

    const config = window.petaInputConfig || {
        geojsonUrl: null,
        defaultLat: -7.551720,
        defaultLng: 110.228210
    };

    let map = null;
    let marker = null;

    const inputLat = document.getElementById('input-latitude');
    const inputLng = document.getElementById('input-longitude');
    const inputAlamat = document.getElementById('input-alamat');

    const redPinIcon = L.divIcon({
        className: 'custom-input-icon',
        html: '<div style="background-color:#e14343;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2.5px solid #ffffff;box-shadow:0 3px 8px rgba(0,0,0,0.4);color:#ffffff;font-size:18px;"><i class="bx bxs-map-pin"></i></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    // Inisialisasi DataTables persis seperti Kepemilikan
    const tableNonOpd = $('#table-non-opd').DataTable({
        processing: true,
        serverSide: false,
        pagingType: "simple_numbers",
        language: {
            paginate: {
                previous: "<i class='bx bx-chevron-left'></i>",
                next: "<i class='bx bx-chevron-right'></i>"
            },
            emptyTable: "Belum ada data lokasi Non-OPD."
        },
        ajax: {
            url: `${baseUrl}input/non-opd/data`,
            dataSrc: 'data'
        },
        columns: [
            { data: null, render: (d, t, r, m) => m.row + 1 },
            { data: 'nama' },
            { data: 'nama_pic', render: (d) => d || '-' },
            { data: 'no_hp_pic', render: (d) => d || '-' },
            { data: 'alamat', render: (d) => d || '-' },
            { data: 'jumlah_pegawai', render: (d) => d || 0 },
            { data: 'jumlah_perangkat', render: (d) => d || 0 },
            { data: 'luas_ruangan', render: (d) => d || 0 },
            { data: 'rata_tamu', render: (d) => d || 0 },
            { data: 'jenis_dinding', render: (d) => d || '-' },
            { data: 'jumlah_lantai', render: (d) => d || 0 },
            { data: 'jumlah_ruangan', render: (d) => d || 0 },
            { data: 'jenis_kabel', render: (d) => d || '-' },
            {
                data: null,
                orderable: false,
                render: (data, type, row) => {
                    const rowJson = JSON.stringify(row).replace(/'/g, "&apos;");
                    return `
                        <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="${row.id}" data-row='${rowJson}'>
                            <i class="bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="${row.id}">
                            <i class="bx bx-trash"></i>
                        </button>`;
                }
            }
        ]
    });

    const updateCoordinates = (lat, lng) => {
        if (inputLat) inputLat.value = parseFloat(lat).toFixed(7);
        if (inputLng) inputLng.value = parseFloat(lng).toFixed(7);
    };

    // Peta Leaflet
    const initInputMap = async (lat, lng) => {
        const startLat = lat ? parseFloat(lat) : config.defaultLat;
        const startLng = lng ? parseFloat(lng) : config.defaultLng;

        if (!map) {
            map = L.map('map-opd', { zoomControl: true, attributionControl: false }).setView([startLat, startLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            marker = L.marker([startLat, startLng], { icon: redPinIcon, draggable: true }).addTo(map);

            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                updateCoordinates(pos.lat, pos.lng);
            });

            map.on('click', (e) => {
                marker.setLatLng([e.latlng.lat, e.latlng.lng]);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });
        } else {
            marker.setLatLng([startLat, startLng]);
            map.setView([startLat, startLng], 15);
        }

        setTimeout(() => map.invalidateSize(), 300);
    };

    const modalNonOpd = document.getElementById('modalTambahNonOpd');
    if (modalNonOpd) {
        modalNonOpd.addEventListener('shown.bs.modal', () => {
            initInputMap(inputLat ? inputLat.value : null, inputLng ? inputLng.value : null);
        });

        modalNonOpd.addEventListener('hidden.bs.modal', () => {
            document.getElementById('form-tambah-non-opd').reset();
            document.querySelector('#form-tambah-non-opd input[name="id_opd"]').value = '';
            document.getElementById('modal-non-opd-title').innerText = "Input Lokasi Non-OPD (Wisata / Fasum)";
        });
    }

    // Edit Data
    $('#table-non-opd').on('click', '.btn-edit', function () {
        let rowData = $(this).data('row');
        if (typeof rowData === 'string') rowData = JSON.parse(rowData);

        document.getElementById('modal-non-opd-title').innerText = "Edit Lokasi Non-OPD";
        document.querySelector('#form-tambah-non-opd input[name="id_opd"]').value = rowData.id;
        document.querySelector('#form-tambah-non-opd input[name="nama_opd"]').value = rowData.nama || '';

        document.querySelector('#form-tambah-non-opd input[name="jumlah_pegawai"]').value = rowData.jumlah_pegawai || 0;
        document.querySelector('#form-tambah-non-opd input[name="jumlah_perangkat"]').value = rowData.jumlah_perangkat || 0;
        document.querySelector('#form-tambah-non-opd input[name="luas_ruangan"]').value = rowData.luas_ruangan || 0;
        document.querySelector('#form-tambah-non-opd input[name="rata_tamu"]').value = rowData.rata_tamu || 0;
        document.querySelector('#form-tambah-non-opd select[name="jenis_dinding"]').value = rowData.jenis_dinding !== '-' ? rowData.jenis_dinding : '';
        document.querySelector('#form-tambah-non-opd input[name="jumlah_lantai"]').value = rowData.jumlah_lantai || 1;
        document.querySelector('#form-tambah-non-opd input[name="jumlah_ruangan"]').value = rowData.jumlah_ruangan || 1;
        
        document.querySelector('#form-tambah-non-opd input[name="nama_pic"]').value = (rowData.nama_pic !== '-') ? rowData.nama_pic : '';
        document.querySelector('#form-tambah-non-opd input[name="no_hp_pic"]').value = (rowData.no_hp_pic !== '-') ? rowData.no_hp_pic : '';
        document.querySelector('#form-tambah-non-opd textarea[name="alamat"]').value = (rowData.alamat !== '-') ? rowData.alamat : '';

        $(`input[name="jenis_kabel"]`).prop('checked', false);
        if (rowData.jenis_kabel && rowData.jenis_kabel !== '-') {
            $(`input[name="jenis_kabel"][value="${rowData.jenis_kabel}"]`).prop('checked', true);
        }

        if (rowData.latitude && rowData.longitude) {
            updateCoordinates(rowData.latitude, rowData.longitude);
            marker?.setLatLng([rowData.latitude, rowData.longitude]);
            map?.setView([rowData.latitude, rowData.longitude], 16);
        }

        const bsModal = new bootstrap.Modal(modalNonOpd);
        bsModal.show();
    });

    // Hapus Data
    $('#table-non-opd').on('click', '.btn-hapus', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data lokasi Non-OPD ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`${baseUrl}input/non-opd/hapus/${id}`);
                    const data = await res.json();
                    Swal.fire('Terhapus!', data.message, 'success');
                    tableNonOpd.ajax.reload();
                } catch (err) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            }
        });
    });

    // Submit Form
    const formNonOpd = document.getElementById('form-tambah-non-opd');
    if (formNonOpd) {
        formNonOpd.addEventListener('submit', async function (e) {
            e.preventDefault();

            try {
                const res = await fetch(`${baseUrl}input/non-opd/store`, { 
                    method: 'POST', 
                    body: new FormData(this) 
                });
                const data = await res.json();

                if (!res.ok) throw data;

                Swal.fire('Berhasil', data.message || 'Data berhasil disimpan', 'success');
                const modalInstance = bootstrap.Modal.getInstance(modalNonOpd);
                modalInstance?.hide();
                this.reset();
                tableNonOpd.ajax.reload();
            } catch (err) {
                Swal.fire('Gagal', err.message || 'Gagal menyimpan data.', 'error');
            }
        });
    }
});