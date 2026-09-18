<?php

namespace Modules\Dashboard\Models;

use CodeIgniter\Model;

class OpdBarang extends Model
{
    protected $table         = 'opd_barang';
    protected $primaryKey    = 'id_opd_barang';

    protected $allowedFields = [
        'id_opd',
        'id_barang',
        'jumlah',
        'jumlah_port',
        'merk',
        'tahun',
        'kondisi',
        'foto_bukti',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    public function getByOpd(int $idOpd): array
    {
        return $this->select('opd_barang.*, barang.jenis_barang')
            ->join('barang', 'barang.id_barang = opd_barang.id_barang')
            ->where('opd_barang.id_opd', $idOpd)
            ->findAll();
    }
}