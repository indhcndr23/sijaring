<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelompokOpdSeeder extends Seeder
{
    public function run()
    {
        // 1. Isi Data Master Ref 'kelompok_opd' jika belum ada
        $builderKelompok = $this->db->table('kelompok_opd');
        if ($builderKelompok->countAllResults() === 0) {
            $data = [
                ['id' => 1, 'nama' => 'OPD', 'created_at' => date('Y-m-d H:i:s')],
                ['id' => 2, 'nama' => 'Non-OPD', 'created_at' => date('Y-m-d H:i:s')],
            ];
            $builderKelompok->insertBatch($data);
        }

        // 2. Set semua data OPD lama yang 'NULL' atau '0' menjadi '1' (OPD Resmi)
        $this->db->table('opd')
            ->where('kelompok_opd_id IS NULL')
            ->orWhere('kelompok_opd_id', 0)
            ->update(['kelompok_opd_id' => 1]);
    }
}