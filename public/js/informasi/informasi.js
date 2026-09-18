const BASE_INFORMASI_URL = window.location.origin + '/informasi';

/**
 * Fetch semua data informasi dari endpoint JSON
 */
async function fetchInformasiData() {
  try {
    const response = await fetch(`${BASE_INFORMASI_URL}/data`);
    if (!response.ok) throw new Error('Gagal memuat data informasi');
    return await response.json();
  } catch (error) {
    console.error('[Informasi]', error);
    return null;
  }
}

/**
 * Render FAQ cards menggunakan template cloning
 */
function renderFaq(items) {
  const container = document.getElementById('faq-container');
  const template = document.getElementById('faq-template');

  // Bersihkan container (kecuali template)
  container.querySelectorAll('.col-lg-6').forEach(el => el.remove());

  items.forEach(item => {
    const clone = template.content.cloneNode(true);

    clone.querySelector('.faq-icon').innerHTML = item.icon;
    clone.querySelector('.faq-judul').textContent = item.judul;
    clone.querySelector('.faq-deskripsi').textContent = item.deskripsi;

    container.appendChild(clone);
  });
}

/**
 * Render data kontak admin
 */
function renderKontak(kontak) {
  const detail = document.getElementById('kontak-detail');

  detail.querySelector('.kontak-nama').textContent = kontak.nama;
  detail.querySelector('.kontak-telepon').textContent = kontak.telepon;
  detail.querySelector('.kontak-email-text').textContent = kontak.email;

  const emailLink = detail.querySelector('.kontak-email-link');
  emailLink.href = `mailto:${kontak.email}`;
}

/**
 * Render SOP list menggunakan template cloning
 */
function renderSop(items) {
  const container = document.getElementById('sop-container');
  const template = document.getElementById('sop-template');

  // Bersihkan container (kecuali template)
  container.querySelectorAll('.col-lg-6').forEach(el => el.remove());

  items.forEach(item => {
    const clone = template.content.cloneNode(true);

    clone.querySelector('.sop-nama').textContent = `${item.nomor}. ${item.nama}`;

    const btn = clone.querySelector('.sop-btn');
    btn.addEventListener('click', () => handleOpenSop(item.slug, item.nama));

    container.appendChild(clone);
  });
}

/**
 * Handle buka SOP — cek ketersediaan file, lalu preview di tab baru
 */
async function handleOpenSop(slug, nama) {
  try {
    const response = await fetch(`${BASE_INFORMASI_URL}/sop/${slug}`, { method: "HEAD" });

    if (response.ok) {
      // File tersedia, buka preview di tab baru
      window.open(`${BASE_INFORMASI_URL}/sop/${slug}`, '_blank');
    } else {
      Swal.fire({
        icon: 'info',
        title: 'Dokumen Belum Tersedia',
        text: `Dokumen SOP "${nama}" belum tersedia. Silakan hubungi admin.`,
        confirmButtonColor: '#288052'
      });
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Terjadi Kesalahan',
      text: 'Gagal mengakses dokumen SOP. Silakan coba lagi.',
      confirmButtonColor: '#288052'
    });
  }
}

/**
 * Initialize — load semua data
 */
(async function init() {
  const data = await fetchInformasiData();
  if (!data) return;

  renderFaq(data.faq);
  renderKontak(data.kontak);
  renderSop(data.sop);
})();