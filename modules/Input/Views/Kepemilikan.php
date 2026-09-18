<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<link href="<?= base_url('skote/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('css/datatable.css') ?>" rel="stylesheet" type="text/css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- Filter Card OPD -->
        <div class="card p-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <label class="fw-bold mb-0" style="white-space: nowrap;">Pilih OPD:</label>
                <select id="filter-opd" class="form-select form-select-sm" style="max-width: 320px;">
                    <option value="">-- Silakan Pilih OPD Dahulu --</option>
                    <?php
                    $db = \Config\Database::connect();
                    $listOpd = $db->table('opd')->orderBy('nama_opd', 'ASC')->get()->getResultArray();
                    ?>
                    <?php if (!empty($listOpd)): ?>
                        <?php foreach ($listOpd as $opd): ?>
                            <option value="<?= $opd['id_opd'] ?>"><?= esc($opd['nama_opd']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="card card-dashboard p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="card-title-dashboard m-0">Data Kepemilikan Barang OPD</h6>
                <button type="button" id="btn-tambah" class="btn btn-primary btn-sm" disabled>
                    <i class="bx bx-plus"></i> Tambah
                </button>
            </div>
            <div class="table-responsive">
                <table id="table-kepemilikan" class="table table-bordered table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama OPD</th>
                            <th>Jenis Perangkat</th>
                            <th>Merk</th>
                            <th>Tahun</th>
                            <th>Kondisi</th>
                            <th>Keterangan</th>
                            <th>Foto Bukti</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalTambahKepemilikan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-tambah-kepemilikan" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Input Kepemilikan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="id_opd" id="modal-id-opd" value="">

                    <?php
                    $listBarang = $db->table('barang')->orderBy('jenis_barang', 'ASC')->get()->getResultArray();
                    ?>

                    <div class="mb-3">
                        <label class="form-label">OPD Terpilih</label>
                        <select id="modal-select-opd-display" class="form-select" disabled>
                            <option value="">-- OPD Belum Dipilih --</option>
                            <?php if (!empty($listOpd)): ?>
                                <?php foreach ($listOpd as $opd): ?>
                                    <option value="<?= $opd['id_opd'] ?>"><?= esc($opd['nama_opd']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Perangkat (Katalog Master) <span class="text-danger">*</span></label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Jenis Perangkat --</option>
                            <?php if (!empty($listBarang)): ?>
                                <?php foreach ($listBarang as $b): ?>
                                    <option value="<?= $b['id_barang'] ?>">
                                        <?= esc($b['jenis_barang']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Model / Merk Perangkat <span class="text-danger">*</span></label>
                            <div id="container-input-merk">
                                <input type="text" name="merk" id="input_merk_text" class="form-control" placeholder="Pilih Jenis Perangkat dahulu..." required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Pengadaan <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" min="1990" max="2100" placeholder="2024" required>
                        </div>
                    </div>

                    <!-- Wrapper Jumlah Unit (Selalu Muncul) -->
                    <div class="mb-3">
                        <label class="form-label">Jumlah Unit <small class="text-muted">(Kosong = 1)</small></label>
                        <input type="number" name="jumlah" class="form-control" min="1" placeholder="1">
                    </div>

                    <!-- Wrapper Khusus Port (Disembunyikan default, khusus Switch) -->
                    <div id="wrapper-port" class="mb-3 d-none">
                        <label class="form-label">Total Port <small class="text-info">(Khusus Switch)</small></label>
                        <input type="number" name="jumlah_port" class="form-control" min="0" placeholder="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select" required>
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan Catatan Tambahan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan spesifikasi / tempat pemasangan..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Bukti Fisik / Terpasang <small class="text-muted">(Maks. 3MB)</small> <span class="text-danger">*</span></label>
                        <input type="file" name="foto_bukti" id="input-foto-bukti" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted d-block mt-1">*Sistem akan mengompres foto secara otomatis.</small>
                        <div id="preview-container-bukti" class="mt-2 d-none">
                            <img id="img-preview-bukti" src="" alt="Preview Foto" style="max-height: 100px; border-radius: 4px;">
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
<script src="<?= base_url('skote/assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('skote/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('js/input/kepemilikan.js') ?>"></script>
<?= $this->endSection() ?>