<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VariabelSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('master_variabel')->emptyTable();
        $this->db->enableForeignKeyChecks();

        $dimensi = $this->db->table('master_dimensi')->get()->getResultArray();
        $dimMap  = array_column($dimensi, 'id_dimensi', 'nama_dimensi');

        if (empty($dimMap)) {
            echo "VariabelSeeder: GAGAL — master_dimensi kosong. Jalankan DimensiSeeder dulu.\n";
            return;
        }

        $idKapasitas = $dimMap['Kapasitas'] ?? 1;
        $idKualitas  = $dimMap['Kualitas']  ?? 2;
        $idKondisi   = $dimMap['Kondisi']   ?? 3;

        $now = date('Y-m-d H:i:s');

        $data = [
            // ================== DIMENSI 1: KAPASITAS (Total Bobot = 1.00) ==================
            [
                'id_variabel'   => 45,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Rasio Pengguna AP',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '({OPD.jumlah_pegawai} * 2 + {OPD.rata_tamu}) / {KEPEMILIKAN.ap.jumlah}',
                'bobot'         => 0.25,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 46,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Rasio Pengguna Switch',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '{KEPEMILIKAN.switch.total_port} / {OPD.jumlah_pegawai}',
                'bobot'         => 0.20,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 47,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Rasio AP Setiap Ruangan',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '{KEPEMILIKAN.ap.jumlah} / {OPD.jumlah_ruangan}',
                'bobot'         => 0.15,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 48,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Rasio AP Berdasarkan Luas Ruangan',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '{OPD.luas_ruangan} / {KEPEMILIKAN.ap.jumlah}',
                'bobot'         => 0.10,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 49,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Rasio Router',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '{KEPEMILIKAN.router.jumlah} / 1',
                'bobot'         => 0.10,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 50,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Jumlah Switch Diluar Wallmount',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '1',
                'bobot'         => 0.05,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 51,
                'id_dimensi'    => $idKapasitas,
                'nama_variabel' => 'Rasio UPS',
                'sumber_tipe'   => 'KOMBINASI',
                'field_source'  => null,
                'formula'       => '1 / {KEPEMILIKAN.ups.jumlah}',
                'bobot'         => 0.10,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // ================== DIMENSI 2: KUALITAS (Total Bobot = 1.00) ==================
            [
                'id_variabel'   => 52,
                'id_dimensi'    => $idKualitas,
                'nama_variabel' => 'Umur Perangkat',
                'sumber_tipe'   => 'KEPEMILIKAN',
                'field_source'  => 'tahun',
                'formula'       => null,
                'bobot'         => 0.50,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_variabel'   => 53,
                'id_dimensi'    => $idKualitas,
                'nama_variabel' => 'Grade Perangkat',
                'sumber_tipe'   => 'KEPEMILIKAN',
                'field_source'  => 'grade_perangkat',
                'formula'       => null,
                'bobot'         => 0.50,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // ================== DIMENSI 3: KONDISI (Total Bobot = 1.00) ==================
            [
                'id_variabel'   => 54,
                'id_dimensi'    => $idKondisi,
                'nama_variabel' => 'Fungsi',
                'sumber_tipe'   => 'KEPEMILIKAN',
                'field_source'  => 'kondisi_skor',
                'formula'       => null,
                'bobot'         => 1.00,
                'nilai_min'     => 1.00,
                'nilai_max'     => 5.00,
                'arah'          => 'MAX',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('master_variabel')->insertBatch($data);
        echo "VariabelSeeder: Berhasil memasukkan " . count($data) . " variabel.\n";
    }
}