<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        email: <?= $user['email'] ?>
    </div>
</div>
<?= $this->endSection() ?>
