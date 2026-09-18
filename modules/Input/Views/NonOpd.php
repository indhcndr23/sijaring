<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<link href="<?= base_url('skote/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('css/datatable.css') ?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
  #map-opd {
    height: 250px;
    width: 100%;
    border-radius: 6px;
    border: 1px solid #ced4da;
  }

  .custom-input-icon {
    background: transparent !important;
    border: none !important;
  }

  /* Styling modal scroll */
  #modalTambahNonOpd .modal-dialog {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
  }

  #modalTambahNonOpd .modal-content {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
  }

  #modalTambahNonOpd .modal-body {
    overflow-y: auto !important;
    max-height: calc(90vh - 120px);
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
  <div class="container-fluid">
    <div class="card card-dashboard p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="card-title-dashboard m-0">Data Lokasi Non-OPD</h6>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahNonOpd">
          <i class="bx bx-plus"></i> Tambah
        </button>
      </div>
      <div class="table-responsive">
        <table id="table-non-opd" class="table table-bordered table-striped dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Lokasi</th>
              <th>PIC</th>
              <th>No. HP</th>
              <th>Alamat</th>
              <th>Pegawai</th>
              <th>Perangkat</th>
              <th>Luas (m²)</th>
              <th>Rata Tamu</th>
              <th>Dinding</th>
              <th>Lantai</th>
              <th>Ruangan</th>
              <th>Jenis Koneksi</th>
              <th>Tindakan</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit Non-OPD -->
<div class="modal fade" id="modalTambahNonOpd" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-tambah-non-opd" method="POST" action="javascript:void(0);">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-non-opd-title">Input Lokasi Non-OPD (Wisata / Fasum)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_opd" value="">

          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
              <input type="text" name="nama_opd" id="input-nama-opd" class="form-control" placeholder="Contoh: Candi Borobudur, Puskesmas Mungkid, Pasar Mertoyudan..." required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Jumlah Pegawai <span class="text-danger">*</span></label>
              <input type="number" name="jumlah_pegawai" class="form-control" required min="0" placeholder="0">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Jumlah Perangkat <span class="text-danger">*</span></label>
              <input type="number" name="jumlah_perangkat" class="form-control" required min="0" placeholder="Laptop/HP">
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Luas Ruangan (m²) <span class="text-danger">*</span></label>
              <input type="number" name="luas_ruangan" class="form-control" placeholder="Contoh: 120" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Rata-Rata Tamu / Hari <span class="text-danger">*</span></label>
              <input type="number" name="rata_tamu" class="form-control" placeholder="Contoh: 25" min="0" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Jenis Dinding <span class="text-danger">*</span></label>
              <select name="jenis_dinding" class="form-select" required>
                <option value="">-- Pilih Dinding --</option>
                <option value="Tembok/Beton">Tembok / Beton</option>
                <option value="Kaca">Kaca</option>
                <option value="Partisi/Gypsum">Partisi / Gypsum</option>
                <option value="Kayu">Kayu</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Jumlah Lantai <span class="text-danger">*</span></label>
              <input type="number" name="jumlah_lantai" class="form-control" placeholder="Contoh: 2" min="1" value="1" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Jumlah Ruangan <span class="text-danger">*</span></label>
              <input type="number" name="jumlah_ruangan" class="form-control" placeholder="Contoh: 8" min="1" value="1" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
              <input type="text" name="nama_pic" class="form-control" required placeholder="Nama lengkap PIC">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">No. HP PIC <span class="text-danger">*</span></label>
              <input type="text" name="no_hp_pic" id="input-no-hp-pic" class="form-control" placeholder="Contoh: 08123456789" required minlength="9" maxlength="15" pattern="^[0-9]+$">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Jenis Koneksi (Pilih Salah Satu) <span class="text-danger">*</span></label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="jenis_kabel" id="opdKabelFO" value="FO" required>
              <label class="form-check-label" for="opdKabelFO">FO (Fiber Optic)</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="jenis_kabel" id="opdKabelBroadband" value="Broadband" required>
              <label class="form-check-label" for="opdKabelBroadband">Broadband</label>
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label">Cari Lokasi di Peta</label>
            <div class="input-group">
              <input type="text" id="search-map-input" class="form-control" placeholder="Ketik nama tempat/jalan...">
              <button class="btn btn-outline-secondary" type="button" id="btn-search-map">
                <i class="bx bx-search"></i> Cari
              </button>
            </div>
          </div>

          <div class="mb-3">
            <div id="map-opd"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="alamat" id="input-alamat" class="form-control" rows="2" required placeholder="Alamat jalan, desa, kecamatan..."></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Latitude <span class="text-danger">*</span></label>
              <input type="text" name="latitude" id="input-latitude" class="form-control" readonly required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Longitude <span class="text-danger">*</span></label>
              <input type="text" name="longitude" id="input-longitude" class="form-control" readonly required>
            </div>
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
<!-- URUTAN SCRIPT HARUS KONSISTEN SEPERTI KEPEMILIKAN -->
<script src="<?= base_url('skote/assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('skote/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  window.baseUrl = "<?= base_url('/') ?>";
  window.petaInputConfig = {
    geojsonUrl: "<?= base_url('geojson/kab_magelang.geojson') ?>",
    defaultLat: -7.551720,
    defaultLng: 110.228210
  };
</script>
<script src="<?= base_url('js/input/non_opd.js') ?>"></script>
<?= $this->endSection() ?>