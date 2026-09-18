document.addEventListener("DOMContentLoaded", () => {
    const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
    const baseUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

    $.fn.dataTable.ext.errMode = 'none';

    // Inisialisasi DataTables Kepemilikan
    const table = $('#table-kepemilikan').DataTable({
        processing: true,
        serverSide: false,
        pagingType: "simple_numbers",
        language: {
            paginate: {
                previous: "<i class='bx bx-chevron-left'></i>",
                next: "<i class='bx bx-chevron-right'></i>"
            },
            emptyTable: "Silakan pilih OPD terlebih dahulu untuk melihat data."
        },
        ajax: {
            url: `${baseUrl}input/kepemilikan/data`,
            data: (d) => {
                d.id_opd = $('#filter-opd').val();
            },
            dataSrc: 'data'
        },
columns: [
    { data: null, render: (data, type, row, meta) => meta.row + 1 },
    { data: 'nama_opd' },
    { data: 'jenis' },
    { data: 'merk' },
    { data: 'tahun' },
    { data: 'kondisi' },
    { data: 'keterangan' },
    { 
        data: 'foto_bukti',
        orderable: false,
        render: (data) => data ? `
            <a href="${baseUrl}uploads/kepemilikan/${data}" target="_blank">
                <img src="${baseUrl}uploads/kepemilikan/${data}" alt="Bukti" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
            </a>` : `<span class="badge bg-secondary" style="font-size:11px">No Photo</span>`
    },
    {
        data: null,
        orderable: false,
        render: (row) => `
            <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="${row.id}">
                <i class="bx bx-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="${row.id}">
                <i class="bx bx-trash"></i>
            </button>`
    }
]
    });

    const openModal = () => {
        const modalEl = document.getElementById('modalTambahKepemilikan');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            myModal.show();
        } else {
            $('#modalTambahKepemilikan').modal('show');
        }
    };

    const closeModal = () => {
        const modalEl = document.getElementById('modalTambahKepemilikan');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const myModal = bootstrap.Modal.getInstance(modalEl);
            myModal?.hide();
        } else {
            $('#modalTambahKepemilikan').modal('hide');
        }
    };

    // Event Listener Filter OPD
    $('#filter-opd').on('change', function () {
        const selectedOpd = $(this).val();
        $('#btn-tambah').prop('disabled', !selectedOpd);
        table.ajax.reload();
    });

    // Buka Modal Tambah
    $('#btn-tambah').on('click', () => {
        const currentOpd = $('#filter-opd').val();
        if (!currentOpd) {
            Swal.fire('Peringatan', 'Silakan pilih OPD terlebih dahulu!', 'warning');
            return;
        }

        $('#modalTambahKepemilikan .modal-title').text('Input Kepemilikan Barang');
        $('#form-tambah-kepemilikan')[0].reset();
        $('#form-tambah-kepemilikan input[name="id"]').val('');
        
        // Sembunyikan form dinamis saat tambah data baru
        $('#wrapper-reset').addClass('d-none');
        $('#wrapper-port').addClass('d-none');

        $('#modal-id-opd').val(currentOpd);
        $('#modal-select-opd-display').val(currentOpd);
        $('#preview-container-bukti').addClass('d-none');
        
        openModal();
    });

    // Preview Foto Bukti
// Preview Foto Bukti (Batas Upload di Front-End Maks. 3 MB)
    $('#input-foto-bukti').on('change', function () {
        const file = this.files[0];
        const maxSizeBytes = 3 * 1024 * 1024; // 3 MB

        if (file && file.size <= maxSizeBytes) {
            const reader = new FileReader();
            reader.onload = (e) => {
                $('#img-preview-bukti').attr('src', e.target.result);
                $('#preview-container-bukti').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        } else if (file) {
            Swal.fire('Error', 'Ukuran foto bukti maksimal 3 MB.', 'error');
            $(this).val('');
            $('#preview-container-bukti').addClass('d-none');
        }
    });

    // Submit Form
    $('#form-tambah-kepemilikan').on('submit', async function (e) {
        e.preventDefault();
        try {
            const res = await fetch(`${baseUrl}input/kepemilikan/store`, { 
                method: 'POST', 
                body: new FormData(this) 
            });
            const data = await res.json();

            if (data.status === 'success') {
                closeModal();
                this.reset();
                table.ajax.reload();
                Swal.fire('Berhasil!', data.message, 'success');
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        } catch (err) {
            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
        }
    });

    // Edit Data
    $('#table-kepemilikan').on('click', '.btn-edit', function () {
        const rowData = table.row($(this).closest('tr')).data();
        $('#modalTambahKepemilikan .modal-title').text('Edit Kepemilikan Barang');
        
        $('#form-tambah-kepemilikan input[name="id"]').val(rowData.id);
        $('#modal-id-opd').val(rowData.id_opd);
        $('#modal-select-opd-display').val(rowData.id_opd);
// Pilih id_barang lalu trigger change untuk membentuk dropdown model yang sesuai
        $('#form-tambah-kepemilikan select[name="id_barang"]').val(rowData.id_barang).trigger('change');
        
        // Set nilai model/merk setelah dropdown terbentuk
        $('#container-input-merk [name="merk"]').val(rowData.merk !== '-' ? rowData.merk : '');
        $('#form-tambah-kepemilikan input[name="tahun"]').val(rowData.tahun !== '-' ? rowData.tahun : '');
        $('#form-tambah-kepemilikan select[name="kondisi"]').val(rowData.kondisi);
        $('#form-tambah-kepemilikan textarea[name="keterangan"]').val(rowData.keterangan !== '-' ? rowData.keterangan : '');
        
        // --- ISI DATA BARU SAAT EDIT ---
        $('#form-tambah-kepemilikan input[name="jumlah"]').val(rowData.jumlah !== null ? rowData.jumlah : '');
        $('#form-tambah-kepemilikan input[name="jumlah_port"]').val(rowData.jumlah_port !== null ? rowData.jumlah_port : '');
        
        // Pancing event change agar form dinamis merespons jenis perangkat yang diedit
        $('#form-tambah-kepemilikan select[name="id_barang"]').trigger('change');

        if (rowData.foto_bukti) {
            $('#img-preview-bukti').attr('src', `${baseUrl}uploads/kepemilikan/${rowData.foto_bukti}`);
            $('#preview-container-bukti').removeClass('d-none');
        } else {
            $('#preview-container-bukti').addClass('d-none');
        }

        openModal();
    });

    // Hapus Data
    $('#table-kepemilikan').on('click', '.btn-hapus', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data kepemilikan alat di OPD ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`${baseUrl}input/kepemilikan/hapus/${id}`, { method: 'POST' });
                    const data = await res.json();
                    Swal.fire('Berhasil!', data.message, 'success');
                    table.ajax.reload();
                } catch (err) {
                    Swal.fire('Error', 'Gagal menghapus data.', 'error');
                }
            }
        });
    });

    // --- LOGIKA FORM DINAMIS BERDASARKAN JENIS PERANGKAT ---
$('select[name="id_barang"]').on('change', function() {
    let namaBarang = $(this).find('option:selected').text().toLowerCase();

    // Reset wrapper
    $('#wrapper-port').addClass('d-none');

    // Kosongkan nilai jika disembunyikan (agar tidak tersubmit tak sengaja)
    $('input[name="jumlah_port"]').val('');

    // "Total Port" cuma relevan untuk Switch
    if (namaBarang.includes('switch')) {
        $('#wrapper-port').removeClass('d-none');
    }
});

// Master Model Resmi Sesuai Spreadsheet
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

    // Logika Form Dinamis Saat Jenis Perangkat Dipilih
    $('select[name="id_barang"]').on('change', function () {
        let namaBarang = $(this).find('option:selected').text().toLowerCase();

        // 1. Tangani Wrapper Port (Khusus Switch)
        $('#wrapper-port').addClass('d-none');
        $('input[name="jumlah_port"]').val('');
        if (namaBarang.includes('switch')) {
            $('#wrapper-port').removeClass('d-none');
        }

        // 2. Tangani Dropdown Model / Merk
        let key = null;
        if (namaBarang.includes('access point') || namaBarang.includes('ap')) key = 'access point';
        else if (namaBarang.includes('router')) key = 'router';
        else if (namaBarang.includes('switch')) key = 'switch';
        else if (namaBarang.includes('kabel')) key = 'kabel';

        const container = $('#container-input-merk');
        container.empty();

        if (key && masterModelKatalog[key]) {
            let options = '<option value="">-- Pilih Model Resmi --</option>';
            masterModelKatalog[key].forEach(item => {
                options += `<option value="${item.val}">${item.label}</option>`;
            });
            container.html(`<select name="merk" class="form-select" required>${options}</select>`);
        } else {
            container.html('<input type="text" name="merk" class="form-control" placeholder="Merk / Tipe Perangkat" required>');
        }
    });

});