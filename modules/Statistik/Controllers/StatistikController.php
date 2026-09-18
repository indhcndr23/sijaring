<?php

namespace Modules\Statistik\Controllers;

use Modules\Statistik\Models\StatistikModel;

class StatistikController extends BaseController
{
    protected string $feature = 'Statistik Jaringan';
    protected StatistikModel $statistikModel;

    public function __construct()
    {
        $this->statistikModel = new StatistikModel();
    }

    public function index()
    {
        return $this->render('index', [
            'activeMenu' => 'statistik',
        ]);
    }

    public function getDataStatistik()
    {
        return $this->response->setJSON([
            'status'               => 'success',
            'summary'              => $this->statistikModel->getSummary(),
            'barang_opd'           => $this->statistikModel->getBarangPerOpd(),
            'umur_stat'            => $this->statistikModel->getStatistikUmur(),
            'sebaran_merk'         => $this->statistikModel->getSebaranMerk(),
            'perangkat_tua'        => $this->statistikModel->getPerangkatTua(),
            'perangkat_rusak'      => $this->statistikModel->getPerangkatRusak(),
            'fasilitas_terburuk'   => $this->statistikModel->getFasilitasTerburuk(),
            'kapasitas_terburuk'   => $this->statistikModel->getKapasitasTerburuk(),
            'kinerja_terburuk'     => $this->statistikModel->getKinerjaTerburuk(),
            'total_index_terburuk' => $this->statistikModel->getTotalIndexTerburuk()
        ]);
    }
}