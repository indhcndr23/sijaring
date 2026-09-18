<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeedRoleGroup extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'nama_role_group' => 'Sekretaris',
                'slug_role_group' => 'sekretaris',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'nama_role_group' => 'Kepala Bidang',
                'slug_role_group' => 'kepala-bidang',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'nama_role_group' => 'Staff',
                'slug_role_group' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'nama_role_group' => 'OPD',
                'slug_role_group' => 'opd',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        $this->db->table('role_group')->insertBatch($data);
    }
}
