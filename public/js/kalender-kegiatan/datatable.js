/**
 * Initialize DataTable dengan konfigurasi server-side
 *
 * @param {jQuery} table         - Element table dalam bentuk jQuery (ex: $('#table-id'))
 * @param {string} pathUrl       - Endpoint API untuk mengambil data
 * @param {Array} columnConfig   - Konfigurasi kolom tambahan (selain nomor & action)
 * @param {string} buttonEdit    - Class untuk tombol edit
 * @param {string} buttonDelete  - Class untuk tombol delete
 * @returns {DataTable}          - Instance DataTable yang bisa digunakan untuk .row(), .ajax.reload(), dll
 */
function initDataTable({ table, pathUrl, year, columnConfig, buttonEdit, buttonDelete, buttonRestore, modalTarget, dataModal, fixedColumn }) {
  if (!table || !table.length) {
    console.error("Table tidak ditemukan");
    return;
  }

  function getWeeksInMonth(month, year) {
    const start = new Date(year, month - 1, 1);
    const end = new Date(year, month, 0);

    const totalDays = end.getDate();
    const firstDay = start.getDay() === 0 ? 7 : start.getDay();

    return Math.ceil((totalDays + firstDay - 1) / 7);
  }

  const bulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];

  // =========================
  // FUNCTION HITUNG MINGGU
  // =========================
  function getWeeksInMonth(month, year) {
    const days = new Date(year, month, 0).getDate();
    return Math.ceil(days / 7);
  }

  // =========================
  // GENERATE HEADER + COLUMNS
  // =========================
  let headerTop = '<tr>';
  let headerBottom = '<tr>';

  const dynamicColumns = [];

  // kolom tetap
  headerTop += `<th rowspan="2">No</th>`;
  headerTop += `<th rowspan="2">Aksi</th>`;
  headerTop += `<th rowspan="2">Penanggung Jawab</th>`;
  headerTop += `<th rowspan="2">Nama Kegiatan</th>`;

  bulan.forEach((b, index) => {
    const m = index + 1;
    const weeks = getWeeksInMonth(m, year);

    // HEADER BULAN
    headerTop += `<th colspan="${weeks}" class="datatable-header text-center">${b}</th>`;

    for (let i = 1; i <= weeks; i++) {
      // HEADER MINGGU
      headerBottom += `<th class="datatable-header text-center rounded-0">Minggu ${i}</th>`;

      // COLUMN DATA
      dynamicColumns.push({
        title: `M${i}`,
        data: null,
        orderable: false,
        className: 'text-center',
        createdCell: function (td, cellData, rowData) {
          const bulan = String(m).padStart(2, '0');

          if (rowData.bulan?.[bulan]?.[`m${i}`]) {
            td.style.backgroundColor = '#2f7d57';
          }
        },
        render: () => ''
      });
    }
  });

  headerTop += '</tr>';
  headerBottom += '</tr>';

  // inject ke thead
  table.find('thead').html(headerTop + headerBottom);


  // check mobile device

  const isMobile = window.innerWidth < 768;


  // =========================
  // INIT DATATABLE
  // =========================

  return table.DataTable({
    processing: true,
    serverSide: true,
    scrollX: true,
    info: false,
    searching: false,
    lengthChange: false,
    pageLength: 10,
    ...(isMobile ? {} : {
      fixedColumns: {
        leftColumns: 3
      }
    }),
    ajax: {
      url: pathUrl,
      type: 'GET',
      data: function (d) {
        const orderColumnIndex = d.order?.[0]?.column;
        const orderDir = d.order?.[0]?.dir;

        // mapping order column
        const orderColumnName = d.columns?.[orderColumnIndex]?.data;

        return {
          draw: d.draw,

          start: d.start,
          length: d.length,

          // sorting
          orderBy: orderColumnName,
          orderDir: orderDir,

          // filter
          search: $('#search-filter').val(),
          kalender: $('#calender-filter').val(),
          year: $('#year-filter').val(),
          month: $('#month-filter').val(),
          penanggungJawab: $('#penanggung-jawab-filter').val()
        };
      }
    },

    columns: [
      {
        data: null,
        className: 'text-center',
        orderable: false,
        searchable: false,
        render: (data, type, row, meta) => {
          return meta.row + meta.settings._iDisplayStart + 1;
        }
      },
      {
        data: null,
        className: 'text-center',
        orderable: false,
        render: () => `
          <span class="icon-click">
            <i class="fa fa-eye" style="color: #288052;" aria-hidden="true"></i>
          </span>
          <span class="icon-click">
            <i class="bx bx-pencil" style="color: #288052;" aria-hidden="true"></i>
          </span>
          <span class="icon-click">
            <i class="bx bx-trash" style="color: #288052;" aria-hidden="true"></i>
          </span>
        `
      },
      {
        data: 'penanggung_jawab',
        orderable: true
      },
      {
        data: 'nama_kegiatan',
        orderable: true
      },
      ...dynamicColumns
    ],
    language: {
      paginate: {
        next: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" id="forward-arrow">
                <path fill="none" d="M24 24H0V0h24v24z" opacity=".87"></path>
                <path d="M7.38 21.01c.49.49 1.28.49 1.77 0l8.31-8.31c.39-.39.39-1.02 0-1.41L9.15 2.98c-.49-.49-1.28-.49-1.77 0s-.49 1.28 0 1.77L14.62 12l-7.25 7.25c-.48.48-.48 1.28.01 1.76z"></path>
              </svg>
              `,
        previous: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" id="back-arrow">
                    <path fill="none" d="M0 0h24v24H0V0z" opacity=".87"></path>
                    <path d="M16.62 2.99c-.49-.49-1.28-.49-1.77 0L6.54 11.3c-.39.39-.39 1.02 0 1.41l8.31 8.31c.49.49 1.28.49 1.77 0s.49-1.28 0-1.77L9.38 12l7.25-7.25c.48-.48.48-1.28-.01-1.76z"></path>
                  </svg>`,
        first: 'First',
        last: 'Last'
      }
    }
  });
};


