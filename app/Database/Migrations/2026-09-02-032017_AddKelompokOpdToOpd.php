<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelompokOpdToOpd extends Migration
{
    public function up()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();

        // 1. Buat Tabel Ref 'kelompok_opd' jika belum ada
        if (!$db->tableExists('kelompok_opd')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('kelompok_opd', true);

            // Insert Data Master Default
            $db->table('kelompok_opd')->insertBatch([
                ['id' => 1, 'nama' => 'OPD', 'created_at' => date('Y-m-d H:i:s')],
                ['id' => 2, 'nama' => 'Non-OPD', 'created_at' => date('Y-m-d H:i:s')],
            ]);
        }

        // 2. Tambahkan Kolom 'kelompok_opd_id' ke Tabel 'opd'
        if (!$db->fieldExists('kelompok_opd_id', 'opd')) {
            $fields = [
                'kelompok_opd_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1, // Default 1 = OPD
                    'after'      => 'id_opd'
                ],
            ];
            $this->forge->addColumn('opd', $fields);
        }

        // 3. Pasang Foreign Key Relasi
        try {
            $db->query('ALTER TABLE `opd` ADD CONSTRAINT `fk_opd_kelompok` FOREIGN KEY (`kelompok_opd_id`) REFERENCES `kelompok_opd`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;');
        } catch (\Throwable $e) {
            // Abaikan jika FK sudah ada
        }
    }

    public function down()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();

        // Hapus FK
        try {
            $this->forge->dropForeignKey('opd', 'fk_opd_kelompok');
        } catch (\Throwable $e) {
            // Abaikan jika FK tidak ada
        }

        if ($db->fieldExists('kelompok_opd_id', 'opd')) {
            $this->forge->dropColumn('opd', 'kelompok_opd_id');
        }

        $this->forge->dropTable('kelompok_opd', true);
    }
}