<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<link href="<?= base_url('skote/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('css/datatable.css') ?>" rel="stylesheet" type="text/css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18 text-uppercase fw-bold text-dark">Statistik & Monitoring Aset Jaringan</h4>
                </div>
            </div>
        </div>

        <!-- 1. Top Summary Cards -->
        <div class="row">
            <!-- Card Total OPD -->
            <div class="col-md-3">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted small fw-semibold">Total OPD</span>
                            <h3 class="fw-bold my-1 text-primary" id="lbl-total-opd">-</h3>
                            <small class="text-muted">Organisasi Perangkat Daerah</small>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                <i class="bx bxs-institution"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Total Aset -->
            <div class="col-md-3">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted small fw-semibold">Total Aset Barang</span>
                            <h3 class="fw-bold my-1 text-dark" id="lbl-total-aset">-</h3>
                            <small class="text-muted">Tercatat di Sistem</small>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                <i class="bx bx-devices"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Status Device / Perangkat Rusak -->
            <div class="col-md-3">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted small fw-semibold">Status Kondisi Aset</span>
                            <h3 class="fw-bold my-1 text-danger" id="lbl-status-device">-</h3>
                            <small class="text-muted">Monitoring Kondisi</small>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                <i class="bx bx-error-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Perlu Peremajaan -->
            <div class="col-md-3">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted small fw-semibold">Perlu Peremajaan (> 5 Thn)</span>
                            <h3 class="fw-bold my-1 text-warning" id="lbl-peremajaan">-</h3>
                            <small class="text-muted">Rekomendasi Penggantian</small>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                <i class="bx bx-history"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Section Charts -->
        <div class="row mt-2">
            <div class="col-xl-8">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-dark">
                        <i class="bx bx-bar-chart-alt-2 me-1 text-primary"></i> Distribusi Total Aset Barang paling Banyak per OPD 
                    </h6>
                    <div style="height: 260px;">
                        <canvas id="chartOpd"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-dark">
                        <i class="bx bx-pie-chart-alt-2 me-1 text-primary"></i> Kategori Umur Perangkat
                    </h6>
                    <div style="height: 260px;">
                        <canvas id="chartUmur"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Section Tables Perangkat -->
        <div class="row mt-2">
            <div class="col-xl-6">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-dark">
                        <i class="bx me-1 text-warning"></i> Perangkat Berstatus Rusak / Perlu Penanganan
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-network">
                            <thead class="table-light">
                                <tr>
                                    <th>OPD</th>
                                    <th>Barang</th>
                                    <th>Kondisi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card card-dashboard p-3 shadow-sm border-0">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-danger">
                        <i class="bx bx-time-five me-1 text-danger"></i> Daftar Perangkat Tua (> 5 Tahun)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-perangkat-tua">
                            <thead class="table-light">
                                <tr>
                                    <th>OPD</th>
                                    <th>Barang / Merk</th>
                                    <th>Tahun</th>
                                    <th>Umur</th>
                                    <th>Kondisi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Section 4 Tabel Index Evaluasi Terburuk -->
        <div class="row mt-3">
            <!-- Fasilitas Terburuk -->
            <div class="col-xl-6 mb-3">
                <div class="card card-dashboard p-3 shadow-sm border-0 h-100">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-danger">
                        <i class="bx bx-x-circle me-1"></i> Fasilitas Terburuk (Banyak Perangkat Rusak)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-fasilitas-terburuk">
                            <thead class="table-light">
                                <tr>
                                    <th>OPD</th>
                                    <th class="text-center">Total Rusak</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kapasitas Terburuk -->
            <div class="col-xl-6 mb-3">
                <div class="card card-dashboard p-3 shadow-sm border-0 h-100">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-warning">
                        <i class="bx bx-doughnut-chart me-1"></i> Kapasitas Terburuk (Rasio Port/Pegawai Rendah)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-kapasitas-terburuk">
                            <thead class="table-light">
                                <tr>
                                    <th>OPD</th>
                                    <th class="text-center">Pegawai</th>
                                    <th class="text-center">Port/AP</th>
                                    <th class="text-center">Rasio</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kinerja Terburuk -->
            <div class="col-xl-6 mb-3">
                <div class="card card-dashboard p-3 shadow-sm border-0 h-100">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-danger">
                        <i class="bx bx-trending-down me-1"></i> Kinerja Terburuk (Dominasi Perangkat Tua)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-kinerja-terburuk">
                            <thead class="table-light">
                                <tr>
                                    <th>OPD</th>
                                    <th class="text-center">Unit Tua (>5Th)</th>
                                    <th class="text-center">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Total Index Terburuk -->
            <div class="col-xl-6 mb-3">
                <div class="card card-dashboard p-3 shadow-sm border-0 h-100">
                    <h6 class="card-title-dashboard mb-3 fw-bold text-dark">
                        <i class="bx bx-error-alt me-1 text-danger"></i> Total Index Terburuk (Skor Komposit)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-total-index-terburuk">
                            <thead class="table-light">
                                <tr>
                                    <th>OPD</th>
                                    <th class="text-center">Rusak</th>
                                    <th class="text-center">Perangkat Tua</th>
                                    <th class="text-center">Skor Index</th>
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
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="<?= base_url('skote/assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('skote/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const baseUrl = "<?= base_url() ?>";
</script>

<script src="<?= base_url('js/statistik/statistik.js') ?>"></script>
<?= $this->endSection() ?>