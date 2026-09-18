<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeedKelompokOpd extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'nama_kelompok_opd' => 'Badan',
                'slug' => 'badan',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'nama_kelompok_opd' => 'Dinas',
                'slug' => 'dinas',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'nama_kelompok_opd' => 'Kecamatan',
                'slug' => 'kecamatan',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'nama_kelompok_opd' => 'Setda',
                'slug' => 'setda',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'nama_kelompok_opd' => 'Setwan',
                'slug' => 'setwan',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        $this->db->table('kelompok_opd')->insertBatch($data);
    }
}
