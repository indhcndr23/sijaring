<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Kabupaten Magelang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('skote/assets/images/favicon.ico') ?>">

    <link href="<?= base_url('skote/assets/css/bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('skote/assets/libs/toastr/build/toastr.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('skote/assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('skote/assets/css/app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('skote/assets/css/custom.css') ?>" rel="stylesheet" type="text/css" />

    <script>
        window.BASE_URL = "<?= base_url('skote') ?>/";
    </script>
    <script src="<?= base_url('skote/assets/js/plugin.js') ?>"></script>
    <style>
        .otp-input {
            width: 50px;
            height: 50px;
            font-size: 22px;
            font-weight: 600;
        }

        .otp-input:focus {
            border-color: #556ee6;
            box-shadow: 0 0 0 .15rem rgba(85, 110, 230, .25);
        }
    </style>
    <?= $this->renderSection('css') ?>
</head>

<body data-sidebar="light">
    <div id="layout-wrapper">

        <?= $this->include('layout/header'); ?>
        <?= $this->include('layout/sidebar'); ?>

        <div class="main-content">
            <div style="margin: 70px 0 0; padding: 20px;" class="position-relative overflow-hidden card-body-brown" >
                <img src="<?= base_url('skote/assets/images/motif-header.svg') ?>" class="header-bg" alt="">

                <div>
                    <h4 style="color: #288052; font-size: 20px; font-weight: 600; margin: 0;letter-spacing: 0.1rem;"><?= $applicationName ?></h4>
                    <h2 style="color: #000000;font-size: 40px;font-weight: 600;margin:0;text-wrap: wrap;width: 400px;"><?= $featureName ?></h2>
                </div>

            </div>
            <?= $this->renderSection('content') ?>
        </div>

    </div>

    <script src="<?= base_url('skote/assets/libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/metismenu/metisMenu.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/simplebar/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/node-waves/waves.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/apexcharts/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('skote/assets/libs/toastr/toastr.js') ?>"></script>

    <script src="<?= base_url('skote/assets/js/pages/dashboard.init.js') ?>"></script>
    <script src="<?= base_url('skote/assets/js/app.js') ?>"></script>

    <?= $this->include('components/toast') ?>

    <?= $this->renderSection('script') ?>
</body>

</html>
