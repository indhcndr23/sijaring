<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<link href="<?= base_url('skote/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('css/datatable.css') ?>" rel="stylesheet" type="text/css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="card card-dashboard p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="card-title-dashboard m-0">Data Index OPD</h6>
                <div class="d-flex gap-2">
                    <select id="filter-status" class="form-select form-select-sm w-auto">
                        <option value="">Semua Status</option>
                        <option value="hijau">Hijau</option>
                        <option value="kuning">Kuning</option>
                        <option value="merah">Merah</option>
                        <option value="abu">Belum Dinilai</option>
                    </select>
                    <select id="filter-jenis" class="form-select form-select-sm w-auto">
                        <option value="">Semua Jenis</option>
                        <option value="FO">FO</option>
                        <option value="Broadband">Broadband</option>
                    </select>
                </div>
            </div>
            <table id="table-dashboard" class="table table-bordered table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama OPD</th>
                        <th>Nilai Index</th>
                        <th>Jenis Koneksi</th>
                        <th>Tren</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailTabel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailTabelLabel">Detail OPD</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalDetailTabelBody">
        Memuat data...
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="<?= base_url('skote/assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('skote/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
    window.tabelData = <?= json_encode($data['opdList']) ?>;
    window.baseUrl = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/dashboard/tabel.js') ?>"></script>
<?= $this->endSection() ?>