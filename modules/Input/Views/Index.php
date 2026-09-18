<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<link href="<?= base_url('skote/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('css/datatable.css') ?>" rel="stylesheet" type="text/css">
<style>
  .nav-pills .nav-link.active {
    background-color: #1a6b3a !important;
    color: #fff !important;
  }

  .nav-pills .nav-link {
    color: #495057;
    background-color: #f3f4f6;
  }

  .card-dimensi {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.2s;
  }

  .card-dimensi:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .table-custom-header thead th {
    background-color: #1a6b3a !important;
    color: #ffffff !important;
    vertical-align: middle;
    border-color: #1a6b3a !important;
  }

  /* Menegaskan border tabel dan memberikan efek hover lembut */
  #table-variabel tbody tr:hover {
    background-color: #f8fafc !important;
  }

  #table-variabel td {
    padding: 12px 8px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
  <div class="container-fluid">
    <div class="card card-dashboard p-4 border-0 shadow-sm rounded-4">

      <!-- NAV TAB BERSAMPINGAN -->
      <ul class="nav nav-pills mb-4 gap-2" id="indexTabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active fw-semibold px-4 py-2" id="tab-dimensi-btn" data-bs-toggle="pill" data-bs-target="#tab-dimensi" type="button">
            <i class="bx bx-grid-alt me-1"></i> Dimensi
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link fw-semibold px-4 py-2" id="tab-variabel-btn" data-bs-toggle="pill" data-bs-target="#tab-variabel" type="button">
            <i class="bx bx-slider-alt me-1"></i> Variabel
          </button>
        </li>
      </ul>

      <div class="tab-content">

        <!-- TAB 1: DIMENSI -->
        <div class="tab-pane fade show active" id="tab-dimensi">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="fw-bold mb-1">Daftar Dimensi Penilaian</h5>
            </div>
            <button type="button" class="btn btn-success btn-sm px-3" onclick="modalTambahDimensi()">
              <i class="bx bx-plus me-1"></i> Tambah
            </button>
          </div>

          <div class="row g-3" id="grid-dimensi-container">
            <div class="col-12 text-center py-4 text-muted">Memuat data...</div>
          </div>
        </div>

        <!-- TAB 2: VARIABEL -->
        <div class="tab-pane fade" id="tab-variabel">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="fw-bold mb-1">Daftar Variabel Penilaian</h5>
            </div>
            <button type="button" class="btn btn-success btn-sm px-3" onclick="modalTambahVariabel()">
              <i class="bx bx-plus me-1"></i> Tambah
            </button>
          </div>

          <div class="table-responsive">
            <table id="table-variabel" class="table table-bordered align-middle w-100 table-custom-header">
              <thead>
                <tr>
                  <th class="text-center" style="width: 200px;">Dimensi</th>
                  <th style="width: 40px;"></th>
                  <th>Nama Variabel</th>
                  <th class="text-center">Acuan Data</th>
                  <th>Rumus</th>
                  <th class="text-center">Min - Max</th>
                  <th class="text-center">Arah</th>
                  <th class="text-center">Bobot</th>
                  <th class="text-center" style="width: 90px;">Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- MODAL EDIT/TAMBAH DIMENSI -->
<div class="modal fade" id="modalDimensi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-dimensi">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title text-white" id="modalDimensiTitle">Tambah</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_dimensi" id="dimensi_id">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Dimensi</label>
            <input type="text" name="nama_dimensi" id="dimensi_nama" class="form-control" required placeholder="Contoh: Dimensi 1 (Infrastruktur)">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Bobot Dimensi (0 - 1.0)</label>
            <input type="number" step="0.01" min="0" max="1" name="bobot" id="dimensi_bobot" class="form-control" required placeholder="0.50">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDIT/TAMBAH VARIABEL -->
<div class="modal fade" id="modalVariabel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-variabel">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title text-white" id="modalVariabelTitle">Tambah</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="id_variabel" id="var_id">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Dimensi Terikat</label>
              <select name="id_dimensi" id="var_dimensi" class="form-select" required>
                <option value="">-- Pilih Dimensi --</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Nama Variabel</label>
              <input type="text" name="nama_variabel" id="var_nama" class="form-control" required placeholder="Contoh: Usia Perangkat / Kepadatan">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Acuan Sumber Data</label>
              <select name="sumber_tipe" id="var_sumber_tipe" class="form-select" required>
                <option value="OPD">Karakteristik OPD</option>
                <option value="KEPEMILIKAN">Kepemilikan Langsung</option>
                <option value="KOMBINASI">Kombinasi (Formula)</option>
              </select>
            </div>

            <div class="col-md-6 mb-3" id="wrapper-field-source">
              <label class="form-label fw-bold">Field Database</label>
              <select name="field_source" id="var_field_source" class="form-select">
              </select>
            </div>
          </div>

          <div class="mb-3 d-none" id="wrapper-formula">
            <label class="form-label fw-bold">Rumus Formula Kombinasi</label>
            <input type="text" name="formula" id="var_formula" class="form-control text-danger fw-bold" placeholder="{OPD.jumlah_pegawai} / {KEPEMILIKAN.jumlah}">
          </div>

          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label fw-bold">Nilai Min</label>
              <input type="number" step="any" name="nilai_min" id="var_min" class="form-control" required placeholder="0">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label fw-bold">Nilai Max</label>
              <input type="number" step="any" name="nilai_max" id="var_max" class="form-control" required placeholder="100">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label fw-bold">Arah Trend</label>
              <select name="arah" id="var_arah" class="form-select" required>
                <option value="MAX">↑ MAX (Ke Atas)</option>
                <option value="MIN">↓ MIN (Ke Bawah)</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label fw-bold">Bobot (0 - 1.0)</label>
              <input type="number" step="0.01" min="0" max="1" name="bobot" id="var_bobot" class="form-control" required placeholder="0.25">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Variabel</button>
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
<script src="<?= base_url('js/input/index.js') ?>"></script>
<?= $this->endSection() ?>