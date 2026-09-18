<?php

namespace Modules\Dashboard\Models;

use CodeIgniter\Model;

class IndexPenilaian extends Model
{
    protected $table         = 'index_penilaian';
    protected $primaryKey    = 'id_index';
    
    // Hapus jenis_kabel dari allowedFields
    protected $allowedFields = [
        'id_opd', 'nilai_index'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLatestByOpd(int $idOpd)
    {
        return $this->where('id_opd', $idOpd)
            ->orderBy('id_index', 'DESC')
            ->first();
    }

    public function getStatusWarna(?float $nilai): string
    {
        if ($nilai === null) {
            return 'abu';
        }

        if ($nilai >= 80) {
            return 'hijau';
        } elseif ($nilai >= 50) {
            return 'kuning';
        }

        return 'merah';
    }
}