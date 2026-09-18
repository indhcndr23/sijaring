<?php

namespace Modules\Input\Models;

use CodeIgniter\Model;

class SubVariabelModel extends Model
{
    protected $table         = 'master_sub_variabel';
    protected $primaryKey    = 'id_sub_variabel';
    protected $allowedFields = [
        'id_variabel',
        'nama_sub_variabel',
        'jenis_barang_id',
        'sumber_tipe',
        'field_source',
        'formula',
        'nilai_min',
        'nilai_max',
        'arah',
        'bobot',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    /**
     * Ambil semua sub-variabel milik satu Variabel, join nama jenis barang untuk ditampilkan di UI.
     */
    public function getByVariabel(int $idVariabel): array
    {
        return $this->select('master_sub_variabel.*, barang.jenis_barang')
            ->join('barang', 'barang.id_barang = master_sub_variabel.jenis_barang_id', 'left')
            ->where('id_variabel', $idVariabel)
            ->orderBy('id_sub_variabel', 'ASC')
            ->findAll();
    }

    /**
     * Total bobot sub-variabel dalam satu Variabel — dipakai untuk indikator
     * "Total bobot: X ✓/✗" di UI. $excludeId dipakai saat mode edit supaya
     * baris yang sedang diedit tidak dihitung dobel.
     */
    public function totalBobot(int $idVariabel, ?int $excludeId = null): float
    {
        $builder = $this->where('id_variabel', $idVariabel);
        if ($excludeId) {
            $builder->where('id_sub_variabel !=', $excludeId);
        }
        $rows = $builder->findAll();
        return (float) array_sum(array_column($rows, 'bobot'));
    }
}