<?php

namespace Modules\Dashboard\Controllers;

use Modules\Dashboard\Controllers\BaseController as Controller;
use Modules\Dashboard\Models\Opd;
use Modules\Input\Models\JaringanModel;
use Modules\Dashboard\Models\IndexPenilaian;
use Modules\Dashboard\Models\OpdBarang;

class DashboardController extends Controller
{
    protected string $feature = 'Dashboard';

    protected const HUB_OPD_ID = 13;

    protected Opd $opdModel;
    protected JaringanModel $jaringanModel;
    protected IndexPenilaian $indexModel;
    protected OpdBarang $opdBarangModel;

    public function __construct()
    {
        $this->opdModel       = new Opd();
        $this->jaringanModel  = new JaringanModel();
        $this->indexModel     = new IndexPenilaian();
        $this->opdBarangModel = new OpdBarang();
    }

    private function getTren(int $idOpd, ?int $nilaiSekarang): array
    {
        if ($nilaiSekarang === null) {
            return ['tren' => 'baru', 'selisih' => 0];
        }

        $db = \Config\Database::connect();

        $histori = $db->table('histori_index')
            ->where('id_opd', $idOpd)
            ->orderBy('created_at', 'DESC')
            ->limit(1, 1)
            ->get()
            ->getRowArray();

        if (!$histori) {
            return ['tren' => 'baru', 'selisih' => 0];
        }

        $selisih = $nilaiSekarang - (int) $histori['nilai_index'];

        if ($selisih > 0)      return ['tren' => 'naik',  'selisih' => $selisih];
        if ($selisih < 0)      return ['tren' => 'turun', 'selisih' => $selisih];
        return ['tren' => 'sama', 'selisih' => 0];
    }

    // SEMUA OPD DITAMPILKAN & DIURUTKAN NILAINYA MENURUN (KIRI KE KANAN)
    private function buildTieredChildrenFromIndex(int $hubId): array
    {
        $db = \Config\Database::connect();

        // Ambil seluruh OPD tanpa filter isian survei
        $opdAll = $db->table('opd')
            ->where('id_opd !=', $hubId)
            ->get()->getResultArray();

        $tiers = [
            'hijau'  => [],
            'kuning' => [],
            'merah'  => [],
            'abu'    => [],
        ];

        foreach ($opdAll as $opd) {
            $indexData  = $this->indexModel->getLatestByOpd($opd['id_opd']);
            $nilaiIndex = ($indexData && $indexData['nilai_index'] !== null) ? (int) $indexData['nilai_index'] : null;
            $status     = $this->indexModel->getStatusWarna($nilaiIndex);

            $tierTarget = isset($tiers[$status]) ? $status : 'abu';

            $tiers[$tierTarget][] = [
                'id'          => $opd['id_opd'],
                'name'        => $opd['nama_opd'],
                'type'        => $tierTarget,
                'jenis_kabel' => $opd['jenis_kabel'] ?? null,
                'nilai_index' => $nilaiIndex,
            ];
        }

        // Urutkan nilai dari tinggi ke rendah (Kiri = Skor Tinggi, Kanan = Menurun)
        foreach ($tiers as $key => &$list) {
            usort($list, function ($a, $b) {
                if ($a['nilai_index'] === null && $b['nilai_index'] === null) {
                    return strcmp($a['name'], $b['name']);
                }
                if ($a['nilai_index'] === null) return 1;
                if ($b['nilai_index'] === null) return -1;
                return $b['nilai_index'] <=> $a['nilai_index'];
            });
        }

        return $tiers;
    }

    private function buildJaringanListFromIndex(int $hubId): array
    {
        $hub = $this->opdModel->find($hubId);
        if (!$hub || !$hub['latitude'] || !$hub['longitude']) return [];

        $db = \Config\Database::connect();
        
        $opdAll = $db->table('opd')
            ->where('id_opd !=', $hubId)
            ->where('latitude IS NOT NULL')
            ->where('longitude IS NOT NULL')
            ->where('jenis_kabel IS NOT NULL')
            ->where('jenis_kabel !=', '')
            ->where('jenis_kabel !=', '-')
            ->get()->getResultArray();

        $jaringanList = [];
        foreach ($opdAll as $opd) {
            $indexData  = $this->indexModel->getLatestByOpd($opd['id_opd']);
            $nilaiIndex = $indexData['nilai_index'] ?? null;
            $status     = $this->indexModel->getStatusWarna($nilaiIndex);

            $jenisKabelList = array_values(array_filter(array_map('trim', explode(',', $opd['jenis_kabel']))));

            foreach ($jenisKabelList as $jk) {
                $jaringanList[] = [
                    'jenis_kabel' => $jk,
                    'status'      => $status,
                    'asal_lat'    => $hub['latitude'],
                    'asal_lng'    => $hub['longitude'],
                    'tujuan_lat'  => $opd['latitude'],
                    'tujuan_lng'  => $opd['longitude'],
                ];
            }
        }

        return $jaringanList;
    }

    public function grafik()
    {
        $hubId = self::HUB_OPD_ID;
        $hub   = $this->opdModel->find($hubId) ?: ['id_opd' => $hubId, 'nama_opd' => 'Diskominfo'];

        $hubIndexData = $this->indexModel->getLatestByOpd($hubId);
        $hubNilai     = $hubIndexData['nilai_index'] ?? null;
        $hubStatus    = $this->indexModel->getStatusWarna($hubNilai);

        $tieredChildren = $this->buildTieredChildrenFromIndex($hubId);

        $orgTree = [
            'id'          => $hub['id_opd'] ?? $hubId,
            'name'        => $hub['nama_opd'] ?? 'Diskominfo',
            'type'        => $hubStatus,
            'jenis_kabel' => null,
            'tiers'       => $tieredChildren,
        ];

        return $this->render('grafik', [
            'activeMenu' => 'grafik',
            'hub'        => $hub,
            'orgTree'    => $orgTree
        ]);
    }

    public function tabel()
    {
        $hubId = self::HUB_OPD_ID;

        $opdAll = $this->opdModel->findAll();
        $opdList = [];

        foreach ($opdAll as $opd) {
            $indexData  = $this->indexModel->getLatestByOpd($opd['id_opd']);
            $nilaiIndex = ($indexData && $indexData['nilai_index'] !== null) ? (int) $indexData['nilai_index'] : null;
            $status     = $this->indexModel->getStatusWarna($nilaiIndex);
            $tren       = $this->getTren($opd['id_opd'], $nilaiIndex);

            $opdList[] = [
                'id_opd'      => $opd['id_opd'],
                'nama_opd'    => $opd['nama_opd'],
                'jenis_kabel' => $opd['jenis_kabel'] ?? null,
                'nilai_index' => $nilaiIndex,
                'status'      => $status,
                'tren'        => $tren['tren'],
                'selisih'     => $tren['selisih'],
            ];
        }

        usort($opdList, function ($a, $b) {
            return strcmp($a['nama_opd'], $b['nama_opd']);
        });

        return $this->render('tabel', [
            'activeMenu' => 'tabel',
            'opdList'    => $opdList
        ]);
    }

    public function index()
    {
        return $this->grafik();
    }

    public function peta()
    {
        $hubId   = self::HUB_OPD_ID;
        $opdList = [];

        foreach ($this->opdModel->findAll() as $opd) {
            if (!$opd['latitude'] || !$opd['longitude']) continue;

            $indexData  = $this->indexModel->getLatestByOpd($opd['id_opd']);
            $nilaiIndex = $indexData['nilai_index'] ?? null;
            $status     = $this->indexModel->getStatusWarna($nilaiIndex);

            $opdList[] = [
                'id_opd'      => $opd['id_opd'],
                'nama_opd'    => $opd['nama_opd'],
                'latitude'    => $opd['latitude'],
                'longitude'   => $opd['longitude'],
                'nilai_index' => $nilaiIndex,
                'status'      => $status,
            ];
        }

        return $this->render('peta', [
            'activeMenu'   => 'peta',
            'opdList'      => $opdList,
            'jaringanList' => $this->buildJaringanListFromIndex($hubId)
        ]);
    }

    public function detail($idOpd)
    {
        $opd    = $this->opdModel->find($idOpd);
        $barang = $this->opdBarangModel->getByOpd($idOpd);

        return $this->response->setJSON([
            'opd'    => $opd,
            'barang' => $barang,
        ]);
    }

    public function historiIndex($idOpd)
    {
        $db = \Config\Database::connect();

        $aktif = $this->indexModel->getLatestByOpd((int) $idOpd);

        $histori = $db->table('histori_index')
            ->where('id_opd', $idOpd)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'aktif'   => $aktif,
            'histori' => $histori,
        ]);
    }
}