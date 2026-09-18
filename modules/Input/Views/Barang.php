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
        <h6 class="card-title-dashboard m-0">Master Jenis Barang</h6>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalMasterBarang">
          <i class="bx bx-plus"></i> Tambah
        </button>
      </div>
      <div class="table-responsive">
        <table id="table-master-barang" class="table table-bordered table-striped dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th style="width: 80px;">No</th>
              <th>Jenis Barang</th>
              <th style="width: 150px;">Tindakan</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit Jenis Barang -->
<div class="modal fade" id="modalMasterBarang" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-master-barang">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Jenis Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" value="">
          <div class="mb-3">
            <label class="form-label">Jenis Barang <span class="text-danger">*</span></label>
            <input type="text" name="jenis" class="form-control" required placeholder="Contoh: Router, Access Point, Modem">
            <div class="invalid-feedback">Jenis barang wajib diisi!</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="<?= base_url('skote/assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('skote/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('js/input/master_barang.js') ?>"></script>
<?= $this->endSection() ?>