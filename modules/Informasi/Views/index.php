<?= $this->extend('layout/base') ?>

<?= $this->section('css') ?>
<style>
  .sop-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #eee;
  }

  .sop-item:last-child {
    border-bottom: none;
  }

  .sop-nama {
    font-weight: 600;
    font-size: 15px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content px-0 text-black" style="background-color: #FFFFFF;">
  <section id="penggunaan-aplikasi" class="p-4">
    <div class="container-fluid">
      <h5 class="mb-2">Terkait Penggunaan Aplikasi</h5>
      <p class="text-muted">Seputar pertanyaan umum terkait cara penggunaan aplikasi Sijaring</p>
      <div class="row g-4 g-lg-4" id="faq-container">
        <template id="faq-template">
          <div class="col-lg-6">
            <div class="card card-soft h-100">
              <div class="card-header bg-transparent border-primary pe-none">
                <button class="btn btn-soft-success btn-icon" style="color: #288052">
                  <span class="faq-icon"></span>
                </button>
              </div>
              <div class="card-body">
                <h5 class="card-title faq-judul"></h5>
                <p class="card-text faq-deskripsi"></p>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </section>

  <section id="hubungi-admin" class="p-4">
    <div class="container-fluid">
      <div class="p-4" style="background-color: #F4FBF7;">
        <div class="row g-4 g-lg-4">
          <div class="col-lg-6">
            <h2 class="fw-bold">Hubungi Admin</h2>
            <div>
              Jika masih terdapat pertanyaan yang belum terjawab, kendala dalam penggunaan
              aplikasi, ataupun laporan terkait bug serta usulan penambahan fitur untuk meningkatkan
              kenyamanan dan efektivitas penggunaan, silakan menghubungi admin melalui kontak
              yang telah disediakan di bawah ini agar dapat ditindaklanjuti dengan cepat dan tepat.
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row" id="kontak-detail">
              <div class="col-auto">
                <h4 class="m-0">
                  <i class="bx bxs-user" style="color: #288052"></i>
                </h4>
              </div>
              <div class="col">
                <div class="fw-bold mb-2 kontak-nama"></div>
                <div class="mb-2 kontak-telepon"></div>
                <div class="mb-2 kontak-email-text"></div>
                <div class="d-flex">
                  <a href="#" class="btn text-white d-flex align-items-center gap-2 kontak-email-link"
                    style="background-color: #288052;">
                    <i class="bx bx-envelope"></i>
                    Kirimkan Email
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="ketentuan-sop" class="p-4">
    <div class="container-fluid">
      <h5 class="mb-2">Ketentuan SOP Setiap Kegiatan</h5>
      <p class="text-muted">Berikut adalah SOP yang wajib dipatuhi untuk setiap jenis kegiatan permohonan</p>

      <div class="row g-4" id="sop-container">
        <template id="sop-template">
          <div class="col-lg-6">
            <div class="sop-item">
              <span class="sop-nama"></span>
              <button class="btn text-white d-flex align-items-center gap-2 sop-btn" style="background-color: #288052;">
                <i class="bx bx-file"></i>
                Buka SOP
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="<?= base_url('js/informasi/informasi.js') ?>"></script>
<?= $this->endSection() ?>