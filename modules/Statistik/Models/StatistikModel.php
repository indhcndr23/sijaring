<?php

namespace Modules\Statistik\Models;

use CodeIgniter\Model;

class StatistikModel extends Model
{
    protected $table = 'opd_barang';

    // 1. Ringkasan Angka Utama (Top Cards)
    public function getSummary()
    {
        $currentYear = (int) date('Y');

        return [
            'total_opd'        => $this->db->table('opd')->countAllResults(),
            'total_aset'       => (int) ($this->db->table('opd_barang')->selectSum('jumlah')->get()->getRow()->jumlah ?? 0),
            'perlu_peremajaan' => $this->db->table('opd_barang')
                ->where("({$currentYear} - CAST(tahun AS SIGNED)) >=", 5)
                ->where('tahun IS NOT NULL')
                ->where('tahun >', 0)
                ->countAllResults(),
            'kondisi_rusak'    => $this->db->table('opd_barang')
                ->whereIn('kondisi', ['Rusak Berat', 'Rusak Ringan', 'Rusak'])
                ->countAllResults()
        ];
    }

    // 2. Total Perangkat per OPD (Bar Chart)
    public function getBarangPerOpd()
    {
        return $this->db->table('opd')
            ->select('opd.id_opd, opd.nama_opd, COALESCE(SUM(opd_barang.jumlah), 0) as total_barang')
            ->join('opd_barang', 'opd_barang.id_opd = opd.id_opd', 'left')
            ->groupBy('opd.id_opd, opd.nama_opd')
            ->orderBy('total_barang', 'DESC')
            ->limit(20)
            ->get()->getResultArray();
    }

    // 3. Sebaran Umur Perangkat (Doughnut Chart)
    public function getStatistikUmur()
    {
        $currentYear = (int) date('Y');
        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN ({$currentYear} - CAST(tahun AS SIGNED)) < 2 THEN COALESCE(jumlah, 1) ELSE 0 END), 0) as kurang_2_tahun,
                    COALESCE(SUM(CASE WHEN ({$currentYear} - CAST(tahun AS SIGNED)) BETWEEN 2 AND 5 THEN COALESCE(jumlah, 1) ELSE 0 END), 0) as '2_sampai_5_tahun',
                    COALESCE(SUM(CASE WHEN ({$currentYear} - CAST(tahun AS SIGNED)) > 5 THEN COALESCE(jumlah, 1) ELSE 0 END), 0) as lebih_5_tahun
                FROM opd_barang WHERE tahun IS NOT NULL AND CAST(tahun AS SIGNED) > 0";

        return $this->db->query($sql)->getRowArray() ?? [
            'kurang_2_tahun'   => 0,
            '2_sampai_5_tahun' => 0,
            'lebih_5_tahun'    => 0
        ];
    }

    // 4. Sebaran Merk Terbanyak
    public function getSebaranMerk()
    {
        return $this->db->table('opd_barang')
            ->select('merk, SUM(COALESCE(jumlah, 1)) as total_unit')
            ->where('merk IS NOT NULL')
            ->where('merk !=', '')
            ->groupBy('merk')
            ->orderBy('total_unit', 'DESC')
            ->limit(5)
            ->get()->getResultArray();
    }

    // 5. Daftar Perangkat Tua (> 5 Tahun)
    public function getPerangkatTua()
    {
        $currentYear = (int) date('Y');
        return $this->db->table('opd_barang ob')
            ->select('o.nama_opd, b.jenis_barang as nama_barang, ob.merk, ob.tahun, ob.kondisi, (' . $currentYear . ' - CAST(ob.tahun AS SIGNED)) as umur_tahun')
            ->join('opd o', 'o.id_opd = ob.id_opd')
            ->join('barang b', 'b.id_barang = ob.id_barang')
            ->where("({$currentYear} - CAST(ob.tahun AS SIGNED)) >=", 5)
            ->where('ob.tahun IS NOT NULL')
            ->where('ob.tahun >', 0)
            ->orderBy('umur_tahun', 'DESC')
            ->limit(10)
            ->get()->getResultArray();
    }

    // 6. Daftar Perangkat Rusak
    public function getPerangkatRusak()
    {
        return $this->db->table('opd_barang ob')
            ->select('o.nama_opd, b.jenis_barang as nama_barang, ob.merk, ob.kondisi')
            ->join('opd o', 'o.id_opd = ob.id_opd')
            ->join('barang b', 'b.id_barang = ob.id_barang')
            ->whereIn('ob.kondisi', ['Rusak Berat', 'Rusak Ringan', 'Rusak'])
            ->limit(10)
            ->get()->getResultArray();
    }

    // 7. Fasilitas Terburuk (Banyak Perangkat Rusak)
    public function getFasilitasTerburuk()
    {
        return $this->db->table('opd o')
            ->select('o.nama_opd, COUNT(ob.id_opd_barang) as total_rusak')
            ->join('opd_barang ob', 'ob.id_opd = o.id_opd AND ob.kondisi IN ("Rusak", "Rusak Berat", "Rusak Ringan")', 'inner')
            ->groupBy('o.id_opd, o.nama_opd')
            ->orderBy('total_rusak', 'DESC')
            ->limit(5)
            ->get()->getResultArray();
    }

// 8. Kapasitas Terburuk (Hanya untuk OPD yang SUDAH diinput asetnya)
    public function getKapasitasTerburuk()
    {
        $sql = "SELECT 
                    o.nama_opd,
                    o.jumlah_pegawai,
                    COALESCE(SUM(CASE WHEN LOWER(b.jenis_barang) LIKE '%switch%' THEN (COALESCE(ob.jumlah, 1) * COALESCE(ob.jumlah_port, 0)) ELSE 0 END), 0) as jumlah_port,
                    COALESCE(SUM(CASE WHEN LOWER(b.jenis_barang) LIKE '%access point%' OR LOWER(b.jenis_barang) LIKE '%ap%' THEN COALESCE(ob.jumlah, 1) ELSE 0 END), 0) as jumlah_access_point,
                    ROUND(
                        (
                            COALESCE(SUM(CASE WHEN LOWER(b.jenis_barang) LIKE '%switch%' THEN (COALESCE(ob.jumlah, 1) * COALESCE(ob.jumlah_port, 0)) ELSE 0 END), 0) + 
                            COALESCE(SUM(CASE WHEN LOWER(b.jenis_barang) LIKE '%access point%' OR LOWER(b.jenis_barang) LIKE '%ap%' THEN COALESCE(ob.jumlah, 1) ELSE 0 END), 0)
                        ) / NULLIF(o.jumlah_pegawai, 0), 2
                    ) as rasio_kapasitas
                FROM opd o
                INNER JOIN opd_barang ob ON ob.id_opd = o.id_opd
                INNER JOIN barang b ON b.id_barang = ob.id_barang
                INNER JOIN index_penilaian ip ON ip.id_opd = o.id_opd
                WHERE o.jumlah_pegawai > 0
                GROUP BY o.id_opd, o.nama_opd, o.jumlah_pegawai
                HAVING (jumlah_port + jumlah_access_point) > 0
                ORDER BY rasio_kapasitas ASC
                LIMIT 5";

        return $this->db->query($sql)->getResultArray();
    }

    // 9. Kinerja Terburuk (Banyak Perangkat Tua)
    public function getKinerjaTerburuk()
    {
        $currentYear = (int) date('Y');
        return $this->db->table('opd o')
            ->select('o.nama_opd, COUNT(ob.id_opd_barang) as total_perangkat_tua')
            ->join('opd_barang ob', "ob.id_opd = o.id_opd AND ({$currentYear} - CAST(ob.tahun AS SIGNED)) >= 5", 'inner')
            ->groupBy('o.id_opd, o.nama_opd')
            ->orderBy('total_perangkat_tua', 'DESC')
            ->limit(5)
            ->get()->getResultArray();
    }

// 10. Total Index Terburuk (Khusus Kategori Merah / Index < 50)
    public function getTotalIndexTerburuk()
    {
        $currentYear = (int) date('Y');
        $sql = "SELECT o.nama_opd,
                       COALESCE(ip.nilai_index, 0) as skor_index,
                       COUNT(CASE WHEN ob.kondisi IN ('Rusak', 'Rusak Berat', 'Rusak Ringan') THEN 1 END) as total_rusak,
                       COUNT(CASE WHEN ({$currentYear} - CAST(ob.tahun AS SIGNED)) >= 5 THEN 1 END) as total_tua
                FROM opd o
                INNER JOIN index_penilaian ip ON ip.id_opd = o.id_opd
                LEFT JOIN opd_barang ob ON ob.id_opd = o.id_opd
                WHERE ip.nilai_index < 50
                GROUP BY o.id_opd, o.nama_opd, ip.nilai_index
                ORDER BY skor_index ASC
                LIMIT 5";

        return $this->db->query($sql)->getResultArray();
    }
}