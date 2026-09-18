<?php

namespace Modules\Input\Models;

use CodeIgniter\Model;

class IndexModel extends Model
{
    protected $table         = 'index_penilaian';
    protected $primaryKey    = 'id_index';
    
    // Hapus jenis_kabel dari allowedFields
    protected $allowedFields = ['id_opd', 'nilai_index'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAllWithOpd(): array
    {
        return $this->select('index_penilaian.*, opd.nama_opd')
            ->join('opd', 'opd.id_opd = index_penilaian.id_opd')
            ->orderBy('index_penilaian.id_index', 'DESC')
            ->findAll();
    }

    public function getLatestByOpd(int $idOpd): ?array
    {
        return $this->where('id_opd', $idOpd)
            ->orderBy('id_index', 'DESC')
            ->first();
    }

    public function simpanKeHistori(int $idIndex): void
    {
        $lama = $this->find($idIndex);
        if (!$lama) return;

        $db = \Config\Database::connect();
        
        // Hapus 'jenis_kabel' => $lama['jenis_kabel'] pada proses insert
        $db->table('histori_index')->insert([
            'id_opd'      => $lama['id_opd'],
            'nilai_index' => $lama['nilai_index'],
            'created_at'  => $lama['updated_at'] ?? $lama['created_at'],
        ]);
    }

    public function getHistoriByOpd(int $idOpd): array
    {
        $db = \Config\Database::connect();
        return $db->table('histori_index')
            ->where('id_opd', $idOpd)
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();
    }

    public function getStatusWarna(?int $nilai): string
    {
        if ($nilai === null) return 'abu';
        if ($nilai >= 8)    return 'hijau';
        if ($nilai >= 5)    return 'kuning';
        return 'merah';
    }
}