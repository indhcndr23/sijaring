<?php

namespace Modules\Input\Models;

use CodeIgniter\Model;

class JaringanModel extends Model
{
    protected $table         = 'jaringan';
    protected $primaryKey    = 'id_jaringan';
    protected $allowedFields = ['id_opd_asal', 'id_opd_tujuan', 'jenis_kabel', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // OPD asal selalu Diskominfo
    const ID_OPD_DISKOMINFO = 13;

    public function getAllWithOpd(): array
    {
        return $this->select('jaringan.*, opd.nama_opd as nama_opd_tujuan')
            ->join('opd', 'opd.id_opd = jaringan.id_opd_tujuan')
            ->orderBy('jaringan.id_jaringan', 'DESC')
            ->findAll();
    }

    /**
     * TAMBAHAN BARU: Auto-sync jenis kabel dari form OPD/Non-OPD ke tabel jaringan
     */
    public function syncFromOpd(int $idOpd, ?string $jenisKabel)
    {
        if (empty($idOpd) || empty($jenisKabel) || $jenisKabel === '-') return;

        $idHub = self::ID_OPD_DISKOMINFO; // 13 (Diskominfo)

        // Cek apakah relasi dari Diskominfo ke OPD ini sudah ada
        $existing = $this->where('id_opd_asal', $idHub)
                         ->where('id_opd_tujuan', $idOpd)
                         ->first();

        $data = [
            'id_opd_asal'   => $idHub,
            'id_opd_tujuan' => $idOpd,
            'jenis_kabel'   => $jenisKabel,
            'keterangan'    => 'Sync otomatis dari data profil OPD/Non-OPD',
        ];

        if ($existing) {
            // Jika sudah ada, update jenis kabelnya
            $this->update($existing['id_jaringan'], $data);
        } else {
            // Jika belum ada, buat baru
            $this->insert($data);
        }
    }
}