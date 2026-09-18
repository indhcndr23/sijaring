<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeedRole extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'role_group_id' => 1,
                'nama_role' => 'Sekretaris',
                'slug_role' => 'sekretaris',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'role_group_id' => 2,
                'nama_role' => 'Kepala Bidang',
                'slug_role' => 'kepala-bidang',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'role_group_id' => 3,
                'nama_role' => 'Kasir',
                'slug_role' => 'kasir',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'role_group_id' => 3,
                'nama_role' => 'Staff',
                'slug_role' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'role_group_id' => 4,
                'nama_role' => 'Admin OPD',
                'slug_role' => 'admin-opd',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        $this->db->table('role')->insertBatch($data);
    }
}
