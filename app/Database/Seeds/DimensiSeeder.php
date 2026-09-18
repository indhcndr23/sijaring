<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DimensiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_dimensi'   => 1,
                'nama_dimensi' => 'Kapasitas',
                'bobot'        => 30.00, 
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'id_dimensi'   => 2,
                'nama_dimensi' => 'Kualitas',
                'bobot'        => 30.00, // 40% (atau 0.40 jika skala 1)
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'id_dimensi'   => 3,
                'nama_dimensi' => 'Kondisi',
                'bobot'        => 40.00, // 25% (atau 0.25 jika skala 1)
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Mengisikan data ke tabel master_dimensi
        $this->db->table('master_dimensi')->upsertBatch($data);
    }
}
