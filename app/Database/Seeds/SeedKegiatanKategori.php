<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeedKegiatanKategori extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'bidang_id' => 1,
                'nama_kegiatan_kategori' => 'Aplikasi',
                'slug_kegiatan_kategori' => 'aplikasi',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'bidang_id' => 2,
                'nama_kegiatan_kategori' => 'Insiden Siber',
                'slug_kegiatan_kategori' => 'insiden-siber',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'bidang_id' => 2,
                'nama_kegiatan_kategori' => 'Permintaan Pentest / VA',
                'slug_kegiatan_kategori' => 'permintaan-pentest-va',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'bidang_id' => 1,
                'nama_kegiatan_kategori' => 'Perawatan Jaringan',
                'slug_kegiatan_kategori' => 'perawatan-jaringan',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'bidang_id' => 1,
                'nama_kegiatan_kategori' => 'Fasilitas Rapat',
                'slug_kegiatan_kategori' => 'fasilitas-rapat',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 6,
                'bidang_id' => 1,
                'nama_kegiatan_kategori' => 'Permintaan Operator',
                'slug_kegiatan_kategori' => 'permintaan-operator',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 7,
                'bidang_id' => 1,
                'nama_kegiatan_kategori' => 'Permintaan Zoom',
                'slug_kegiatan_kategori' => 'permintaan-zoom',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 8,
                'bidang_id' => 4,
                'nama_kegiatan_kategori' => 'Permintaan Narasumber / Juri',
                'slug_kegiatan_kategori' => 'permintaan-narasumber-juri',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 9,
                'bidang_id' => 2,
                'nama_kegiatan_kategori' => 'Permintaan Data',
                'slug_kegiatan_kategori' => 'permintaan-data',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 10,
                'bidang_id' => 3,
                'nama_kegiatan_kategori' => 'Permintaan Liputan',
                'slug_kegiatan_kategori' => 'permintaan-liputan',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 11,
                'bidang_id' => 3,
                'nama_kegiatan_kategori' => 'Permintaan Desain Media',
                'slug_kegiatan_kategori' => 'permintaan-desain-media',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 12,
                'bidang_id' => 3,
                'nama_kegiatan_kategori' => 'Permintaan MC',
                'slug_kegiatan_kategori' => 'permintaan-mc',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 13,
                'bidang_id' => 4,
                'nama_kegiatan_kategori' => 'Tugas Internal Lainnya',
                'slug_kegiatan_kategori' => 'tugas-internal-lainnya',
                'catatan' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        $this->db->table('kegiatan_kategori')->insertBatch($data);
    }
}
