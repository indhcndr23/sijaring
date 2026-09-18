<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<style>
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

    .org-chart-wrap {
        position: relative;
        width: 100%;
        padding: 40px 0 20px;
        overflow-x: hidden;
    }

    .org-lines {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }

    .org-root-row {
        display: flex;
        justify-content: center;
        margin-bottom: 50px;
        position: relative;
        z-index: 1;
    }

    .org-tiers-container {
        display: flex;
        flex-direction: column;
        gap: 45px;
        position: relative;
        z-index: 1;
    }

    .org-tier-row {
        display: flex;
        flex-wrap: wrap !important;
        justify-content: center;
        gap: 35px 20px;
        padding: 0 10px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .org-child {
        position: relative;
    }

    .org-node {
        display: inline-block;
        min-width: 140px;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #222;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
    }

    .org-node .btn-detail {
        display: block;
        margin: 8px auto 0;
        font-size: 11px;
        padding: 2px 12px;
        border-radius: 20px;
        border: none;
        background: rgba(255, 255, 255, .65);
        cursor: pointer;
    }

    .org-node.type-root { background: #b39ddb; }
    .org-node.type-hijau { background: #5ce67c; color: #133c1f; }
    .org-node.type-kuning { background: #fff37a; }
    .org-node.type-merah { background: #f16b6b; color: #fff; }
    .org-node.type-abu { background: #e0e0e0; color: #333; }

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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="card card-dashboard p-3">

            <h6 class="card-title-dashboard mb-3">Struktur Organisasi</h6>

            <div class="mb-2">
                <span class="legend-box"><span class="legend-dot" style="background:#5ce67c"></span> Hijau: Index 80-100</span>
                <span class="legend-box"><span class="legend-dot" style="background:#fff37a"></span> Kuning: Index 50-79</span>
                <span class="legend-box"><span class="legend-dot" style="background:#f16b6b"></span> Merah: Index &lt;50</span>
                <span class="legend-box"><span class="legend-dot" style="background:#e0e0e0"></span> Belum dinilai</span>
                <span class="legend-box"><span class="legend-line"></span>FO</span>
                <span class="legend-box"><span class="legend-line dashed"></span>Broadband</span>
            </div>

            <div class="org-chart-wrap">
                <svg id="org-lines" class="org-lines"></svg>

                <div class="org-root-row">
                    <div class="org-node type-<?= esc($data['orgTree']['type']) ?>">
                        <?= esc($data['orgTree']['name']) ?>
                        <button type="button" class="btn-detail"
                            data-id="<?= esc($data['orgTree']['id']) ?>"
                            data-name="<?= esc($data['orgTree']['name']) ?>">Detail</button>
                    </div>
                </div>

                <div class="org-tiers-container" id="org-children">
                    <?php 
                    $orderTiers = ['hijau', 'kuning', 'merah', 'abu'];
                    foreach ($orderTiers as $tierKey): 
                        if (empty($data['orgTree']['tiers'][$tierKey])) continue;
                    ?>
                        <div class="org-tier-row">
                            <?php foreach ($data['orgTree']['tiers'][$tierKey] as $child): ?>
                                <?php
                                // Default ke FO jika jenis kabel belum diisi agar garis pohon tetap tersambung
                                $kabelSlug = !empty($child['jenis_kabel']) ? strtolower($child['jenis_kabel']) : 'fo';
                                ?>
                                <div class="org-child" data-kabel="<?= esc($kabelSlug) ?>">
                                    <div class="org-node type-<?= esc($child['type']) ?>">
                                        <?= esc($child['name']) ?>
                                        <button type="button" class="btn-detail"
                                            data-id="<?= esc($child['id']) ?>"
                                            data-name="<?= esc($child['name']) ?>">Detail</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailOrg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailOrgLabel">Detail Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailOrgBody">
                Memuat data...
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    window.baseUrl = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/dashboard/grafik.js') ?>"></script>
<script src="<?= base_url('js/dashboard/orgchart.js') ?>"></script>
<?= $this->endSection() ?>