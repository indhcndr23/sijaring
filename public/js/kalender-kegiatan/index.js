const KalenderKegiatan = (() => {
  /* ================================
   STATE
   =================================*/
  const modalTarget = 'modal-form-kalender-kegiatan';
  const buttonEdit = 'btn-kalender-kegiatan-edit'
  const buttonDelete = 'btn-kalender-kegiatan-delete'
  const buttonRestore = 'btn-kalender-kegiatan-restore'
  const dataModal = 'kalender-kegiatan'
  const fixedColumn = {
    fixedColumns: {
      leftColumns: 3
    },
    scrollCollapse: true,
    scrollX: true
  }
  const calenderFilter = $('#calender-filter');
  const yearFilter = $('#year-filter');
  const monthFilter = $('#month-filter');
  const searchFilter = $('#search-filter');
  const penanggungJawabFilter = $('#penanggung-jawab-filter');

  let searchValue = '';
  /* ================================
  DATATABLE CONFIG
  =================================*/
  let dataTable;
  const table = $('#table-kalender-kegiatan');
  const pathUrl = '/kalenderkegiatan/get-data';
  /* ================================
  FILTER
  =================================*/
  const initFilter = () => {
    let prevValue = calenderFilter.val();

    calenderFilter.on('change', function () {
      if (this.value === prevValue) {
        yearFilter.removeClass('d-none');
        monthFilter.addClass('d-none');
      } else {
        monthFilter.removeClass('d-none');
      }
      dataTable.ajax.reload();
    });

    yearFilter.on('change', function () {
      dataTable.ajax.reload();
    });

    monthFilter.on('change', function () {
      dataTable.ajax.reload();
    });

    let delay;
    searchFilter.on('input', function () {
      clearTimeout(delay);
      delay = setTimeout(() => {
        dataTable.ajax.reload();
      }, 400);
    });
    penanggungJawabFilter.on('change', function () {
      dataTable.ajax.reload();
    });
  };

  const init = () => {
    dataTable = initDataTable({
      table,
      pathUrl,
      year: yearFilter.val(),
      buttonEdit,
      buttonDelete,
      buttonRestore,
      modalTarget,
      dataModal,
      fixedColumn,
    });

    // =========================
    // CONFIG
    // =========================

    // initModal();

    initFilter();
  };

  return { init };
})();

$(function () {
  KalenderKegiatan.init();
});