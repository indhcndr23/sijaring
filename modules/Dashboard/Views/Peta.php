<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    #peta-map {
        width: 100%;
        height: 500px;
        border-radius: 10px;
        background: #ffffff;
        transition: height 0.2s ease;
    }

    .legend-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        margin-right: 18px;
        margin-bottom: 8px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .legend-line {
        width: 26px;
        height: 0;
        border-top: 2px solid #a3a3a3;
        display: inline-block;
    }

    .legend-line.dashed {
        border-top-style: dashed;
    }

    /* CSS Tabel Kustom Modal Detail (Header Hijau Rounded) */
    .table-custom {
        border-collapse: separate !important;
        border-spacing: 0;
        width: 100% !important;
        border: none !important;
    }

    .table-custom thead tr {
        background-color: #2e7d4e !important;
        color: #ffffff !important;
    }

    .table-custom thead th {
        background-color: #2e7d4e !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 13px;
        padding: 10px 12px;
        border: none !important;
        vertical-align: middle;
    }

    .table-custom thead tr th:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }

    .table-custom thead tr th:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    .table-custom tbody td {
        padding: 10px 12px;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #f0f0f0 !important;
        border-top: none !important;
        vertical-align: middle;
    }

    .table-custom tbody td:not(:last-child) {
        border-right: 1px solid #f2f2f2;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="card card-dashboard p-3">

            <!-- ALERT KONDISI JARINGAN DIHAPUS-->

            <h6 class="card-title-dashboard mb-3">Peta Sebaran OPD — Kabupaten Magelang</h6>

            <!-- LEGEND LENGKAP KEMBALI -->
            <div class="mb-3">
                <span class="legend-box"><span class="legend-dot" style="background:#5ce67c"></span> Hijau (Index 80-100)</span>
                <span class="legend-box"><span class="legend-dot" style="background:#fff37a"></span> Kuning (Index 50-79)</span>
                <span class="legend-box"><span class="legend-dot" style="background:#f16b6b"></span> Merah (Index &lt;50)</span>
                <span class="legend-box"><span class="legend-dot" style="background:#9e9e9e"></span> Belum dinilai</span>
                <span class="legend-box"><span class="legend-line"></span>FO</span>
                <span class="legend-box"><span class="legend-line dashed"></span>Broadband</span>
            </div>

            <div id="peta-map"></div>
        </div>
    </div>
</div>

<!-- Modal Detail Peta (Ukuran Standar Sesuai Grafik & Tabel) -->
<div class="modal fade" id="modalDetailPeta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailPetaLabel">Detail Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailPetaBody">Memuat data...</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    window.baseUrl = "<?= base_url() ?>";

    window.petaData = {
        opd: <?= json_encode($data['opdList']) ?>,
        jaringan: <?= json_encode($data['jaringanList']) ?>,
        geojsonUrl: "<?= base_url('geojson/kab_magelang.geojson') ?>",
        kecamatanFolderUrl: "<?= base_url('geojson/peta_kecamatan/') ?>"
    };
</script>
<script src="<?= base_url('js/dashboard/peta.js') ?>"></script>
<?= $this->endSection() ?>