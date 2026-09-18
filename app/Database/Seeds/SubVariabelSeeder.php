<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubVariabelSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('master_sub_variabel')->emptyTable();
        $this->db->enableForeignKeyChecks();

        $jenisBarang = $this->db->table('barang')->get()->getResultArray();
        $jenisMap    = [];
        foreach ($jenisBarang as $b) {
            $jenisMap[strtolower(trim($b['jenis_barang']))] = (int)$b['id_barang'];
        }

        if (empty($jenisMap)) {
            echo "SubVariabelSeeder: GAGAL — tabel barang kosong.\n";
            return;
        }

        // Helper pencari id jenis barang fleksibel
        $findId = function (...$keys) use ($jenisMap) {
            foreach ($keys as $k) {
                foreach ($jenisMap as $name => $id) {
                    if (str_contains($name, strtolower($k))) return $id;
                }
            }
            return 1;
        };

        $idAp     = $findId('access point', 'ap');
        $idSwitch = $findId('switch');
        $idRouter = $findId('router');
        $idUps    = $findId('ups');
        $idKabel  = $findId('kabel');

        $now = date('Y-m-d H:i:s');
        $data = [
            // --- SUB VARIABEL UNTUK 'Fungsi' (Kondisi Fisik) (ID: 54) ---
            // Bobot: Switch 30%, AP 30%, Router 30%, UPS 10%
            [
                'id_variabel'       => 54,
                'nama_sub_variabel' => 'Fungsi Switch',
                'jenis_barang_id'   => $idSwitch,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'kondisi_skor',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.30,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 54,
                'nama_sub_variabel' => 'Fungsi Access Point',
                'jenis_barang_id'   => $idAp,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'kondisi_skor',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.30,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 54,
                'nama_sub_variabel' => 'Fungsi Router',
                'jenis_barang_id'   => $idRouter,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'kondisi_skor',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.30,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 54,
                'nama_sub_variabel' => 'Fungsi UPS',
                'jenis_barang_id'   => $idUps,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'kondisi_skor',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.10,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],

            // --- SUB VARIABEL UNTUK 'Umur Perangkat' (ID: 52) ---
            // Masing-masing jenis barang bobot 20%
            [
                'id_variabel'       => 52,
                'nama_sub_variabel' => 'Umur Access Point',
                'jenis_barang_id'   => $idAp,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'tahun',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 52,
                'nama_sub_variabel' => 'Umur Switch',
                'jenis_barang_id'   => $idSwitch,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'tahun',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 52,
                'nama_sub_variabel' => 'Umur Router',
                'jenis_barang_id'   => $idRouter,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'tahun',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 52,
                'nama_sub_variabel' => 'Umur UPS',
                'jenis_barang_id'   => $idUps,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'tahun',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 52,
                'nama_sub_variabel' => 'Umur Kabel',
                'jenis_barang_id'   => $idKabel,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'tahun',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],

            // --- SUB VARIABEL UNTUK 'Grade Perangkat' (ID: 53) ---
            [
                'id_variabel'       => 53,
                'nama_sub_variabel' => 'Grade Access Point',
                'jenis_barang_id'   => $idAp,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'grade_perangkat',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 53,
                'nama_sub_variabel' => 'Grade Switch',
                'jenis_barang_id'   => $idSwitch,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'grade_perangkat',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 53,
                'nama_sub_variabel' => 'Grade Router',
                'jenis_barang_id'   => $idRouter,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'grade_perangkat',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 53,
                'nama_sub_variabel' => 'Grade UPS',
                'jenis_barang_id'   => $idUps,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'grade_perangkat',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'id_variabel'       => 53,
                'nama_sub_variabel' => 'Grade Kabel',
                'jenis_barang_id'   => $idKabel,
                'sumber_tipe'       => 'KEPEMILIKAN',
                'field_source'      => 'grade_perangkat',
                'formula'           => null,
                'nilai_min'         => 1.00,
                'nilai_max'         => 5.00,
                'arah'              => 'MAX',
                'bobot'             => 0.20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        $this->db->table('master_sub_variabel')->insertBatch($data);
        echo "SubVariabelSeeder: Berhasil memasukkan " . count($data) . " sub-variabel.\n";
    }
}