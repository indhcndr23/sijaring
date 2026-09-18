<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.3/css/fixedColumns.dataTables.min.css">
<link href="<?= base_url('skote/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('css/datatable.css') ?>" rel="stylesheet" type="text/css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <section id="kalender-kegiatan" class="px-4 category-menu">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-2">Pengajuan Hibah, Bansos dan BKK</h5>
                        <p class="text-muted mb-3">Kolom informasi pada menu "Jadwal Penting Prosedur Hibah"</p>
                    </div>
                </div>
                <div class="d-flex flex-column flex-xl-row justify-content-md-between gap-2 gap-md-3">
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <select name="calender-filter" id="calender-filter" class="form-select w-auto">
                            <option value="year-calender">Kalender Tahunan</option>
                            <option value="month-calender">Kalender Bulanan</option>
                        </select>
                        <select name="year-filter" id="year-filter" class="form-select w-auto">
                            <?php for ($i = date('Y'); $i >= date('Y') - 10; $i--) : ?>
                                <option value="<?= $i ?>" <?= $i == $data['year'] ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="month-filter" id="month-filter" class="form-select w-auto d-none">
                            <?php foreach ($data['months'] as $index => $b) : ?>
                                <option value="<?= $index + 1 ?>" <?= $index + 1 == $data['month'] ? 'selected' : '' ?>><?= $b ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="penanggung-jawab-filter" id="penanggung-jawab-filter" class="form-select w-auto">
                            <option value="">Penanggung Jawab</option>
                            <?php foreach ($data['penanggungJawab'] as $index => $pj) : ?>
                                <option value="<?= $pj['id'] ?>"><?= $pj['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-nowrap flex-column flex-sm-row">
                        <input type="text" id="search-filter" class="form-control flex-grow-1" placeholder="&#128269; Cari Kegiatan">

                        <button
                            type="button"
                            class="btn btn-primary text-nowrap btn-tambah-kegiatan"
                            data-bs-toggle="modal"
                            data-bs-target=".modal-tambah-kegiatan">
                            Tambah Kegiatan
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="card-body mt-3" style="padding:0">
                    <input type="hidden" id="email" value="<?= $data['email'] ?>">
                    <table id="table-kalender-kegiatan" class="table nowrap cell-border table-bordered">
                        <thead class="text-center">

                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="<?= base_url('skote/assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('skote/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.datatables.net/fixedcolumns/3.3.3/js/dataTables.fixedColumns.min.js"></script>
<!-- <script src="<?= base_url('js/util.js') ?>"></script> -->
<script src="<?= base_url('js/kalender-kegiatan/datatable.js') ?>"></script>
<script src="<?= base_url('js/kalender-kegiatan/index.js') ?>"></script>
<?= $this->endSection() ?>