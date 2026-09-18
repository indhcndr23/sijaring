<?php

namespace Modules\Input\Controllers;

use App\Controllers\BaseController;
use Modules\Input\Models\BarangModel;
use Modules\Input\Models\OpdBarangModel;
use Modules\Kalkulasi\Services\IndexEngine;

class InputController extends BaseController
{
    protected string $feature = 'Input Data Inventaris Jaringan';
    protected BarangModel $barangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
    }

    // ================== 1. MASTER BARANG ==================
    public function barang()
    {
        return $this->render('Barang');
    }

    public function barangData()
    {
        try {
            $rows = $this->barangModel->orderBy('jenis_barang', 'ASC')->findAll();
            $data = array_map(function ($row) {
                return [
                    'id'    => $row['id_barang'],
                    'jenis' => $row['jenis_barang'],
                ];
            }, $rows);
            return $this->response->setJSON(['data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function barangStore()
    {
        $id    = $this->request->getPost('id');
        $jenis = trim($this->request->getPost('jenis') ?? '');

        if (empty($jenis)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama Jenis Barang wajib diisi!']);
        }

        $payload = ['jenis_barang' => $jenis];

        if (!empty($id)) {
            $this->barangModel->update($id, $payload);
        } else {
            $this->barangModel->insert($payload);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Jenis barang berhasil disimpan']);
    }

    public function barangHapus($id)
    {
        $barang = $this->barangModel->find($id);
        if ($barang) {
            $opdBarangModel = new OpdBarangModel();
            $opdBarangModel->where('id_barang', $id)->delete();
            $this->barangModel->delete($id);
        }
        return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil dihapus']);
    }

    // ================== 2. KEPEMILIKAN ==================
    public function kepemilikan()
    {
        $db = \Config\Database::connect();
        $opdList    = $db->table('opd')->orderBy('nama_opd', 'ASC')->get()->getResultArray();
        $barangList = $db->table('barang')->orderBy('jenis_barang', 'ASC')->get()->getResultArray();

        return $this->render('Kepemilikan', [
            'opdList'    => $opdList ?? [],
            'barangList' => $barangList ?? []
        ]);
    }

    public function kepemilikanData()
    {
        $db = \Config\Database::connect();
        try {
            $id_opd = $this->request->getGet('id_opd');
            if (empty($id_opd)) return $this->response->setJSON(['data' => []]);

            $rows = $db->table('opd_barang ob')
                ->select('ob.*, b.jenis_barang, o.nama_opd')
                ->join('barang b', 'b.id_barang = ob.id_barang')
                ->join('opd o', 'o.id_opd = ob.id_opd')
                ->where('ob.id_opd', $id_opd)
                ->orderBy('ob.id_opd_barang', 'DESC')
                ->get()->getResultArray();

            $data = array_map(function ($row) {
                return [
                    'id'          => $row['id_opd_barang'],
                    'id_opd'      => $row['id_opd'],
                    'id_barang'   => $row['id_barang'],
                    'nama_opd'    => $row['nama_opd'],
                    'jenis'       => $row['jenis_barang'],
                    'jumlah'      => (int)($row['jumlah'] ?? 1),
                    'jumlah_port' => $row['jumlah_port'] !== null ? (int)$row['jumlah_port'] : null,
                    'merk'        => $row['merk'] ?? '-',
                    'tahun'       => $row['tahun'] ?? '-',
                    'kondisi'     => $row['kondisi'] ?? '-',
                    'keterangan'  => $row['keterangan'] ?? '-',
                    'foto_bukti'  => $row['foto_bukti'] ?? null,
                ];
            }, $rows);

            return $this->response->setJSON(['data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function kepemilikanStore()
    {
        $id          = $this->request->getPost('id');
        $id_opd      = $this->request->getPost('id_opd');
        $id_barang   = $this->request->getPost('id_barang');
        $jumlah      = $this->request->getPost('jumlah') ?: 1;
        $jumlah_port = $this->request->getPost('jumlah_port');
        $merk        = $this->request->getPost('merk');
        $tahun       = $this->request->getPost('tahun');
        $kondisi     = $this->request->getPost('kondisi');
        $keterangan  = $this->request->getPost('keterangan');

        if (empty($id_opd) || empty($id_barang) || empty($merk) || empty($tahun) || empty($kondisi) || empty($keterangan)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Semua bidang wajib diisi!']);
        }

        $fileFoto = $this->request->getFile('foto_bukti');
        $namaFoto = null;

        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $targetDir = FCPATH . 'uploads/kepemilikan';
            $fileFoto->move($targetDir, $namaFoto);

            try {
                $imagePath = $targetDir . '/' . $namaFoto;
                \Config\Services::image()
                    ->withFile($imagePath)
                    ->resize(1200, 1200, true, 'height')
                    ->save($imagePath, 70);
            } catch (\Exception $e) {
            }
        }

        $opdBarangModel = new OpdBarangModel();
        $payload = [
            'id_opd'      => $id_opd,
            'id_barang'   => $id_barang,
            'jumlah'      => (int)$jumlah,
            'jumlah_port' => ($jumlah_port !== null && $jumlah_port !== '') ? (int)$jumlah_port : null,
            'merk'        => $merk,
            'tahun'       => $tahun,
            'kondisi'     => $kondisi,
            'keterangan'  => $keterangan,
        ];

        if (!empty($id)) {
            if ($namaFoto) {
                $lama = $opdBarangModel->find($id);
                if (!empty($lama['foto_bukti']) && file_exists(FCPATH . 'uploads/kepemilikan/' . $lama['foto_bukti'])) {
                    @unlink(FCPATH . 'uploads/kepemilikan/' . $lama['foto_bukti']);
                }
                $payload['foto_bukti'] = $namaFoto;
            }
            $opdBarangModel->update($id, $payload);
        } else {
            if (!$namaFoto && empty($id)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Foto bukti fisik wajib diunggah!']);
            }
            $payload['foto_bukti'] = $namaFoto;
            $opdBarangModel->insert($payload);
        }

        register_shutdown_function(fn() => IndexEngine::recalculate((int)$id_opd));

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data kepemilikan inventaris berhasil disimpan']);
    }

    public function kepemilikanHapus($id)
    {
        $opdBarangModel = new OpdBarangModel();
        $row = $opdBarangModel->find($id);
        if ($row) {
            if (!empty($row['foto_bukti']) && file_exists(FCPATH . 'uploads/kepemilikan/' . $row['foto_bukti'])) {
                @unlink(FCPATH . 'uploads/kepemilikan/' . $row['foto_bukti']);
            }
            $opdBarangModel->delete($id);
        }

        if ($row) {
            register_shutdown_function(fn() => IndexEngine::recalculate((int)$row['id_opd']));
        }
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data kepemilikan dihapus']);
    }

    // ================== 3. INDEX PENILAIAN ==================
    public function index()
    {
        $db = \Config\Database::connect();
        $dimensiList = $db->table('master_dimensi')->orderBy('id_dimensi', 'ASC')->get()->getResultArray();
        return $this->render('Index', ['dimensiList' => $dimensiList]);
    }

    public function dimensiList()
    {
        $db = \Config\Database::connect();
        $dimensi = $db->table('master_dimensi')->orderBy('id_dimensi', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($dimensi);
    }

    public function indexData()
    {
        $db = \Config\Database::connect();
        try {
            $dimensi = $db->table('master_dimensi')->orderBy('id_dimensi', 'ASC')->get()->getResultArray();
            $variabel = $db->table('master_variabel mv')
                ->select('mv.*, md.nama_dimensi')
                ->join('master_dimensi md', 'md.id_dimensi = mv.id_dimensi')
                ->orderBy('mv.id_dimensi', 'ASC')
                ->orderBy('mv.id_variabel', 'ASC')
                ->get()->getResultArray();

            return $this->response->setJSON(['dimensi' => $dimensi, 'variabel' => $variabel]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function dimensiDetail($id)
    {
        $db = \Config\Database::connect();
        $dimensi = $db->table('master_dimensi')->where('id_dimensi', $id)->get()->getRowArray();
        if ($dimensi) {
            $variabel = $db->table('master_variabel')->where('id_dimensi', $id)->get()->getResultArray();
            $dimensi['variabel'] = $variabel;
        }
        return $this->response->setJSON($dimensi);
    }

    public function dimensiStore()
    {
        $db = \Config\Database::connect();
        $idDimensi    = $this->request->getPost('id_dimensi');
        $namaDimensi  = $this->request->getPost('nama_dimensi');
        $bobotDimensi = $this->request->getPost('bobot');

        $db->table('master_dimensi')->where('id_dimensi', $idDimensi)->update([
            'nama_dimensi' => $namaDimensi,
            'bobot'        => $bobotDimensi,
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        $variabelIds   = $this->request->getPost('variabel_id');
        $variabelNamas = $this->request->getPost('variabel_nama');
        $variabelBobots = $this->request->getPost('variabel_bobot');

        if (!empty($variabelIds) && is_array($variabelIds)) {
            foreach ($variabelIds as $index => $varId) {
                $db->table('master_variabel')->where('id_variabel', $varId)->update([
                    'nama_variabel' => $variabelNamas[$index] ?? '',
                    'bobot'         => $variabelBobots[$index] ?? 0,
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Dimensi berhasil diperbarui!']);
    }

    public function variabelDetail($id)
    {
        $db = \Config\Database::connect();
        $data = $db->table('master_variabel')->where('id_variabel', $id)->get()->getRowArray();
        return $this->response->setJSON($data);
    }

    public function variabelStore()
    {
        $db = \Config\Database::connect();
        $id_variabel   = $this->request->getPost('id_variabel');
        $id_dimensi    = $this->request->getPost('id_dimensi');
        $bobotBaru     = (float) $this->request->getPost('bobot');
        $sumber_tipe   = $this->request->getPost('sumber_tipe');

        $payload = [
            'id_dimensi'    => $id_dimensi,
            'nama_variabel' => $this->request->getPost('nama_variabel'),
            'sumber_tipe'   => $sumber_tipe,
            'field_source'  => $sumber_tipe !== 'KOMBINASI' ? $this->request->getPost('field_source') : null,
            'formula'       => $sumber_tipe === 'KOMBINASI' ? $this->request->getPost('formula') : null,
            'bobot'         => $bobotBaru,
            'nilai_min'     => $this->request->getPost('nilai_min'),
            'nilai_max'     => $this->request->getPost('nilai_max'),
            'arah'          => $this->request->getPost('arah'),
        ];

        if (!empty($id_variabel)) {
            $db->table('master_variabel')->where('id_variabel', $id_variabel)->update($payload);
        } else {
            $db->table('master_variabel')->insert($payload);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Variabel disimpan']);
    }

    // ================== 4. SUB-VARIABEL ==================
    public function subVariabelList($idVariabel)
    {
        $model = new \Modules\Input\Models\SubVariabelModel();
        return $this->response->setJSON([
            'data'        => $model->getByVariabel((int)$idVariabel),
            'total_bobot' => $model->totalBobot((int)$idVariabel),
        ]);
    }

    public function subVariabelDetail($id)
    {
        $model = new \Modules\Input\Models\SubVariabelModel();
        return $this->response->setJSON($model->find($id));
    }

    public function subVariabelStore()
    {
        $model = new \Modules\Input\Models\SubVariabelModel();

        $id           = $this->request->getPost('id_sub_variabel');
        $id_variabel  = $this->request->getPost('id_variabel');
        $sumber_tipe  = $this->request->getPost('sumber_tipe');

        $payload = [
            'id_variabel'       => $id_variabel,
            'nama_sub_variabel' => $this->request->getPost('nama_sub_variabel'),
            'jenis_barang_id'   => $this->request->getPost('jenis_barang_id') ?: null,
            'sumber_tipe'       => $sumber_tipe,
            'field_source'      => $sumber_tipe !== 'KOMBINASI' ? $this->request->getPost('field_source') : null,
            'formula'           => $sumber_tipe === 'KOMBINASI' ? $this->request->getPost('formula') : null,
            'nilai_min'         => $this->request->getPost('nilai_min'),
            'nilai_max'         => $this->request->getPost('nilai_max'),
            'arah'              => $this->request->getPost('arah'),
            'bobot'             => $this->request->getPost('bobot'),
        ];

        if (!empty($id)) {
            $model->update($id, $payload);
        } else {
            $model->insert($payload);
        }

        return $this->response->setJSON([
            'status'      => 'success',
            'message'     => 'Sub-variabel disimpan',
            'total_bobot' => $model->totalBobot((int)$id_variabel),
        ]);
    }

    public function subVariabelHapus($id)
    {
        $model = new \Modules\Input\Models\SubVariabelModel();
        $row   = $model->find($id);
        if ($row) $model->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Sub-variabel dihapus']);
    }
}
