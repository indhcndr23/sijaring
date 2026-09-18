<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeedBidang extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'nama_bidang' => 'Informatika',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'nama_bidang' => 'Statistik dan Persandian',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'nama_bidang' => 'Informasi Komunikasi Publik',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'nama_bidang' => 'Sekretariat',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        $this->db->table('bidang')->insertBatch($data);
    }
}
