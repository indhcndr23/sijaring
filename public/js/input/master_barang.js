document.addEventListener("DOMContentLoaded", () => {
    const rawBaseUrl = window.baseUrl || `${window.location.origin}/`;
    const baseUrl = rawBaseUrl.endsWith('/') ? rawBaseUrl : `${rawBaseUrl}/`;

    $.fn.dataTable.ext.errMode = 'none';

const table = $('#table-master-barang').DataTable({
    processing: true,
    serverSide: false,
    order: [], // Menjaga urutan abjad dari server
    ajax: {
        url: `${baseUrl}input/barang/data`,
        dataSrc: 'data',
        error: (xhr) => console.error('DataTables Ajax Error:', xhr.responseText)
    },
    // ... sisa kode DataTables ke bawah tetap sama
        pagingType: "simple_numbers",
        language: {
            paginate: {
                previous: "<i class='bx bx-chevron-left'></i>",
                next: "<i class='bx bx-chevron-right'></i>"
            }
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'jenis' },
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

    const modalEl = document.getElementById('modalMasterBarang');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

    $('button[data-bs-target="#modalMasterBarang"]').on('click', () => {
        document.getElementById('modalMasterBarang').querySelector('.modal-title').textContent = 'Tambah Jenis Barang';
        $('#form-master-barang')[0].reset();
        $('#form-master-barang input[name="id"]').val('');
    });

$('#form-master-barang').on('submit', async function (e) {
    e.preventDefault();

    // Cek validasi bawaan browser
    if (!this.checkValidity()) {
        e.stopPropagation();
        $(this).addClass('was-validated');
        return;
    }

    try {
        const res = await fetch(`${baseUrl}input/barang/store`, { method: 'POST', body: new FormData(this) });
        const data = await res.json();
        
        if (data.status === 'success') {
            modal?.hide();
            this.reset();
            $(this).removeClass('was-validated');
            table.ajax.reload();
            Swal.fire('Berhasil!', data.message, 'success');
        } else {
            Swal.fire('Gagal!', data.message, 'error');
        }
    } catch (err) {
        Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
    }
});

    $('#table-master-barang').on('click', '.btn-edit', function () {
        const rowData = table.row($(this).closest('tr')).data();
        document.getElementById('modalMasterBarang').querySelector('.modal-title').textContent = 'Edit Jenis Barang';
        $('#form-master-barang input[name="id"]').val(rowData.id);
        $('#form-master-barang input[name="jenis"]').val(rowData.jenis);
        modal?.show();
    });

    $('#table-master-barang').on('click', '.btn-hapus', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Jenis Barang?',
            text: "Data kepemilikan jenis barang ini di OPD akan ikut terhapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`${baseUrl}input/barang/hapus/${id}`, { method: 'POST' });
                    const data = await res.json();
                    Swal.fire('Berhasil!', data.message, 'success');
                    table.ajax.reload();
                } catch (err) {
                    Swal.fire('Error', 'Gagal menghapus data.', 'error');
                }
            }
        });
    });
});