<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Boxicons jika belum terload -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
    /* Card Accent & Soft Elevation */
    .form-section-card {
        border-radius: 12px;
        border: 1px solid #edf2f7;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    
    .section-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    .section-icon {
        width: 34px;
        height: 34px;
        background: #e8f5e9;
        color: #237845;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Input Styling Modern */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 8px 12px;
        font-size: 13.5px;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #237845;
        box-shadow: 0 0 0 3px rgba(35, 120, 69, 0.15);
    }
    .input-group-text {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        border-radius: 8px 0 0 8px;
        color: #64748b;
    }
    .input-group .form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    /* Radio Button Segmented / Card Style */
    .radio-card-wrap {
        display: flex;
        gap: 12px;
    }
    .radio-card-label {
        flex: 1;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s;
        margin: 0;
    }
    .radio-card-label:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .radio-card-input:checked + .radio-card-label {
        border-color: #237845;
        background-color: #f0fdf4;
        color: #166534;
        box-shadow: 0 2px 6px rgba(35, 120, 69, 0.15);
    }
    .radio-card-input {
        display: none;
    }

    /* Peta */
    #map-picker-opd {
        height: 360px;
        width: 100%;
        border-radius: 10px;
        z-index: 1;
    }

    /* Nav Tabs Kapsul */
    .nav-tabs-capsule {
        border-bottom: none;
        gap: 8px;
    }
    .nav-tabs-capsule .nav-link {
        border: none;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.2s;
    }
    .nav-tabs-capsule .nav-link.active {
        background: #237845 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(35, 120, 69, 0.25);
    }
    
    .table-custom-green thead tr {
        background-color: #237845 !important;
    }
    .table-custom-green thead th {
        background-color: #237845 !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 13px;
        padding: 10px 12px;
        border: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php 
    $listOpd = $opdList ?? $data['opdList'] ?? [];
    $listBarang = $masterBarang ?? $data['masterBarang'] ?? [];
?>

<div class="page-content">
    <div class="container-fluid">

        <!-- Title & Autosave Badge -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1 font-size-18 text-uppercase fw-bold text-dark">Pendataan Survei Jaringan</h4>
                <p class="text-muted small mb-0">Lengkapi parameter fisik kantor dan inventaris infrastruktur perangkat.</p>
            </div>
            <div id="badge-autosave" class="badge bg-success-subtle text-success p-2 px-3 d-none shadow-sm rounded-pill font-size-12">
                <i class="bx bx-check-double me-1"></i> Data Tersimpan Otomatis
            </div>
        </div>

        <!-- DROPDOWN OPD DENGAN SEARCH LOOK -->
        <div class="card p-3 shadow-sm border-0 mb-4 rounded-3" style="border-left: 5px solid #237845 !important;">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bx bx-buildings text-success font-size-18"></i> Pilih OPD Survei:
                    </label>
                </div>
                <div class="col-md-9">
                    <select id="select-opd-survei" class="form-select fw-semibold text-dark shadow-none" style="font-size: 14.5px;" required>
                        <option value="">-- Pilih Organisasi Perangkat Daerah --</option>
                        <?php foreach($listOpd as $opd): ?>
                            <option value="<?= $opd['id_opd'] ?>"><?= esc($opd['nama_opd']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- WRAPPER FORM UTAMA -->
        <div id="wrapper-survei-form" class="d-none">

            <!-- NAV TABS -->
            <ul class="nav nav-tabs nav-tabs-capsule mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info-umum" type="button">
                        <i class="bx bx-slider-alt me-1"></i> Blok I: Parameter Profil & Lokasi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info-aset" type="button">
                        <i class="bx bx-server me-1"></i> Blok II: Aset Perangkat Jaringan
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- TAB 1: PARAMETER PROFIL & LOKASI -->
                <div class="tab-pane fade show active" id="tab-info-umum">
                    
                    <div class="row g-4">
                        <!-- BAGIAN KIRI: FORM DATA TEKNIS & RUANGAN -->
                        <div class="col-xl-6 col-lg-12">
                            <!-- MINI CARD 1: IDENTITAS & PIC -->
                            <div class="form-section-card p-4 mb-4">
                                <div class="section-title-wrap">
                                    <div class="section-icon"><i class="bx bx-id-card"></i></div>
                                    <h6 class="fw-bold text-dark mb-0">Identitas Kantor & Kontak PIC</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Nama OPD</label>
                                        <input type="text" class="form-control auto-save-opd" data-field="nama_opd" id="field_nama_opd" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Alamat Lengkap</label>
                                        <input type="text" class="form-control auto-save-opd" data-field="alamat" id="field_alamat" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Nama PIC / Admin Jaringan</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                                            <input type="text" class="form-control auto-save-opd" data-field="nama_pic" id="field_nama_pic" placeholder="Nama lengkap" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">No. WhatsApp PIC</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bxl-whatsapp"></i></span>
                                            <input type="text" class="form-control auto-save-opd" data-field="no_hp_pic" id="field_no_hp_pic" placeholder="08xxxxxxxx" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MINI CARD 2: BEBAN PENGGUNA & RUANGAN -->
                            <div class="form-section-card p-4">
                                <div class="section-title-wrap">
                                    <div class="section-icon"><i class="bx bx-grid-alt"></i></div>
                                    <h6 class="fw-bold text-dark mb-0">Beban Pengguna & Ruang Bangunan</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Jumlah Pegawai (User Tetap)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-group"></i></span>
                                            <input type="number" min="0" class="form-control auto-save-opd" data-field="jumlah_pegawai" id="field_jumlah_pegawai" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Rata-rata Tamu / Hari</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-walk"></i></span>
                                            <input type="number" min="0" class="form-control auto-save-opd" data-field="rata_tamu" id="field_rata_tamu" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Luas Bangunan (m²)</label>
                                        <input type="number" min="0" class="form-control auto-save-opd" data-field="luas_ruangan" id="field_luas_ruangan" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Jumlah Ruangan</label>
                                        <input type="number" min="0" class="form-control auto-save-opd" data-field="jumlah_ruangan" id="field_jumlah_ruangan" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Jumlah Lantai</label>
                                        <input type="number" min="0" class="form-control auto-save-opd" data-field="jumlah_lantai" id="field_jumlah_lantai" required>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <label class="form-label small fw-semibold text-secondary">Jenis Dinding Dominan</label>
                                        <select class="form-select auto-save-opd" data-field="jenis_dinding" id="field_jenis_dinding" required>
                                            <option value="">-- Pilih Jenis Dinding --</option>
                                            <option value="Kaca">Kaca / Partisi Terbuka</option>
                                            <option value="Kayu">Partisi Kayu / Triplek</option>
                                            <option value="Bata">Tembok Bata</option>
                                            <option value="Beton">Beton Cor / Kedap</option>
                                        </select>
                                    </div>

                                    <!-- RADIO JALUR KONEKSI MODERN -->
                                    <div class="col-md-6 mt-3">
                                        <label class="form-label small fw-semibold text-secondary d-block">Jalur Koneksi Induk</label>
                                        <div class="radio-card-wrap">
                                            <input class="radio-card-input auto-save-opd-radio" type="radio" name="jenis_kabel" id="kabelFO" value="FO">
                                            <label class="radio-card-label" for="kabelFO">
                                                <i class="bx bx-pulse font-size-16 text-success"></i> Fiber Optic
                                            </label>

                                            <input class="radio-card-input auto-save-opd-radio" type="radio" name="jenis_kabel" id="kabelBroadband" value="Broadband">
                                            <label class="radio-card-label" for="kabelBroadband">
                                                <i class="bx bx-wifi font-size-16 text-primary"></i> Broadband
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BAGIAN KANAN: PETA INTERAKTIF & KOORDINAT -->
                        <div class="col-xl-6 col-lg-12">
                            <div class="form-section-card p-4 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="section-title-wrap">
                                        <div class="section-icon"><i class="bx bx-map-pin"></i></div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Titik Koordinat Geospasial</h6>
                                            <small class="text-muted">Posisikan pin tepat di titik lokasi gedung kantor OPD</small>
                                        </div>
                                    </div>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                                        <input type="text" id="input-cari-map" class="form-control" placeholder="Cari nama lokasi / jalan di Magelang...">
                                        <button class="btn btn-success px-3" type="button" id="btn-cari-map">Cari</button>
                                    </div>

                                    <div id="map-picker-opd" class="border shadow-sm mb-3"></div>
                                </div>

                                <div class="row g-2 pt-2 border-top">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-secondary mb-1">Latitude</label>
                                        <input type="text" class="form-control auto-save-opd bg-light text-monospace font-size-12 fw-bold text-success" data-field="latitude" id="field_latitude" readonly required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-secondary mb-1">Longitude</label>
                                        <input type="text" class="form-control auto-save-opd bg-light text-monospace font-size-12 fw-bold text-success" data-field="longitude" id="field_longitude" readonly required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- TAB 2: INVENTARIS ASET PERANGKAT -->
                <div class="tab-pane fade" id="tab-info-aset">
                    <div id="container-kategori-perangkat"></div>
                </div>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.masterBarangList = <?= json_encode($listBarang) ?>;
</script>
<script src="<?= base_url('js/input/data_awal.js') ?>"></script>
<?= $this->endSection() ?>