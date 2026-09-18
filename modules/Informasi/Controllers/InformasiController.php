<?php

namespace Modules\Informasi\Controllers;

use Modules\Informasi\Controllers\BaseController as Controller;

class InformasiController extends Controller
{
    protected string $feature = 'Informasi Umum Tentang Aplikasi';

    public function index()
    {
        $data = [
            'user' => $this->auth->user()
        ];

        return $this->render("index", $data);
    }

    /**
     * Endpoint JSON yang menyediakan seluruh data informasi.
     */
    public function getInformasiData()
    {
        return $this->response->setJSON([
            'faq'    => $this->getFaqData(),
            'kontak' => $this->getKontakData(),
            'sop'    => $this->getSopData(),
        ]);
    }

    /**
     * Buka file SOP berdasarkan slug.
     * File dicari di public/documents/sop/sop-{slug}.pdf
     */
    public function openSop(string $slug)
    {
        $filename = "sop-{$slug}.pdf";
        $filepath = FCPATH . "documents/sop/{$filename}";

        if (!file_exists($filepath)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Dokumen SOP belum tersedia. Silakan hubungi admin.'
            ])->setStatusCode(404);
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "inline; filename=\"{$filename}\"")
            ->setBody(file_get_contents($filepath));
    }

    /**
     * Data FAQ — Terkait Penggunaan Aplikasi
     * @return array
     */
    private function getFaqData(): array
    {
        return [
            [
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/></svg>',
                'judul'     => 'Apakah itu aplikasi SIJARING?',
                'deskripsi' => 'Aplikasi SINTIA adalah aplikasi yang digunakan untuk mempermudah proses pengajuan bantuan yang ditujukan ke Dinas Komunikasi dan Informatika. Selain dapat melakukan tracking real time progress permohonan, juga dapat mengurangi langkah administrasi permohonan tersebut.',
            ],
            [
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/></svg>',
                'judul'     => 'Bagaimana cara menggunakan aplikasi SIJARING?',
                'deskripsi' => 'Cara menggunakan aplikasi ini sangat mudah, yaitu dengan login sesuai dengan SKPD masing masing lewat magelang kab, lalu pengguna memilih fitur yang tersedia pada halaman utama untuk melakukan permohonan. Selain itu tabel permohonan juga dapat digunakan untuk melakukan tracking permohonan.',
            ],
            [
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stack" viewBox="0 0 16 16"><path d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z"/><path d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z"/></svg>',
                'judul'     => 'Apa saja yang dapat diakses di aplikasi ini?',
                'deskripsi' => 'Yang dapat diakses pada aplikasi ini yaitu fitur dashboard untuk meminta permohonan yang relevan kepada Dinas Komunikasi dan Informatika dan juga melacak progress permohonan tersebut. Selain itu juga ada fitur kalender untuk melihat progress pengerjaan setiap kegiatan.',
            ],
            [
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg>',
                'judul'     => 'Siapa saja yang dapat mengakses aplikasi ini?',
                'deskripsi' => 'Yang dapat mengakses aplikasi ini adalah pihak semua pihak SKPD Pemkab Magelang, namun beberapa fitur hanya dapat diakses oleh SKPD Dinas Komunikasi dan Informatika Magelang, namun untuk permohonan kegiatan dapat dilakukan oleh semua SKPD.',
            ],
        ];
    }

    /**
     * Data Kontak Admin
     * @return array
     */
    private function getKontakData(): array
    {
        return [
            'nama'    => 'Administrasi Diskominfo',
            'telepon' => 'xxxxxxxxxxxxx',
            'email'   => 'xxxxxxxxxxxxx@magelangkab.go.id',
        ];
    }

    /**
     * Data SOP — Ketentuan SOP Setiap Kegiatan
     * 'slug' harus sesuai dengan nama file di public/documents/sop/sop-{slug}.pdf
     * @return array
     */
    private function getSopData(): array
    {
        return [
            ['nomor' => 1,  'nama' => 'Permohonan Aplikasi',          'slug' => '1-permohonan-aplikasi'],
            ['nomor' => 2,  'nama' => 'Insiden Siber',                 'slug' => '2-insiden-siber'],
            ['nomor' => 3,  'nama' => 'Permohonan Pentest / VA',       'slug' => '3-permohonan-pentest-va'],
            ['nomor' => 4,  'nama' => 'Perawatan Jaringan',            'slug' => '4-perawatan-jaringan'],
            ['nomor' => 5,  'nama' => 'Fasilitasi Rapat',              'slug' => '5-fasilitasi-rapat'],
            ['nomor' => 6,  'nama' => 'Permohonan Operator',           'slug' => '6-permohonan-operator'],
            ['nomor' => 7,  'nama' => 'Permohonan Zoom',               'slug' => '7-permohonan-zoom'],
            ['nomor' => 8,  'nama' => 'Permohonan Narasumber / Juri',  'slug' => '8-permohonan-narasumber-juri'],
            ['nomor' => 9,  'nama' => 'Permohonan Data',               'slug' => '9-permohonan-data'],
            ['nomor' => 10, 'nama' => 'Permohonan Liputan',            'slug' => '10-permohonan-liputan'],
            ['nomor' => 11, 'nama' => 'Permohonan Desain Media',       'slug' => '11-permohonan-desain-media'],
            ['nomor' => 12, 'nama' => 'Permohonan MC',                 'slug' => '12-permohonan-mc'],
            ['nomor' => 13, 'nama' => 'Tugas Internal Lainya',         'slug' => '13-tugas-internal-lainya'],
        ];
    }
}
