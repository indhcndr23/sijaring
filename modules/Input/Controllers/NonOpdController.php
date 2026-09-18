<?php

namespace Modules\Input\Controllers;

use App\Controllers\BaseController;
use Modules\Input\Models\OpdModel;

class NonOpdController extends BaseController
{
    protected string $feature = 'Input Data Non-OPD';
    protected OpdModel $opdModel;

    public function __construct()
    {
        $this->opdModel = new OpdModel();
    }

    public function index()
    {
        return $this->render('NonOpd', [
            'activeMenu' => 'non-opd'
        ]);
    }

    // Ambil Data Khusus Non-OPD (kelompok_opd_id = 2)
    public function nonOpdData()
    {
        $db = \Config\Database::connect();
        $rows = $db->table('opd')
            ->where('kelompok_opd_id', 2)
            ->orderBy('id_opd', 'DESC')
            ->get()->getResultArray();

        $data = array_map(function ($row) {
            return [
                'id'               => $row['id_opd'],
                'nama'             => $row['nama_opd'],
                'jumlah_pegawai'   => $row['jumlah_pegawai'] ?? 0,
                'jumlah_perangkat' => $row['jumlah_perangkat'] ?? 0,
                'luas_ruangan'     => $row['luas_ruangan'] ?? 0,
                'rata_tamu'        => $row['rata_tamu'] ?? 0,
                'jenis_dinding'    => $row['jenis_dinding'] ?? '-',
                'jumlah_lantai'    => $row['jumlah_lantai'] ?? 0,
                'jumlah_ruangan'   => $row['jumlah_ruangan'] ?? 0,
                'alamat'           => $row['alamat'] ?? '-',
                'nama_pic'         => $row['nama_pic'] ?? '-',
                'no_hp_pic'        => $row['no_hp_pic'] ?? '-',
                'latitude'         => $row['latitude'] ?? '',
                'longitude'        => $row['longitude'] ?? '',
                'jenis_kabel'      => $row['jenis_kabel'] ?? '-',
            ];
        }, $rows);

        return $this->response->setJSON(['data' => $data]);
    }

    // Simpan / Edit Non-OPD
    public function nonOpdStore()
    {
        $id_opd           = $this->request->getPost('id_opd');
        $nama_opd         = trim($this->request->getPost('nama_opd') ?? '');
        $jumlah_pegawai   = $this->request->getPost('jumlah_pegawai');
        $jumlah_perangkat = $this->request->getPost('jumlah_perangkat');
        $luas_ruangan     = $this->request->getPost('luas_ruangan');
        $rata_tamu        = $this->request->getPost('rata_tamu');
        $jenis_dinding    = $this->request->getPost('jenis_dinding');
        $jumlah_lantai    = $this->request->getPost('jumlah_lantai');
        $jumlah_ruangan   = $this->request->getPost('jumlah_ruangan');
        $alamat           = trim($this->request->getPost('alamat') ?? '');
        $nama_pic         = trim($this->request->getPost('nama_pic') ?? '');
        $no_hp_pic        = trim($this->request->getPost('no_hp_pic') ?? '');
        $latitude         = $this->request->getPost('latitude');
        $longitude        = $this->request->getPost('longitude');
        $jenis_kabel      = $this->request->getPost('jenis_kabel');

        if (
            empty($nama_opd) ||
            $jumlah_pegawai === null || $jumlah_pegawai === '' ||
            $jumlah_perangkat === null || $jumlah_perangkat === '' ||
            empty($luas_ruangan) ||
            $rata_tamu === null || $rata_tamu === '' ||
            empty($jenis_dinding) ||
            empty($jumlah_lantai) ||
            empty($jumlah_ruangan) ||
            empty($alamat) ||
            empty($nama_pic) ||
            empty($no_hp_pic) ||
            empty($latitude) ||
            empty($longitude) ||
            empty($jenis_kabel)
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Seluruh bidang formulir wajib diisi dengan lengkap!'
            ]);
        }

        $payload = [
            'nama_opd'         => $nama_opd,
            'kelompok_opd_id'   => 2, // OTOMATIS TERSIMPAN SEBAGAI NON-OPD
            'jumlah_pegawai'   => $jumlah_pegawai,
            'jumlah_perangkat' => $jumlah_perangkat,
            'luas_ruangan'     => $luas_ruangan,
            'rata_tamu'        => $rata_tamu,
            'jenis_dinding'    => $jenis_dinding,
            'jumlah_lantai'    => $jumlah_lantai,
            'jumlah_ruangan'   => $jumlah_ruangan,
            'jenis_kabel'      => $jenis_kabel,
            'alamat'           => $alamat,
            'nama_pic'         => $nama_pic,
            'no_hp_pic'        => $no_hp_pic,
            'latitude'         => $latitude,
            'longitude'        => $longitude,
        ];

        try {
            if (!empty($id_opd)) {
                $this->opdModel->update($id_opd, $payload);
                $savedId = $id_opd; // Ambil ID yang di-update
            } else {
                $savedId = $this->opdModel->insert($payload); // Ambil ID baru yang di-insert
            }

            // === TAMBAHKAN KODE INI DI SINI ===
            // Sync otomatis jenis kabel Non-OPD ke tabel jaringan
            if (!empty($jenis_kabel)) {
                $jaringanModel = new \Modules\Input\Models\JaringanModel();
                $jaringanModel->syncFromOpd((int)$savedId, $jenis_kabel);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data lokasi Non-OPD berhasil disimpan']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function nonOpdHapus($id)
    {
        try {
            $this->opdModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data Non-OPD berhasil dihapus']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
