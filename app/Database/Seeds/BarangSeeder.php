<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_barang' => 1, 'jenis_barang' => 'Access point', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id_barang' => 2, 'jenis_barang' => 'Kabel', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id_barang' => 3, 'jenis_barang' => 'Penangkal petir', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id_barang' => 4, 'jenis_barang' => 'Router', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id_barang' => 5, 'jenis_barang' => 'Switch', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id_barang' => 6, 'jenis_barang' => 'UPS', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id_barang' => 7, 'jenis_barang' => 'Wallmount', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('barang')->ignore(true)->insertBatch($data);
    }
}