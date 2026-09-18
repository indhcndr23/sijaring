<?php

namespace Modules\Input\Controllers;

use App\Controllers\BaseController;
use Modules\Dashboard\Models\Opd;
use Modules\Kalkulasi\Services\IndexEngine;

class DataAwalController extends BaseController
{
    protected string $feature = 'Data Awal';
    protected $opdModel;

    public function __construct()
    {
        $this->opdModel = new Opd();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $opdList = $this->opdModel->findAll();
        $masterBarang = $db->table('barang')->get()->getResultArray();

        return $this->render('DataAwal', [
            'activeMenu'   => 'data-awal',
            'opdList'      => $opdList,
            'masterBarang' => $masterBarang,
            'data'         => [
                'opdList'      => $opdList,
                'masterBarang' => $masterBarang
            ]
        ]);
    }

    public function getDetailSurvei($idOpd)
    {
        $db = \Config\Database::connect();
        $opd = $this->opdModel->find($idOpd);

        $barang = $db->table('opd_barang ob')
            ->select('ob.*, b.jenis_barang')
            ->join('barang b', 'b.id_barang = ob.id_barang')
            ->where('ob.id_opd', $idOpd)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'opd'    => $opd,
            'barang' => $barang
        ]);
    }

    public function saveInfoOpd()
    {
        $idOpd = $this->request->getPost('id_opd');
        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');

        if (!$idOpd || !$field) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak valid']);
        }

        $this->opdModel->update($idOpd, [$field => $value]);

        if ($field === 'jenis_kabel') {
            $jaringanModel = new \Modules\Input\Models\JaringanModel();
            $jaringanModel->syncFromOpd((int)$idOpd, $value);
        }

        register_shutdown_function(fn() => IndexEngine::recalculate((int)$idOpd));

        return $this->response->setJSON(['status' => 'success', 'message' => 'Tersimpan otomatis']);
    }

    public function saveBarang()
    {
        $db = \Config\Database::connect();

        $idOpdBarang = $this->request->getPost('id_opd_barang');
        $idOpd       = $this->request->getPost('id_opd');
        $idBarang    = $this->request->getPost('id_barang');
        $jumlah      = $this->request->getPost('jumlah') ?: 1;
        $jumlahPort  = $this->request->getPost('jumlah_port');
        $merk        = $this->request->getPost('merk');
        $tahun       = $this->request->getPost('tahun');
        $kondisi     = $this->request->getPost('kondisi');
        $keterangan  = $this->request->getPost('keterangan');

        $data = [
            'id_opd'      => $idOpd,
            'id_barang'   => $idBarang,
            'jumlah'      => (int)$jumlah,
            'jumlah_port' => ($jumlahPort !== null && $jumlahPort !== '') ? (int)$jumlahPort : null,
            'merk'        => $merk,
            'tahun'       => $tahun,
            'kondisi'     => $kondisi ?: 'Baik',
            'keterangan'  => $keterangan,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        if (!empty($idOpdBarang)) {
            $db->table('opd_barang')->where('id_opd_barang', $idOpdBarang)->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('opd_barang')->insert($data);
            $idOpdBarang = $db->insertID();
        }

        register_shutdown_function(fn() => IndexEngine::recalculate((int)$idOpd));
        return $this->response->setJSON([
            'status'        => 'success',
            'id_opd_barang' => $idOpdBarang
        ]);
    }

    public function deleteBarang($idOpdBarang)
    {
        $db = \Config\Database::connect();

        $item = $db->table('opd_barang')->where('id_opd_barang', $idOpdBarang)->get()->getRowArray();
        if ($item) {
            $idOpdnya = (int)$item['id_opd'];
            $db->table('opd_barang')->where('id_opd_barang', $idOpdBarang)->delete();
            register_shutdown_function(fn() => IndexEngine::recalculate($idOpdnya));
        }

        return $this->response->setJSON(['status' => 'success']);
    }
}